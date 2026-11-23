<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation;

use Galette\Access\Permission;
use Galette\Core\Authentication;
use Galette\Core\GalettePlugin;
use Galette\Core\Plugins\MenuProviderInterface;
use Galette\Core\Plugins\PermissionsProviderInterface;
use Galette\Core\Plugins\InstallInitInterface;
use Galette\Core\Db;
use EEDSFoundation\Domain\EedsExportRoleInitializer;
use EEDSFoundation\Domain\StructureService;
use EEDSFoundation\Domain\StructureGroupMapper;

use function Safe\date;

/**
 * R1 EEDS plugin boundary and permission provider.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
class PluginGaletteEeds extends GalettePlugin implements
    MenuProviderInterface,
    PermissionsProviderInterface,
    InstallInitInterface
{
    /**
     * Constructor.
     *
     * @param Db $zdb Database instance
     */
    public function __construct(private readonly Db $zdb)
    {
    }

    /**
     * Create the default national root during plugin installation.
     */
    public function installInit(Db $zdb): void
    {
        $root_id = (new StructureService())->ensureNationalRoot(
            $zdb,
            'Nation',
            'NATION',
            date('Y-m-d'),
            null
        );
        (new StructureGroupMapper())->ensureGroupForStructure($zdb, $root_id);
        (new EedsExportRoleInitializer())->initialize($zdb);
    }

    /**
     * Is the plugin fully installed?
     */
    public function isInstalled(): bool
    {
        $required_tables = [
            'eeds_structures',
            'eeds_member_scopes',
            'eeds_member_affiliations',
            'eeds_member_lifecycle',
            'eeds_status_history',
            'eeds_renewals',
            'eeds_member_numbers',
            'eeds_duplicate_candidates',
            'eeds_export_audit',
            'eeds_structure_groups',
        ];
        return array_diff(
            array_map(static fn(string $table): string => PREFIX_DB . $table, $required_tables),
            $this->zdb->getTables()
        ) === [];
    }

    /**
     * Untracked EEDS installations predate the dedicated export roles.
     */
    public function getLegacyDbVersion(): ?float
    {
        return 0.2;
    }

    /**
     * Get R1 plugin permissions.
     *
     * @return list<Permission>
     */
    public function getPermissions(): array
    {
        return [
            new Permission('eeds:read', _T('Read EEDS registry'), Authentication::ACCESS_STAFF, group: false),
            new Permission(
                'eeds:structure-create',
                _T('Create EEDS structures'),
                Authentication::ACCESS_STAFF,
                group: false
            ),
            new Permission(
                'eeds:member-edit',
                _T('Edit EEDS member affiliations'),
                Authentication::ACCESS_STAFF,
                group: false
            ),
            new Permission(
                'eeds:validate',
                _T('Validate EEDS membership events'),
                Authentication::ACCESS_STAFF,
                group: false
            ),
            new Permission(
                'eeds:export',
                _T('Export EEDS registry data'),
                Authentication::ACCESS_SUPERADMIN,
                group: false
            ),
        ];
    }

    /**
     * Get private menu entries.
     *
     * @return array<string, string|array<string,mixed>>
     */
    public function getMenus(): array
    {
        return [
            'eeds_registry' => [
                'title' => _T('EEDS registry'),
                'route' => [
                    'name' => 'eeds_registry',
                ],
            ],
        ];
    }

    /**
     * Get public menu entries.
     *
     * @return array<int, string|array<string,mixed>>
     */
    public function getPublicMenus(): array
    {
        return [];
    }
}
