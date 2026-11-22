<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace Galette\Tests\Core;

use EEDSFoundation\Access\EedsScope;
use EEDSFoundation\Security\EedsExportPolicy;
use EEDSFoundation\Domain\MemberLifecycle;
use PHPUnit\Framework\TestCase;

require_once GALETTE_ROOT . 'plugins/eeds/lib/EEDSFoundation/Access/EedsScope.php';
require_once GALETTE_ROOT . 'plugins/eeds/lib/EEDSFoundation/Security/EedsExportPolicy.php';
require_once GALETTE_ROOT . 'plugins/eeds/lib/EEDSFoundation/Domain/MemberLifecycle.php';

/**
 * R0 fail-closed scope and export contract tests.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
class EedsFoundationTest extends TestCase
{
    /**
     * Scope allows only explicitly assigned structures.
     */
    public function testScopeAllowsOnlyExplicitStructures(): void
    {
        $scope = new EedsScope([10, 11]);

        $this->assertTrue($scope->allowsStructure(10));
        $this->assertTrue($scope->allowsStructure(11));
        $this->assertFalse($scope->allowsStructure(12));
        $this->assertFalse($scope->allowsStructure(0));
    }

    /**
     * Empty scope denies every structure.
     */
    public function testEmptyScopeFailsClosed(): void
    {
        $scope = new EedsScope([]);

        $this->assertFalse($scope->allowsStructure(10));
        $this->assertSame([], $scope->getStructureIds());
    }

    /**
     * Scope filtering denies sibling and unrelated structures.
     */
    public function testScopeFiltersSiblingAndUnrelatedStructures(): void
    {
        $scope = new EedsScope([10, 11]);

        $this->assertSame(
            [10, 11],
            $scope->filterStructureIds([10, 11, 12, 13])
        );
        $this->assertSame(
            [],
            $scope->filterStructureIds([12, 13])
        );
    }

    /**
     * Export allowlist validates fields and structures.
     */
    public function testExportPolicyAllowlistsFieldsAndStructures(): void
    {
        $policy = new EedsExportPolicy(
            ['id', 'name', 'structure_id'],
            [10, 11]
        );

        $this->assertTrue(
            $policy->allows(['id', 'name'], [10])
        );
        $this->assertFalse(
            $policy->allows(['id', 'birth_date'], [10])
        );
        $this->assertFalse(
            $policy->allows(['id', 'name'], [12])
        );
        $this->assertFalse(
            $policy->allows([], [10])
        );
        $this->assertFalse(
            $policy->allows(['id'], [])
        );
    }

    /**
     * Approved R1 status transitions deny unapproved directions.
     */
    public function testLifecycleTransitionRules(): void
    {
        $lifecycle = new MemberLifecycle();

        $this->assertTrue($lifecycle->isAllowedTransition(null, 'draft'));
        $this->assertTrue($lifecycle->isAllowedTransition('pending', 'active'));
        $this->assertTrue($lifecycle->isAllowedTransition('suspended', 'active'));
        $this->assertFalse($lifecycle->isAllowedTransition('deceased', 'active'));
        $this->assertFalse($lifecycle->isAllowedTransition('active', 'draft'));
    }
}
