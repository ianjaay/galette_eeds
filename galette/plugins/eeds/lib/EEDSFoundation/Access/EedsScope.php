<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Access;

use Galette\Core\Db;
use Safe\DateTimeImmutable;

/**
 * Minimal fail-closed EEDS organizational scope contract for R1.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class EedsScope
{
    /**
     * @param list<int> $structure_ids
     */
    public function __construct(private array $structure_ids)
    {
    }

    /**
     * Is a structure within this scope?
     */
    public function allowsStructure(int $structure_id): bool
    {
        return $structure_id > 0 && in_array($structure_id, $this->structure_ids, strict: true);
    }

    /**
     * Is a member currently affiliated with a structure within this scope?
     */
    public function allowsMember(Db $zdb, int $member_id, ?string $effective_date = null): bool
    {
        if ($member_id <= 0 || $this->structure_ids === []) {
            return false;
        }

        $date = $effective_date ?? (new DateTimeImmutable())->format('Y-m-d');
        $select = $zdb->select('eeds_member_affiliations');
        $select->columns(['id_structure'])->where([
            'id_adh' => $member_id,
            'affiliation_status' => 'active',
        ]);
        $select->where('effective_start <= ' . $zdb->platform->quoteValue($date));
        $select->where('(effective_end IS NULL OR effective_end >= ' . $zdb->platform->quoteValue($date) . ')');

        $has_affiliation = false;
        foreach ($zdb->execute($select) as $row) {
            $has_affiliation = true;
            if (!$this->allowsStructure((int)$row->id_structure)) {
                return false;
            }
        }

        return $has_affiliation;
    }

    /**
     * @param list<int> $structure_ids
     * @return list<int>
     */
    public function filterStructureIds(array $structure_ids): array
    {
        return array_values(
            array_unique(
                array_values(
                    array_filter(
                        $structure_ids,
                        fn(int $structure_id): bool => $this->allowsStructure($structure_id)
                    )
                )
            )
        );
    }

    /**
     * @return list<int>
     */
    public function getStructureIds(): array
    {
        return $this->structure_ids;
    }

    /**
     * Resolve a member's effective EEDS structure scope.
     *
     * @return list<int>
     */
    public static function forMember(
        Db $zdb,
        int $member_id,
        string $permission,
        ?string $effective_date = null
    ): array {
        $date = $effective_date ?? (new DateTimeImmutable())->format('Y-m-d');
        $select = $zdb->select('eeds_member_scopes', 'ms');
        $select->columns(['ms.id_structure'])->join(
            name: ['st' => PREFIX_DB . 'eeds_structures'],
            on: 'ms.id_structure=st.id_structure',
            columns: [],
            type: $select::JOIN_INNER
        )->where([
            'ms.id_adh' => $member_id,
            'ms.scope_permission' => $permission,
        ]);
        $select->where('ms.effective_start <= ' . $zdb->platform->quoteValue($date));
        $select->where('(ms.effective_end IS NULL OR ms.effective_end >= ' . $zdb->platform->quoteValue($date) . ')');
        $select->where('st.effective_start <= ' . $zdb->platform->quoteValue($date));
        $select->where('(st.effective_end IS NULL OR st.effective_end >= ' . $zdb->platform->quoteValue($date) . ')');

        $roots = [];
        foreach ($zdb->execute($select) as $row) {
            $roots[(int)$row->id_structure] = true;
        }

        $scope = array_keys($roots);
        $queue = $scope;
        while ($queue !== []) {
            $current = array_shift($queue);
            $children = $zdb->select('eeds_structures');
            $children->columns(['id_structure'])->where(['parent_structure' => $current]);
            $children->where('effective_start <= ' . $zdb->platform->quoteValue($date));
            $children->where('(effective_end IS NULL OR effective_end >= ' . $zdb->platform->quoteValue($date) . ')');
            foreach ($zdb->execute($children) as $row) {
                $child = (int)$row->id_structure;
                if (!isset($roots[$child])) {
                    $roots[$child] = true;
                    $queue[] = $child;
                    $scope[] = $child;
                }
            }
        }

        sort($scope, SORT_NUMERIC);
        return $scope;
    }
}
