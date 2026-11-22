<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace Galette\Tests\Core;

use EEDSFoundation\Access\EedsScope;
use EEDSFoundation\Domain\AffiliationService;
use EEDSFoundation\Domain\DuplicatePreview;
use EEDSFoundation\Domain\EedsExportRoleInitializer;
use EEDSFoundation\Domain\RenewalService;
use EEDSFoundation\Domain\StructureService;
use EEDSFoundation\Domain\EedsExportService;
use EEDSFoundation\Security\EedsExportPolicy;
use EEDSFoundation\Domain\MemberLifecycle;
use Galette\Core\Db;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Select;
use PHPUnit\Framework\TestCase;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;

use function Safe\define;
use function Safe\unlink;

require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Access/EedsScope.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/AffiliationService.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Security/EedsExportPolicy.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/MemberLifecycle.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/DuplicatePreview.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/RenewalService.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/StructureService.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/EedsExportService.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/EedsExportRoleInitializer.php';

if (!defined('PREFIX_DB')) {
    define('PREFIX_DB', value: 'galette_');
}

/**
 * R1 fail-closed scope and export contract tests.
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
     * Member authorization requires every current active affiliation in scope.
     */
    public function testScopeRequiresEveryCurrentMemberAffiliation(): void
    {
        $result = new ResultSet();
        $result->initialize([
            ['id_structure' => 10],
            ['id_structure' => 11],
        ]);

        $select = $this->createStub(Select::class);
        $select->method('columns')->willReturnSelf();
        $conditions = [];
        $select->method('where')->willReturnCallback(
            static function (mixed $condition) use ($select, &$conditions): Select {
                $conditions[] = $condition;
                return $select;
            }
        );

        $database = $this->createMock(Db::class);
        $database->method('select')
            ->with('eeds_member_affiliations')
            ->willReturn($select);
        $database->method('execute')->willReturn($result);
        $platform = $this->createStub(PlatformInterface::class);
        $platform->method('quoteValue')->willReturnCallback(
            static fn(string $value): string => "'" . $value . "'"
        );
        $database->method('__get')->with('platform')->willReturn($platform);

        $scope = new EedsScope([10, 11]);

        $this->assertTrue($scope->allowsMember($database, 42, '2026-11-22'));
        $this->assertSame([
            'id_adh' => 42,
            'affiliation_status' => 'active',
        ], $conditions[0]);
        $this->assertSame("effective_start <= '2026-11-22'", $conditions[1]);
        $this->assertSame(
            "(effective_end IS NULL OR effective_end >= '2026-11-22')",
            $conditions[2]
        );
        $this->assertFalse((new EedsScope([10]))->allowsMember($database, 42, '2026-11-22'));
        $this->assertFalse((new EedsScope([]))->allowsMember($database, 42, '2026-11-22'));
        $this->assertFalse($scope->allowsMember($database, 0, '2026-11-22'));
    }

    /**
     * Duplicate preview never queries members without an authorized structure.
     */
    public function testDuplicatePreviewFailsClosedForEmptyScope(): void
    {
        $database = $this->createMock(Db::class);
        $database->expects($this->never())->method('select');

        $this->assertSame([], (new DuplicatePreview())->preview($database, []));
    }

    /**
     * Duplicate preview groups matching values and excludes empty identifiers.
     */
    public function testDuplicatePreviewGroupsMatchingValues(): void
    {
        $result = new ResultSet();
        $result->initialize([
            [
                'id_adh' => 2,
                'nom_adh' => 'Doe',
                'prenom_adh' => 'Sam',
                'ddn_adh' => '2000-01-01',
                'login_adh' => 'same-login',
                'email_adh' => 'sam@example.org',
                'national_number' => 'EEDS-42',
            ],
            [
                'id_adh' => 1,
                'nom_adh' => 'DOE',
                'prenom_adh' => 'SAM',
                'ddn_adh' => '2000-01-01',
                'login_adh' => 'same-login',
                'email_adh' => ' Sam@Example.org ',
                'national_number' => 'EEDS-42',
            ],
            [
                'id_adh' => 3,
                'nom_adh' => '',
                'prenom_adh' => '',
                'ddn_adh' => null,
                'login_adh' => '',
                'email_adh' => '',
                'national_number' => null,
            ],
        ]);

        $select = $this->createStub(Select::class);
        $select->method('columns')->willReturnSelf();
        $select->method('quantifier')->willReturnSelf();
        $select->method('join')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $database = $this->createMock(Db::class);
        $database->expects($this->once())
            ->method('select')
            ->with('adherents', 'a')
            ->willReturn($select);
        $database->method('execute')->willReturn($result);
        $platform = $this->createStub(PlatformInterface::class);
        $platform->method('quoteValue')->willReturnCallback(
            static fn(string $value): string => "'" . $value . "'"
        );
        $database->method('__get')->with('platform')->willReturn($platform);

        $this->assertSame([
            [
                'left_member_id' => 1,
                'right_member_id' => 2,
                'match_fields' => ['national_number', 'email', 'login', 'name_birthdate'],
                'confidence' => 'high',
            ],
        ], (new DuplicatePreview())->preview($database, [10]));
    }

    /**
     * Transfer rejects an active source affiliation outside the authorized scope.
     */
    public function testTransferRejectsOutOfScopeSourceAffiliation(): void
    {
        $result = new ResultSet();
        $result->initialize([['id_structure' => 99]]);

        $select = $this->createStub(Select::class);
        $select->method('columns')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $database = $this->createMock(Db::class);
        $database->expects($this->once())->method('beginTransaction');
        $database->expects($this->once())->method('select')
            ->with('eeds_member_affiliations')
            ->willReturn($select);
        $database->method('execute')->willReturn($result);
        $database->expects($this->once())->method('rollback');
        $database->expects($this->never())->method('update');
        $database->expects($this->never())->method('insert');
        $platform = $this->createStub(PlatformInterface::class);
        $platform->method('quoteValue')->willReturnCallback(
            static fn(string $value): string => "'" . $value . "'"
        );
        $database->method('__get')->with('platform')->willReturn($platform);

        $this->assertFalse((new AffiliationService())->transfer(
            $database,
            42,
            10,
            [10],
            null,
            null,
            null,
            '2026-11-22',
            7,
            'scope test'
        ));
    }

    /**
     * A validated renewal keeps the current status without an invalid no-op transition.
     */
    public function testValidatedRenewalKeepsAnUnchangedStatus(): void
    {
        $result = new ResultSet();
        $result->initialize([['membership_status' => 'active']]);

        $insert = $this->createStub(Insert::class);
        $insert->method('values')->willReturnSelf();
        $select = $this->createStub(Select::class);
        $select->method('columns')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $database = $this->createMock(Db::class);
        $database->expects($this->once())->method('beginTransaction');
        $database->expects($this->once())->method('insert')
            ->with('eeds_renewals')
            ->willReturn($insert);
        $database->expects($this->once())->method('select')
            ->with('eeds_member_lifecycle')
            ->willReturn($select);
        $database->expects($this->exactly(2))->method('execute')->willReturn($result);
        $database->expects($this->once())->method('commit');
        $database->expects($this->never())->method('rollback');

        $this->assertTrue((new RenewalService())->renew(
            $database,
            42,
            '2026',
            '2027',
            7,
            null,
            'active',
            true
        ));
    }

    /**
     * A failed status transition rolls the renewal record back with it.
     */
    public function testValidatedRenewalRollsBackWhenStatusTransitionFails(): void
    {
        $result = new ResultSet();
        $result->initialize([['membership_status' => 'deceased']]);

        $insert = $this->createStub(Insert::class);
        $insert->method('values')->willReturnSelf();
        $select = $this->createStub(Select::class);
        $select->method('columns')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $database = $this->createMock(Db::class);
        $database->expects($this->once())->method('beginTransaction');
        $database->expects($this->once())->method('insert')
            ->with('eeds_renewals')
            ->willReturn($insert);
        $database->expects($this->exactly(2))->method('select')
            ->with('eeds_member_lifecycle')
            ->willReturn($select);
        $database->expects($this->exactly(3))->method('execute')->willReturn($result);
        $database->method('inTransaction')->willReturn(true);
        $database->expects($this->never())->method('commit');
        $database->expects($this->once())->method('rollback');

        try {
            (new RenewalService())->renew(
                $database,
                42,
                '2026',
                '2027',
                7,
                null,
                'active',
                true
            );
            $this->fail('The invalid renewal transition should throw.');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(
                'This R1 membership transition is not allowed.',
                $e->getMessage()
            );
        } finally {
            if (isset($GLOBALS['galette_log_var'])) {
                $GLOBALS['galette_log_var'] = null;
            }
        }
    }

    /**
     * Structure creation rejects a parent that is not effective at the selected date.
     */
    public function testStructureCreationRejectsInactiveParent(): void
    {
        $empty = new ResultSet();
        $empty->initialize([]);

        $select = $this->createStub(Select::class);
        $select->method('columns')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $database = $this->createMock(Db::class);
        $database->expects($this->once())->method('beginTransaction');
        $database->expects($this->once())->method('select')
            ->with('eeds_structures')
            ->willReturn($select);
        $database->method('execute')->willReturn($empty);
        $database->expects($this->once())->method('rollback');
        $database->expects($this->never())->method('insert');
        $database->method('inTransaction')->willReturn(false);
        $platform = $this->createStub(PlatformInterface::class);
        $platform->method('quoteValue')->willReturnCallback(
            static fn(string $value): string => "'" . $value . "'"
        );
        $database->method('__get')->with('platform')->willReturn($platform);

        $this->expectException(\InvalidArgumentException::class);
        (new StructureService())->create(
            $database,
            99,
            'district',
            'DISTRICT-99',
            'District 99',
            'pending',
            '2026-11-22',
            7
        );
    }

    /**
     * National root initialization is idempotent.
     */
    public function testNationalRootInitializationReusesExistingRoot(): void
    {
        $result = new ResultSet();
        $result->initialize([['id_structure' => 1]]);

        $select = $this->createStub(Select::class);
        $select->method('columns')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $database = $this->createMock(Db::class);
        $database->expects($this->once())->method('beginTransaction');
        $database->expects($this->once())->method('select')
            ->with('eeds_structures')
            ->willReturn($select);
        $database->method('execute')->willReturn($result);
        $database->expects($this->once())->method('commit');
        $database->expects($this->never())->method('insert');

        $this->assertSame(
            1,
            (new StructureService())->ensureNationalRoot(
                $database,
                'Nation',
                'NATION',
                '2026-11-22',
                7
            )
        );
    }

    /**
     * Structure types remain restricted to the documented hierarchy.
     */
    public function testStructureTypesFollowDocumentedHierarchy(): void
    {
        $this->assertSame(
            ['national', 'region', 'district', 'groupe_local', 'unite'],
            StructureService::ALLOWED_TYPES
        );
    }

    /**
     * XLSX export fails closed without an organizational scope.
     */
    public function testExportFailsClosedWithoutScope(): void
    {
        $database = $this->createMock(Db::class);
        $database->expects($this->never())->method('select');

        $this->expectException(\InvalidArgumentException::class);
        (new EedsExportService())->export($database, 7, []);
    }

    /**
     * Export roles are limited to the four owner-approved profiles.
     */
    public function testExportRolesAreLimitedToApprovedProfiles(): void
    {
        $this->assertSame([
            'Administrateur central/national' => 'EEDS export - Administrateur central/national',
            'Commissaire de région' => 'EEDS export - Commissaire de région',
            'Commissaire de district' => 'EEDS export - Commissaire de district',
            'Commissaire de groupe local' => 'EEDS export - Commissaire de groupe local',
        ], EedsExportRoleInitializer::PROFILE_ROLES);
    }

    /**
     * Scoped XLSX export creates a workbook and records an audit row.
     */
    public function testScopedExportCreatesXlsxAndAudit(): void
    {
        $members = new ResultSet();
        $members->initialize([['nom_adh' => '=1+1']]);
        $empty = new ResultSet();
        $empty->initialize([]);

        $select = $this->createStub(Select::class);
        $select->method('columns')->willReturnSelf();
        $select->method('join')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $insert = $this->createStub(Insert::class);
        $insert->method('values')->willReturnSelf();

        $database = $this->createMock(Db::class);
        $database->expects($this->once())->method('select')
            ->willReturn($select);
        $database->expects($this->once())->method('insert')
            ->with('eeds_export_audit')
            ->willReturn($insert);
        $database->expects($this->exactly(2))->method('execute')
            ->willReturnOnConsecutiveCalls($members, $empty);
        $platform = $this->createStub(PlatformInterface::class);
        $platform->method('quoteValue')->willReturnCallback(
            static fn(string $value): string => "'" . $value . "'"
        );
        $database->method('__get')->with('platform')->willReturn($platform);

        $export = (new EedsExportService())->export($database, 7, [10]);

        try {
            $this->assertFileExists($export['path']);
            $loaded = IOFactory::load($export['path']);
            $sheet = $loaded->getActiveSheet();
            $this->assertSame('EEDS', $sheet->getTitle());
            $name_column = array_search('nom_adh', $export['fields'], strict: true);
            $this->assertIsInt($name_column);
            $this->assertSame(
                DataType::TYPE_STRING,
                $sheet->getCell([$name_column + 1, 2])->getDataType()
            );
            $this->assertSame('=1+1', $sheet->getCell([$name_column + 1, 2])->getValue());
            $loaded->disconnectWorksheets();
            $this->assertSame(1, $export['row_count']);
            $this->assertContains('national_number', $export['fields']);
        } finally {
            unlink($export['path']);
        }
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
