<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Domain;

use Galette\Core\Db;
use Throwable;
use Galette\Core\Logs;
use Analog\Analog;
use Safe\DateTimeImmutable;

/**
 * Read-only R1 duplicate candidate preview.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class DuplicatePreview
{
    /**
     * @param list<int> $structure_ids
     * @return list<array{left_member_id:int,right_member_id:int,match_fields:list<string>,confidence:string}>
     */
    public function preview(Db $zdb, array $structure_ids): array
    {
        if ($structure_ids === []) {
            return [];
        }

        try {
            $select = $zdb->select('adherents', 'a');
            $select->columns([
                'a.id_adh',
                'a.nom_adh',
                'a.prenom_adh',
                'a.ddn_adh',
                'a.login_adh',
                'a.email_adh',
                'n.national_number',
            ]);
            $select->quantifier('DISTINCT');
            $select->join(
                name: ['aff' => PREFIX_DB . 'eeds_member_affiliations'],
                on: 'a.id_adh=aff.id_adh',
                columns: [],
                type: $select::JOIN_INNER
            )->join(
                name: ['n' => PREFIX_DB . 'eeds_member_numbers'],
                on: 'a.id_adh=n.id_adh',
                columns: [],
                type: $select::JOIN_LEFT
            );
            $select->where([
                'aff.id_structure' => $structure_ids,
                'aff.affiliation_status' => 'active',
            ]);
            $date = (new DateTimeImmutable())->format('Y-m-d');
            $select->where('aff.effective_start <= ' . $zdb->platform->quoteValue($date));
            $select->where(
                '(aff.effective_end IS NULL OR aff.effective_end >= '
                . $zdb->platform->quoteValue($date)
                . ')'
            );

            $rows = [];
            foreach ($zdb->execute($select) as $row) {
                $rows[(int)$row->id_adh] = $row;
            }

            $groups = [
                'national_number' => [],
                'email' => [],
                'login' => [],
                'name_birthdate' => [],
            ];
            foreach ($rows as $member_id => $row) {
                $national_number = trim((string)($row->national_number ?? ''));
                if ($national_number !== '') {
                    $groups['national_number'][$national_number][] = $member_id;
                }

                $email = mb_strtolower(trim((string)($row->email_adh ?? '')));
                if ($email !== '') {
                    $groups['email'][$email][] = $member_id;
                }

                $login = mb_strtolower(trim((string)($row->login_adh ?? '')));
                if ($login !== '') {
                    $groups['login'][$login][] = $member_id;
                }

                $name = mb_strtolower(trim((string)$row->prenom_adh . ' ' . (string)$row->nom_adh));
                $birth_date = (string)($row->ddn_adh ?? '');
                if ($name !== '' && $birth_date !== '') {
                    $groups['name_birthdate'][$name . "\0" . $birth_date][] = $member_id;
                }
            }

            $candidates_by_pair = [];
            foreach ($groups as $match_field => $values) {
                foreach ($values as $member_ids) {
                    sort($member_ids, SORT_NUMERIC);
                    for ($left = 0; $left < count($member_ids); $left++) {
                        for ($right = $left + 1; $right < count($member_ids); $right++) {
                            $left_id = $member_ids[$left];
                            $right_id = $member_ids[$right];
                            $key = $left_id . ':' . $right_id;
                            $candidates_by_pair[$key] ??= [
                                'left_member_id' => $left_id,
                                'right_member_id' => $right_id,
                                'match_fields' => [],
                            ];
                            $candidates_by_pair[$key]['match_fields'][] = $match_field;
                        }
                    }
                }
            }

            $candidates = array_values($candidates_by_pair);
            usort(
                $candidates,
                static fn(array $left, array $right): int => [
                    $left['left_member_id'],
                    $left['right_member_id'],
                ] <=> [
                    $right['left_member_id'],
                    $right['right_member_id'],
                ]
            );
            foreach ($candidates as &$candidate) {
                $candidate['confidence'] = in_array('national_number', $candidate['match_fields'], true)
                    || in_array('name_birthdate', $candidate['match_fields'], true)
                    ? 'high'
                    : 'medium';
            }
            unset($candidate);

            return $candidates;
        } catch (Throwable $e) {
            Logs::exception($e, 'Unable to preview EEDS duplicate members', Analog::WARNING);
            throw $e;
        }
    }
}
