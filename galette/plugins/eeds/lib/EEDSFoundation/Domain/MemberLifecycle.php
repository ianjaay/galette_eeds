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
 * Approved R1 membership lifecycle transitions.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class MemberLifecycle
{
    public const array STATUSES = [
        'draft',
        'pending',
        'active',
        'suspended',
        'inactive',
        'resigned',
        'expelled',
        'deceased',
        'sympathizer',
        'former',
    ];

    /**
     * Transition a member and preserve typed history.
     */
    public function transition(
        Db $zdb,
        int $member_id,
        string $new_status,
        string $effective_date,
        int $actor_id,
        string $reason,
        ?string $evidence_reference = null
    ): bool {
        if (!in_array($new_status, self::STATUSES, strict: true)) {
            throw new \InvalidArgumentException('Unknown EEDS membership status.');
        }

        $effective = DateTimeImmutable::createFromFormat('Y-m-d', $effective_date);
        if ($effective->format('Y-m-d') !== $effective_date) {
            throw new \InvalidArgumentException('Invalid effective date.');
        }

        try {
            $select = $zdb->select('eeds_member_lifecycle');
            $select->columns(['membership_status'])->where(['id_adh' => $member_id]);
            $result = $zdb->execute($select);
            $previous = $result->count() > 0
                ? (string)$result->current()->membership_status
                : null;

            $allowed = $this->isAllowedTransition($previous, $new_status);
            if (!$allowed) {
                throw new \InvalidArgumentException('This R1 membership transition is not allowed.');
            }

            $zdb->beginTransaction();

            $insert = $zdb->insert('eeds_status_history');
            $insert->values([
                'id_adh' => $member_id,
                'previous_status' => $previous,
                'new_status' => $new_status,
                'effective_date' => $effective_date,
                'reason' => $reason,
                'evidence_reference' => $evidence_reference,
                'actor_id' => $actor_id,
                'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]);
            $zdb->execute($insert);

            if ($previous === null) {
                $insert = $zdb->insert('eeds_member_lifecycle');
                $insert->values([
                    'id_adh' => $member_id,
                    'membership_status' => $new_status,
                    'effective_date' => $effective_date,
                    'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'updated_by' => $actor_id,
                ]);
                $zdb->execute($insert);
            } else {
                $update = $zdb->update('eeds_member_lifecycle');
                $update->set([
                    'membership_status' => $new_status,
                    'effective_date' => $effective_date,
                    'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'updated_by' => $actor_id,
                ]);
                $update->where(['id_adh' => $member_id]);
                $zdb->execute($update);
            }

            $zdb->commit();
            return true;
        } catch (Throwable $e) {
            if ($zdb->inTransaction()) {
                $zdb->rollback();
            }
            Logs::exception($e, 'Unable to transition EEDS member lifecycle', Analog::WARNING);
            throw $e;
        }
    }

    /**
     * Is a transition allowed by the approved R1 proposal?
     */
    public function isAllowedTransition(?string $previous, string $new_status): bool
    {
        if ($previous === null) {
            return in_array($new_status, ['draft', 'pending', 'active', 'sympathizer'], strict: true);
        }

        if ($previous === 'suspended') {
            return $new_status === 'active';
        }

        if ($previous === 'pending') {
            return in_array($new_status, ['active', 'inactive', 'expelled'], strict: true);
        }

        if ($previous === 'draft') {
            return in_array($new_status, ['pending', 'inactive'], strict: true);
        }

        if ($previous === 'active') {
            return in_array(
                $new_status,
                ['suspended', 'inactive', 'resigned', 'expelled', 'deceased', 'former'],
                strict: true
            );
        }

        return false;
    }
}
