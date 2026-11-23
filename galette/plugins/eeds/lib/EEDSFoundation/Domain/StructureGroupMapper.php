<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Domain;

use Galette\Core\Db;
use Galette\Core\Logs;
use Galette\Access\Roles;
use Galette\Entity\Group;
use Analog\Analog;
use Safe\DateTimeImmutable;
use Throwable;

/**
 * Materialize the authoritative EEDS structure tree as Galette groups.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class StructureGroupMapper
{
    /**
     * Create a Galette group for an EEDS structure and persist its explicit link.
     */
    public function ensureGroupForStructure(Db $zdb, int $structure_id): int
    {
        $structure = $this->getStructure($zdb, $structure_id);
        $existing_group_id = $this->getMappedGroupId($zdb, $structure_id);
        if ($existing_group_id !== null) {
            $this->assertMappingMatches($zdb, $structure, $existing_group_id);
            return $existing_group_id;
        }

        $parent_group_id = $this->getParentGroupId($zdb, $structure);
        $candidates = $this->groupIdsByName($zdb, (string)$structure['structure_name']);
        if ($candidates !== []) {
            throw new \RuntimeException(
                sprintf(
                    'EEDS structure #%1$d cannot be mapped automatically: Galette group name "%2$s" '
                    . 'already exists (group IDs: %3$s). Review the group and use eeds:map-structure-group.',
                    $structure_id,
                    $structure['structure_name'],
                    implode(', ', $candidates)
                )
            );
        }

        $group = new Group();
        $group->setName((string)$structure['structure_name']);
        if ($parent_group_id !== null) {
            $group->setParentGroup($parent_group_id);
        }
        $group->store();
        $group_id = $group->getId();
        if ($group_id === null) {
            throw new \RuntimeException('Galette did not return an ID for the new EEDS structure group.');
        }

        $this->insertMapping($zdb, $structure_id, $group_id);
        return $group_id;
    }

    /**
     * Explicitly link a reviewed existing Galette group to an EEDS structure.
     */
    public function mapExistingGroup(
        Db $zdb,
        int $structure_id,
        int $group_id,
        bool $replace_memberships = false
    ): void {
        $owns_transaction = !$zdb->inTransaction();
        try {
            if ($owns_transaction) {
                $zdb->beginTransaction();
            }
            $structure = $this->getStructure($zdb, $structure_id);

            $mapped_group_id = $this->getMappedGroupId($zdb, $structure_id);
            if ($mapped_group_id !== null) {
                if ($mapped_group_id !== $group_id) {
                    throw new \InvalidArgumentException('This EEDS structure is already mapped to another group.');
                }
                $this->assertMappingMatches($zdb, $structure, $group_id);
                if ($owns_transaction) {
                    $zdb->commit();
                }
                return;
            }

            $select = $zdb->select(Group::TABLE);
            $select->columns([Group::PK, 'group_name', 'parent_group'])->where([Group::PK => $group_id]);
            $rows = $zdb->execute($select);
            if ($rows->count() !== 1) {
                throw new \InvalidArgumentException('The selected Galette group does not exist.');
            }
            $group = $rows->current();
            if ((string)$group->group_name !== (string)$structure['structure_name']) {
                throw new \InvalidArgumentException(
                    'The Galette group name must exactly match the EEDS structure name.'
                );
            }

            $expected_parent_id = $this->getParentGroupId($zdb, $structure);
            $actual_parent_id = $group->parent_group === null ? null : (int)$group->parent_group;
            if ($actual_parent_id !== $expected_parent_id) {
                throw new \InvalidArgumentException(
                    'The existing Galette group has a different parent; it was not reparented.'
                );
            }
            $this->assertGroupHasNoManagersOrScopedRoles($zdb, $group_id);

            $duplicate = $zdb->select('eeds_structure_groups');
            $duplicate->columns(['id_structure'])->where(['id_group' => $group_id]);
            if ($zdb->execute($duplicate)->count() > 0) {
                throw new \InvalidArgumentException('The Galette group is already mapped to another EEDS structure.');
            }

            $this->insertMapping($zdb, $structure_id, $group_id);
            $replace_ids = $replace_memberships ? [$group_id] : [];
            $this->synchronizeMemberships(
                $zdb,
                (new DateTimeImmutable())->format('Y-m-d'),
                $replace_ids
            );
            if ($owns_transaction) {
                $zdb->commit();
            }
        } catch (Throwable $e) {
            if ($owns_transaction && $zdb->inTransaction()) {
                $zdb->rollback();
            }
            Logs::exception($e, 'Unable to map an existing Galette group to an EEDS structure', Analog::WARNING);
            throw $e;
        }
    }

    /**
     * Preview unmapped structures without changing Galette data.
     *
     * @return list<string>
     */
    public function preview(Db $zdb): array
    {
        $messages = [];
        foreach ($this->getStructures($zdb) as $structure) {
            $structure_id = (int)$structure['id_structure'];
            $mapped_group_id = $this->getMappedGroupId($zdb, $structure_id);
            if ($mapped_group_id !== null) {
                $this->assertMappingMatches($zdb, $structure, $mapped_group_id);
                $messages[] = sprintf(
                    'Structure #%1$d "%2$s" is mapped to group #%3$d.',
                    $structure_id,
                    $structure['structure_name'],
                    $mapped_group_id
                );
                continue;
            }

            $candidates = $this->groupIdsByName($zdb, (string)$structure['structure_name']);
            if ($candidates !== []) {
                $messages[] = sprintf(
                    'Structure #%1$d "%2$s" needs explicit review of existing group IDs: %3$s.',
                    $structure_id,
                    $structure['structure_name'],
                    implode(', ', $candidates)
                );
                continue;
            }

            $messages[] = sprintf(
                'Structure #%1$d "%2$s" would create a Galette group under its mapped EEDS parent.',
                $structure_id,
                $structure['structure_name']
            );
        }
        return $messages;
    }

    /**
     * Create missing groups, then reconcile mapped-group memberships.
     */
    public function synchronize(Db $zdb, bool $replace_memberships = false): void
    {
        $owns_transaction = !$zdb->inTransaction();
        try {
            if ($owns_transaction) {
                $zdb->beginTransaction();
            }
            foreach ($this->getStructures($zdb) as $structure) {
                $this->ensureGroupForStructure($zdb, (int)$structure['id_structure']);
            }

            $replace_ids = $replace_memberships ? $this->getMappedGroupIds($zdb) : [];
            $this->synchronizeMemberships(
                $zdb,
                (new DateTimeImmutable())->format('Y-m-d'),
                $replace_ids
            );
            if ($owns_transaction) {
                $zdb->commit();
            }
        } catch (Throwable $e) {
            if ($owns_transaction && $zdb->inTransaction()) {
                $zdb->rollback();
            }
            Logs::exception($e, 'Unable to synchronize EEDS structures with Galette groups', Analog::WARNING);
            throw $e;
        }
    }

    /**
     * Reconcile one member after an EEDS affiliation change.
     */
    public function synchronizeMember(Db $zdb, int $member_id): void
    {
        $this->synchronizeMemberships(
            $zdb,
            (new DateTimeImmutable())->format('Y-m-d'),
            $this->getMappedGroupIds($zdb),
            $member_id
        );
    }

    /**
     * @param array<string, int|string|null> $structure
     */
    private function getParentGroupId(Db $zdb, array $structure): ?int
    {
        $parent_structure_id = $structure['parent_structure'];
        if ($parent_structure_id === null) {
            return null;
        }

        $parent_group_id = $this->getMappedGroupId($zdb, (int)$parent_structure_id);
        if ($parent_group_id === null) {
            throw new \RuntimeException(
                'The parent EEDS structure must be mapped before its child structure.'
            );
        }
        return $parent_group_id;
    }

    /**
     * @return array<string, int|string|null>
     */
    private function getStructure(Db $zdb, int $structure_id): array
    {
        $select = $zdb->select('eeds_structures');
        $select->columns(['id_structure', 'parent_structure', 'structure_name'])
            ->where(['id_structure' => $structure_id]);
        $rows = $zdb->execute($select);
        if ($rows->count() !== 1) {
            throw new \InvalidArgumentException('The selected EEDS structure does not exist.');
        }
        return (array)$rows->current();
    }

    /**
     * @return list<array<string, int|string|null>>
     */
    private function getStructures(Db $zdb): array
    {
        $select = $zdb->select('eeds_structures');
        $select->columns(['id_structure', 'parent_structure', 'structure_name', 'structure_type']);

        $structures = [];
        foreach ($zdb->execute($select) as $row) {
            $structures[] = (array)$row;
        }
        $type_order = [
            'national' => 0,
            'region' => 1,
            'district' => 2,
            'groupe_local' => 3,
            'unite' => 4,
        ];
        usort(
            $structures,
            static fn(array $left, array $right): int => (
                ($type_order[$left['structure_type']] ?? PHP_INT_MAX)
                <=> ($type_order[$right['structure_type']] ?? PHP_INT_MAX)
            ) ?: ((int)$left['id_structure'] <=> (int)$right['id_structure'])
        );
        return $structures;
    }

    /**
     * Find the Galette group linked to a structure.
     */
    private function getMappedGroupId(Db $zdb, int $structure_id): ?int
    {
        $select = $zdb->select('eeds_structure_groups');
        $select->columns(['id_group'])->where(['id_structure' => $structure_id]);
        $rows = $zdb->execute($select);
        return $rows->count() === 0 ? null : (int)$rows->current()->id_group;
    }

    /**
     * @return list<int>
     */
    private function getMappedGroupIds(Db $zdb): array
    {
        $select = $zdb->select('eeds_structure_groups');
        $select->columns(['id_group']);
        $ids = [];
        foreach ($zdb->execute($select) as $row) {
            $ids[] = (int)$row->id_group;
        }
        return $ids;
    }

    /**
     * @return list<int>
     */
    private function groupIdsByName(Db $zdb, string $name): array
    {
        $select = $zdb->select(Group::TABLE);
        $select->columns([Group::PK])->where(['group_name' => $name]);
        $ids = [];
        foreach ($zdb->execute($select) as $row) {
            $ids[] = (int)$row->{Group::PK};
        }
        return $ids;
    }

    /**
     * @param array<string, int|string|null> $structure
     */
    private function assertMappingMatches(Db $zdb, array $structure, int $group_id): void
    {
        $select = $zdb->select(Group::TABLE);
        $select->columns(['group_name', 'parent_group'])->where([Group::PK => $group_id]);
        $rows = $zdb->execute($select);
        if ($rows->count() !== 1) {
            throw new \RuntimeException('A mapped Galette group no longer exists.');
        }
        $group = $rows->current();
        $expected_parent_id = $this->getParentGroupId($zdb, $structure);
        $actual_parent_id = $group->parent_group === null ? null : (int)$group->parent_group;
        if (
            (string)$group->group_name !== (string)$structure['structure_name']
            || $actual_parent_id !== $expected_parent_id
        ) {
            throw new \RuntimeException(
                sprintf(
                    'The mapping for EEDS structure #%1$d is inconsistent with its Galette group.',
                    $structure['id_structure']
                )
            );
        }
    }

    /**
     * Persist the one-to-one structure/group relation.
     */
    private function insertMapping(Db $zdb, int $structure_id, int $group_id): void
    {
        $insert = $zdb->insert('eeds_structure_groups');
        $insert->values([
            'id_structure' => $structure_id,
            'id_group' => $group_id,
        ]);
        $zdb->execute($insert);
    }

    /**
     * Avoid converting a group with existing manager or permission assignments.
     */
    private function assertGroupHasNoManagersOrScopedRoles(Db $zdb, int $group_id): void
    {
        $managers = $zdb->select(Group::GROUPSMANAGERS_TABLE);
        $managers->columns(['id_adh'])->where([Group::PK => $group_id]);
        $roles = $zdb->select(Roles::MEMBERS_TABLE);
        $roles->columns(['id_member_role'])->where(['id_group' => $group_id]);
        if ($zdb->execute($managers)->count() > 0 || $zdb->execute($roles)->count() > 0) {
            throw new \InvalidArgumentException(
                'The existing group has managers or group-scoped roles. Review and remove those assignments '
                . 'before mapping it; no permissions are migrated automatically.'
            );
        }
    }

    /**
     * Synchronize members of mapped groups from active affiliations and ancestors.
     *
     * @param list<int> $replace_group_ids Mapped group IDs whose stale memberships may be removed
     */
    private function synchronizeMemberships(
        Db $zdb,
        string $effective_date,
        array $replace_group_ids,
        ?int $only_member_id = null
    ): void {
        $tree_select = $zdb->select('eeds_structure_groups', 'sg');
        $tree_select->columns([
            'id_structure' => 's.id_structure',
            'parent_structure' => 's.parent_structure',
            'id_group' => 'sg.id_group',
        ])->join(
            ['s' => PREFIX_DB . 'eeds_structures'],
            's.id_structure=sg.id_structure',
            [],
            $tree_select::JOIN_INNER
        );
        $tree = [];
        foreach ($zdb->execute($tree_select) as $row) {
            $tree[(int)$row->id_structure] = [
                'parent' => $row->parent_structure === null ? null : (int)$row->parent_structure,
                'group' => (int)$row->id_group,
            ];
        }
        if ($tree === []) {
            return;
        }
        $mapped_group_ids = array_values(array_map(
            static fn(array $entry): int => $entry['group'],
            $tree
        ));
        $replace_group_ids = array_values(array_intersect($mapped_group_ids, $replace_group_ids));

        $affiliations = $zdb->select('eeds_member_affiliations');
        $affiliations->columns(['id_adh', 'id_structure'])->where([
            'affiliation_status' => 'active',
        ]);
        $affiliations->where('effective_start <= ' . $zdb->platform->quoteValue($effective_date));
        $affiliations->where(
            '(effective_end IS NULL OR effective_end >= ' . $zdb->platform->quoteValue($effective_date) . ')'
        );
        if ($only_member_id !== null) {
            $affiliations->where(['id_adh' => $only_member_id]);
        }

        /** @var array<int, array<int, true>> $desired */
        $desired = [];
        foreach ($zdb->execute($affiliations) as $row) {
            $member_id = (int)$row->id_adh;
            $structure_id = (int)$row->id_structure;
            $seen = [];
            while ($structure_id !== 0) {
                if (isset($seen[$structure_id])) {
                    throw new \RuntimeException('A cycle was found in the EEDS structure hierarchy.');
                }
                $seen[$structure_id] = true;
                if (!isset($tree[$structure_id])) {
                    throw new \RuntimeException(
                        'An active EEDS affiliation or its ancestor has no mapped Galette group.'
                    );
                }
                $desired[$member_id][$tree[$structure_id]['group']] = true;
                $structure_id = $tree[$structure_id]['parent'] ?? 0;
            }
        }

        $current = $zdb->select(Group::GROUPSUSERS_TABLE, 'gm');
        $current->columns(['id_adh' => 'gm.id_adh', 'id_group' => 'gm.id_group'])
            ->where->in('gm.id_group', $mapped_group_ids);
        if ($only_member_id !== null) {
            $current->where(['gm.id_adh' => $only_member_id]);
        }
        /** @var array<int, array<int, true>> $current_memberships */
        $current_memberships = [];
        foreach ($zdb->execute($current) as $row) {
            $current_memberships[(int)$row->id_adh][(int)$row->id_group] = true;
        }

        $member_ids = array_unique(array_merge(array_keys($desired), array_keys($current_memberships)));
        $plans = [];
        foreach ($member_ids as $member_id) {
            $desired_ids = array_keys($desired[$member_id] ?? []);
            $current_ids = array_keys($current_memberships[$member_id] ?? []);
            $stale_ids = array_values(array_diff($current_ids, $desired_ids));
            $removable_ids = array_values(array_intersect($stale_ids, $replace_group_ids));
            $unresolved_ids = array_values(array_diff($stale_ids, $removable_ids));
            if ($unresolved_ids !== []) {
                throw new \RuntimeException(
                    sprintf(
                        'Mapped Galette group memberships for member #%1$d differ from active EEDS affiliations. '
                        . 'Review the groups, then rerun with --replace-memberships if removal is approved.',
                        $member_id
                    )
                );
            }
            $plans[(int)$member_id] = [
                'add' => array_values(array_diff($desired_ids, $current_ids)),
                'remove' => $removable_ids,
            ];
        }

        foreach ($plans as $member_id => $plan) {
            foreach ($plan['remove'] as $group_id) {
                $delete = $zdb->delete(Group::GROUPSUSERS_TABLE);
                $delete->where([
                    'id_adh' => $member_id,
                    'id_group' => $group_id,
                ]);
                $zdb->execute($delete);
            }
            foreach ($plan['add'] as $group_id) {
                $insert = $zdb->insert(Group::GROUPSUSERS_TABLE);
                $insert->values([
                    'id_adh' => $member_id,
                    'id_group' => $group_id,
                ]);
                $zdb->execute($insert);
            }
        }
    }
}
