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
 * R1 read repository for structures and memberships.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class RegistryRepository
{
    /**
     * @param list<int> $structure_ids
     * @return list<array<string,mixed>>
     */
    public function structures(Db $zdb, array $structure_ids): array
    {
        if ($structure_ids === []) {
            return [];
        }

        try {
            $select = $zdb->select('eeds_structures');
            $select->where(['id_structure' => $structure_ids]);
            $select->order('structure_name ASC');
            $rows = [];
            foreach ($zdb->execute($select) as $row) {
                $rows[] = (array)$row;
            }
            return $rows;
        } catch (Throwable $e) {
            Logs::exception($e, 'Unable to list EEDS structures', Analog::WARNING);
            throw $e;
        }
    }

    /**
     * @param list<int> $structure_ids
     * @return list<array<string,mixed>>
     */
    public function affiliations(Db $zdb, array $structure_ids): array
    {
        if ($structure_ids === []) {
            return [];
        }

        try {
            $select = $zdb->select('eeds_member_affiliations');
            $select->where(['id_structure' => $structure_ids]);
            $select->order('effective_start DESC');
            $rows = [];
            foreach ($zdb->execute($select) as $row) {
                $rows[] = (array)$row;
            }
            return $rows;
        } catch (Throwable $e) {
            Logs::exception($e, 'Unable to list EEDS member affiliations', Analog::WARNING);
            throw $e;
        }
    }
}
