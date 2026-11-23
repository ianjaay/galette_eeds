<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Updates;

use EEDSFoundation\Domain\EedsExportRoleInitializer;
use Galette\Updater\AbstractUpdater;

/**
 * Remove the broad Staff export grant and provision the approved profile roles.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final class UpgradeTo03 extends AbstractUpdater
{
    protected ?string $db_version = '0.3';

    /**
     * Initialize dedicated roles and their permissions.
     */
    protected function update(): bool
    {
        (new EedsExportRoleInitializer())->initialize($this->zdb);
        $this->addReportEntry('Dedicated EEDS export roles initialized.', self::REPORT_SUCCESS);
        return true;
    }
}
