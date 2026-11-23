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
    protected ?string $sql_version = '0.4';

    protected function update(): bool
    {
        return true;
    }
}
