<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Domain;

use Safe\DateTimeImmutable;
use Galette\Core\Db;
use Galette\Core\Logs;
use Analog\Analog;
use Throwable;

/**
 * Effective-dated R1 structure and unit affiliations.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class AffiliationService
{
    /**
     * Close an active affiliation and create a dated transfer.
     *
     * @param list<int> $allowed_structure_ids
     */
    public function transfer(
        Db $zdb,
        int $member_id,
        int $structure_id,
        array $allowed_structure_ids,
        ?string $branch,
        ?string $unit,
        ?string $subgroup,
        string $effective_date,
        int $actor_id,
        string $reason,
        ?string $evidence_reference = null
    ): bool {
        $effective = DateTimeImmutable::createFromFormat('Y-m-d', $effective_date);
        if ($effective->format('Y-m-d') !== $effective_date) {
            throw new \InvalidArgumentException('Invalid effective date.');
        }

        if (
            $allowed_structure_ids === []
            || !in_array($structure_id, $allowed_structure_ids, strict: true)
        ) {
            Analog::log(
                'EEDS transfer rejected: target structure is outside the authorized scope',
                Analog::INFO
            );
            return false;
        }

        try {
            $zdb->beginTransaction();

            $source = $zdb->select('eeds_member_affiliations');
            $source->columns(['id_structure'])->where([
                'id_adh' => $member_id,
                'affiliation_status' => 'active',
            ]);
            $source->where('effective_start <= ' . $zdb->platform->quoteValue($effective_date));
            $source->where(
                '(effective_end IS NULL OR effective_end >= '
                . $zdb->platform->quoteValue($effective_date)
                . ')'
            );

            $source_structure_ids = [];
            foreach ($zdb->execute($source) as $row) {
                $source_structure_ids[] = (int)$row->id_structure;
            }
            if (
                count($source_structure_ids) !== 1
                || !in_array($source_structure_ids[0], $allowed_structure_ids, strict: true)
            ) {
                $zdb->rollback();
                Analog::log(
                    'EEDS transfer rejected: exactly one active in-scope source affiliation is required',
                    Analog::INFO
                );
                return false;
            }

            $update = $zdb->update('eeds_member_affiliations');
            $update->set([
                'effective_end' => $effective_date,
                'affiliation_status' => 'transferred',
            ]);
            $update->where([
                'id_adh' => $member_id,
                'id_structure' => $source_structure_ids[0],
                'affiliation_status' => 'active',
            ]);
            $update->where('effective_start <= ' . $zdb->platform->quoteValue($effective_date));
            $update->where(
                '(effective_end IS NULL OR effective_end >= '
                . $zdb->platform->quoteValue($effective_date)
                . ')'
            );
            if ($zdb->execute($update)->getAffectedRows() !== 1) {
                $zdb->rollback();
                Analog::log(
                    'EEDS transfer rejected: source affiliation changed before the transfer',
                    Analog::INFO
                );
                return false;
            }

            $insert = $zdb->insert('eeds_member_affiliations');
            $insert->values([
                'id_adh' => $member_id,
                'id_structure' => $structure_id,
                'branch' => $branch,
                'unit' => $unit,
                'subgroup' => $subgroup,
                'affiliation_status' => 'active',
                'effective_start' => $effective_date,
                'effective_end' => null,
                'reason' => $reason,
                'evidence_reference' => $evidence_reference,
                'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                'created_by' => $actor_id,
            ]);
            $zdb->execute($insert);

            (new StructureGroupMapper())->synchronizeMember($zdb, $member_id);
            $zdb->commit();
            return true;
        } catch (Throwable $e) {
            if ($zdb->inTransaction()) {
                $zdb->rollback();
            }
            Logs::exception($e, 'Unable to transfer EEDS member affiliation', Analog::WARNING);
            throw $e;
        }
    }
}
