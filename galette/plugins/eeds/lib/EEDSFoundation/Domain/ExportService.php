<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Domain;

use Safe\DateTimeImmutable;
use Galette\Core\Db;
use Galette\Core\Logs;
use Analog\Analog;
use Throwable;

use function Safe\json_encode;

/**
 * R1 audited export boundary.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class ExportService
{
    /**
     * Record an approved export without producing unrestricted database exports.
     */
    /**
     * @param list<string> $fields
     * @param list<int>    $structure_ids
     */
    public function record(
        Db $zdb,
        int $actor_id,
        array $fields,
        array $structure_ids,
        int $row_count
    ): bool {
        if ($fields === [] || $structure_ids === []) {
            throw new \InvalidArgumentException('Export fields and structure scope are required.');
        }

        try {
            $insert = $zdb->insert('eeds_export_audit');
            $insert->values([
                'actor_id' => $actor_id,
                'export_fields' => json_encode($fields, JSON_UNESCAPED_UNICODE),
                'structure_ids' => json_encode($structure_ids, JSON_UNESCAPED_UNICODE),
                'row_count' => $row_count,
                'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]);
            $zdb->execute($insert);
            return true;
        } catch (Throwable $e) {
            Logs::exception($e, 'Unable to record EEDS export', Analog::WARNING);
            throw $e;
        }
    }
}
