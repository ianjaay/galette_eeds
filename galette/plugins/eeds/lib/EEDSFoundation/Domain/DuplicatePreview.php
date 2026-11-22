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

/**
 * Read-only R1 duplicate candidate preview.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class DuplicatePreview
{
    /**
     * @return list<array{left_member_id:int,right_member_id:int,match_fields:list<string>,confidence:string}>
     */
    public function preview(Db $zdb): array
    {
        try {
            $select = $zdb->select('adherents');
            $select->columns([
                'id_adh',
                'nom_adh',
                'prenom_adh',
                'ddn_adh',
                'login_adh',
                'email_adh',
                'gsm_adh',
                'tel_adh',
                'num_adh',
            ]);

            $rows = [];
            foreach ($zdb->execute($select) as $row) {
                $rows[(int)$row->id_adh] = $row;
            }

            $candidates = [];
            $ids = array_keys($rows);
            for ($left = 0; $left < count($ids); $left++) {
                for ($right = $left + 1; $right < count($ids); $right++) {
                    $a = $rows[$ids[$left]];
                    $b = $rows[$ids[$right]];
                    $matches = [];

                    $national_a = trim((string)($a->num_adh ?? ''));
                    $national_b = trim((string)($b->num_adh ?? ''));
                    if ($national_a !== '' && $national_a === $national_b) {
                        $matches[] = 'national_number';
                    }

                    $email_a = mb_strtolower(trim((string)($a->email_adh ?? '')));
                    $email_b = mb_strtolower(trim((string)($b->email_adh ?? '')));
                    if ($email_a !== '' && $email_a === $email_b) {
                        $matches[] = 'email';
                    }

                    $login_a = mb_strtolower(trim((string)($a->login_adh ?? '')));
                    $login_b = mb_strtolower(trim((string)($b->login_adh ?? '')));
                    if ($login_a !== '' && $login_a === $login_b) {
                        $matches[] = 'login';
                    }

                    $name_a = mb_strtolower(trim((string)$a->prenom_adh . ' ' . (string)$a->nom_adh));
                    $name_b = mb_strtolower(trim((string)$b->prenom_adh . ' ' . (string)$b->nom_adh));
                    $birth_a = (string)($a->ddn_adh ?? '');
                    $birth_b = (string)($b->ddn_adh ?? '');
                    if ($name_a !== '' && $name_a === $name_b && $birth_a !== '' && $birth_a === $birth_b) {
                        $matches[] = 'name_birthdate';
                    }

                    if ($matches !== []) {
                        $confidence = in_array('national_number', $matches, true)
                            || in_array('name_birthdate', $matches, true)
                            ? 'high'
                            : 'medium';
                        $candidates[] = [
                            'left_member_id' => (int)$a->id_adh,
                            'right_member_id' => (int)$b->id_adh,
                            'match_fields' => $matches,
                            'confidence' => $confidence,
                        ];
                    }
                }
            }
            return $candidates;
        } catch (Throwable $e) {
            Logs::exception($e, 'Unable to preview EEDS duplicate members', Analog::WARNING);
            throw $e;
        }
    }
}
