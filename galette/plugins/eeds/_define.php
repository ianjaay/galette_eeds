<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

use Galette\Core\Plugins\PluginDefinition;

return new PluginDefinition(
    name: 'EEDS Foundation',
    desc: 'EEDS domain and authorization foundation',
    author: 'EEDS',
    version: '0.1.0',
    compver: GALETTE_COMPAT_VERSION,
    route: 'eeds',
    date: '2026-11-22',
    acls: [
        'eeds_registry' => 'eeds:read',
        'eeds_structures_create' => 'eeds:structure-create',
        'eeds_status_transition' => 'eeds:validate',
        'eeds_transfer' => 'eeds:validate',
        'eeds_renewal' => 'eeds:validate',
        'eeds_duplicates' => 'eeds:read',
        'eeds_duplicate_review' => 'eeds:validate',
        'eeds_member_number' => 'eeds:validate',
        'eeds_export' => 'eeds:export',
    ],
    dbver: 0.2
);
