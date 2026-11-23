<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Updates;

use Galette\Updater\AbstractUpdater;

/**
 * Add the explicit EEDS structure to Galette group mapping table.
 *
 * Existing groups and memberships are intentionally not modified here.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final class UpgradeTo04 extends AbstractUpdater
{
    protected ?string $db_version = '0.4';

    /**
     * Configure the SQL scripts for both supported database engines.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setSqlScripts('0.4');
    }

    /**
     * Keep existing groups and memberships unchanged during the migration.
     */
    protected function update(): bool
    {
        return true;
    }
}
