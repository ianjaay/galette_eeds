<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Domain;

use Analog\Analog;
use Galette\Access\Role;
use Galette\Access\Roles;
use Galette\Core\Db;
use Galette\Core\Logs;
use Throwable;

/**
 * Create the dedicated Galette roles authorized to export EEDS data.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class EedsExportRoleInitializer
{
    /**
     * @var array<string, string>
     */
    public const array PROFILE_ROLES = [
        'Administrateur central/national' => 'EEDS export - Administrateur central/national',
        'Commissaire de région' => 'EEDS export - Commissaire de région',
        'Commissaire de district' => 'EEDS export - Commissaire de district',
        'Commissaire de groupe local' => 'EEDS export - Commissaire de groupe local',
    ];

    /**
     * Create the approved profile roles and grant only EEDS read and export.
     *
     * No member assignments or organizational scopes are created here.
     */
    public function initialize(Db $zdb): void
    {
        $transaction = !$zdb->inTransaction();
        if ($transaction) {
            $zdb->beginTransaction();
        }

        try {
            foreach (self::PROFILE_ROLES as $name) {
                $select = $zdb->select(Roles::TABLE);
                $select->columns([Role::PK])->where(['name' => $name]);
                $result = $zdb->execute($select);

                if ($result->count() > 0) {
                    $role_id = (int)$result->current()->{Role::PK};
                } else {
                    $role = new Role();
                    $role->setName($name);
                    if (!$role->store($zdb)) {
                        throw new \RuntimeException(sprintf('Unable to create the role "%s".', $name));
                    }
                    $role_id = (int)$role->getId();
                }

                foreach (['eeds:read', 'eeds:export'] as $permission) {
                    $select = $zdb->select(Roles::PERMISSIONS_TABLE);
                    $select->columns([Role::PK])->where([
                        Role::PK => $role_id,
                        'permission' => $permission,
                    ]);
                    if ($zdb->execute($select)->count() === 0) {
                        $insert = $zdb->insert(Roles::PERMISSIONS_TABLE);
                        $insert->values([
                            Role::PK => $role_id,
                            'permission' => $permission,
                        ]);
                        $zdb->execute($insert);
                    }
                }
            }

            $select = $zdb->select(Roles::TABLE);
            $select->columns([Role::PK])->where(['role_key' => Roles::STAFF]);
            foreach ($zdb->execute($select) as $row) {
                $delete = $zdb->delete(Roles::PERMISSIONS_TABLE);
                $delete->where([
                    Role::PK => (int)$row->{Role::PK},
                    'permission' => 'eeds:export',
                ]);
                $zdb->execute($delete);
            }

            if ($transaction) {
                $zdb->commit();
            }
        } catch (Throwable $e) {
            if ($transaction && $zdb->inTransaction()) {
                $zdb->rollback();
            }
            Logs::exception($e, 'Unable to initialize dedicated EEDS export roles', Analog::WARNING);
            throw $e;
        }
    }
}
