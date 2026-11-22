<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation;

use Galette\Core\Authentication;
use Galette\Core\GalettePlugin;
use Galette\Core\Plugins\MenuProviderInterface;
use Galette\Core\Plugins\PermissionsProviderInterface;
use Galette\Core\Plugins\InstallInitInterface;
use Galette\Core\Db;
use EEDSFoundation\Domain\StructureService;

use function Safe\date;
use Galette\Access\Permission;

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
     * Create the default national root during plugin installation.
     */
    public function installInit(Db $zdb): void
    {
        (new StructureService())->ensureNationalRoot(
            $zdb,
            'Nation',
            'NATION',
            date('Y-m-d'),
            null
        );
    }

    /**
     * Is the plugin fully installed?
     */
    public function isInstalled(): bool
    {
        return true;
    }

    /**
     * Get R1 plugin permissions.
     *
     * @return list<Permission>
     */
    public function getPermissions(): array
    {
        return [
            new Permission('eeds:read', _T('Read EEDS registry'), Authentication::ACCESS_STAFF, group: true),
            new Permission('eeds:structure-create', _T('Create EEDS structures'), Authentication::ACCESS_STAFF, group: true),
            new Permission('eeds:member-edit', _T('Edit EEDS member affiliations'), Authentication::ACCESS_STAFF, group: true),
            new Permission('eeds:validate', _T('Validate EEDS membership events'), Authentication::ACCESS_STAFF, group: true),
            new Permission('eeds:export', _T('Export EEDS registry data'), Authentication::ACCESS_STAFF, group: true),
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
