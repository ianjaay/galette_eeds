<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Security;

/**
 * R1 export allowlist contract.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class EedsExportPolicy
{
    /**
     * @param list<string> $allowed_fields
     * @param list<int>    $allowed_structure_ids
     */
    public function __construct(
        private array $allowed_fields,
        private array $allowed_structure_ids
    ) {
    }

    /**
     * @param list<string> $requested_fields
     * @param list<int>    $requested_structure_ids
     */
    public function allows(array $requested_fields, array $requested_structure_ids): bool
    {
        if ($requested_fields === [] || $requested_structure_ids === []) {
            return false;
        }

        foreach ($requested_fields as $field) {
            if (!in_array($field, $this->allowed_fields, strict: true)) {
                return false;
            }
        }

        foreach ($requested_structure_ids as $structure_id) {
            if (!in_array($structure_id, $this->allowed_structure_ids, strict: true)) {
                return false;
            }
        }

        return true;
    }
}
