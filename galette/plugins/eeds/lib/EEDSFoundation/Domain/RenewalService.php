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

/**
 * R1 membership renewal history.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class RenewalService
{
    /**
     * Record an approved or pending renewal event.
     */
    public function renew(
        Db $zdb,
        int $member_id,
        string $source_period,
        string $target_period,
        int $validator_id,
        ?string $fee_status_reference,
        string $resulting_status,
        bool $validated
    ): bool {
        if ($source_period === '' || $target_period === '') {
            throw new \InvalidArgumentException('Renewal periods are required.');
        }

        if (!in_array($resulting_status, MemberLifecycle::STATUSES, strict: true)) {
            throw new \InvalidArgumentException('Unknown renewal resulting status.');
        }

        try {
            $insert = $zdb->insert('eeds_renewals');
            $insert->values([
                'id_adh' => $member_id,
                'source_period' => $source_period,
                'target_period' => $target_period,
                'request_date' => (new DateTimeImmutable())->format('Y-m-d'),
                'validation_date' => $validated
                    ? (new DateTimeImmutable())->format('Y-m-d')
                    : null,
                'validator_id' => $validator_id,
                'fee_status_reference' => $fee_status_reference,
                'resulting_status' => $resulting_status,
                'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]);
            $zdb->execute($insert);

            if ($validated) {
                (new MemberLifecycle())->transition(
                    $zdb,
                    $member_id,
                    $resulting_status,
                    (new DateTimeImmutable())->format('Y-m-d'),
                    $validator_id,
                    'Annual membership renewal',
                    null
                );
            }
            return true;
        } catch (Throwable $e) {
            Logs::exception($e, 'Unable to record EEDS renewal', Analog::WARNING);
            throw $e;
        }
    }
}
