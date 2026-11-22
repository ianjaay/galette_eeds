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
 * R1 configurable structure administration.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class StructureService
{
    public const array ALLOWED_TYPES = [
        'national',
        'region',
        'district',
        'groupe_local',
        'unite',
    ];

    /**
     * Create the unique national root, if absent.
     */
    public function ensureNationalRoot(
        Db $zdb,
        string $name,
        string $code,
        string $effective_start,
        ?int $actor_id
    ): int {
        $name = trim($name);
        $code = trim($code);
        if ($name === '' || $code === '') {
            throw new \InvalidArgumentException('National structure name and code are required.');
        }

        $effective = DateTimeImmutable::createFromFormat('Y-m-d', $effective_start);
        if ($effective->format('Y-m-d') !== $effective_start) {
            throw new \InvalidArgumentException('Invalid effective date.');
        }

        try {
            $zdb->beginTransaction();

            $existing = $zdb->select('eeds_structures');
            $existing->columns(['id_structure'])->where(['structure_type' => 'national']);
            $rows = $zdb->execute($existing);
            if ($rows->count() > 0) {
                $row = $rows->current();
                $zdb->commit();
                return (int)$row->id_structure;
            }

            $insert = $zdb->insert('eeds_structures');
            $insert->values([
                'parent_structure' => null,
                'structure_type' => 'national',
                'structure_code' => $code,
                'structure_name' => $name,
                'recognition_status' => 'recognized',
                'effective_start' => $effective_start,
                'effective_end' => null,
                'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                'created_by' => $actor_id,
            ]);
            $zdb->execute($insert);

            $select = $zdb->select('eeds_structures');
            $select->columns(['id_structure'])->where(['structure_code' => $code]);
            $row = $zdb->execute($select)->current();
            $zdb->commit();
            return (int)$row->id_structure;
        } catch (Throwable $e) {
            if ($zdb->inTransaction()) {
                $zdb->rollback();
            }
            Logs::exception($e, 'Unable to initialize the EEDS national root', Analog::WARNING);
            throw $e;
        }
    }

    /**
     * Create a recognized or pending structure.
     */
    public function create(
        Db $zdb,
        ?int $parent_structure,
        string $structure_type,
        string $structure_code,
        string $structure_name,
        string $recognition_status,
        string $effective_start,
        int $actor_id
    ): int {
        if (
            trim($structure_type) === ''
            || trim($structure_code) === ''
            || trim($structure_name) === ''
        ) {
            throw new \InvalidArgumentException('Structure type, code and name are required.');
        }

        $effective = DateTimeImmutable::createFromFormat('Y-m-d', $effective_start);
        if ($effective->format('Y-m-d') !== $effective_start) {
            throw new \InvalidArgumentException('Invalid effective date.');
        }
        if (!in_array(trim($structure_type), self::ALLOWED_TYPES, strict: true)) {
            throw new \InvalidArgumentException('Unknown EEDS structure type.');
        }
        if (trim($structure_type) === 'national') {
            throw new \InvalidArgumentException('The national root must be initialized separately.');
        }
        if ($parent_structure === null) {
            throw new \InvalidArgumentException('Only the national structure can have no parent.');
        }
        $structure_type = trim($structure_type);

        try {
            $zdb->beginTransaction();

            $parent = $zdb->select('eeds_structures');
            $parent->columns(['id_structure', 'structure_type'])->where(['id_structure' => $parent_structure]);
            $parent->where('effective_start <= ' . $zdb->platform->quoteValue($effective_start));
            $parent->where(
                '(effective_end IS NULL OR effective_end >= '
                . $zdb->platform->quoteValue($effective_start)
                . ')'
            );
            $parent_row = $zdb->execute($parent);
            if ($parent_row->count() !== 1) {
                $zdb->rollback();
                throw new \InvalidArgumentException(
                    'The parent structure does not exist or is not effective on the selected date.'
                );
            }
            $parent_type = (string)$parent_row->current()->structure_type;
            $allowed_parent = [
                'region' => 'national',
                'district' => 'region',
                'groupe_local' => 'district',
                'unite' => 'groupe_local',
            ];
            if (($allowed_parent[$structure_type] ?? null) !== $parent_type) {
                $zdb->rollback();
                throw new \InvalidArgumentException(
                    'The parent structure type is not valid for this EEDS structure level.'
                );
            }

            $insert = $zdb->insert('eeds_structures');
            $insert->values([
                'parent_structure' => $parent_structure,
                'structure_type' => trim($structure_type),
                'structure_code' => trim($structure_code),
                'structure_name' => trim($structure_name),
                'recognition_status' => $recognition_status,
                'effective_start' => $effective_start,
                'effective_end' => null,
                'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                'created_by' => $actor_id,
            ]);
            $zdb->execute($insert);

            $select = $zdb->select('eeds_structures');
            $select->columns(['id_structure'])->where(['structure_code' => trim($structure_code)]);
            $row = $zdb->execute($select)->current();
            $zdb->commit();
            return (int)$row->id_structure;
        } catch (Throwable $e) {
            if ($zdb->inTransaction()) {
                $zdb->rollback();
            }
            Logs::exception($e, 'Unable to create EEDS structure', Analog::WARNING);
            throw $e;
        }
    }
}
