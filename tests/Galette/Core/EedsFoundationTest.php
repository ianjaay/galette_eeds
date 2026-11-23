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
use EEDSFoundation\Domain\StructureGroupMapper;
use EEDSFoundation\Domain\EedsExportService;
use EEDSFoundation\Security\EedsExportPolicy;
use EEDSFoundation\Domain\MemberLifecycle;
use EEDSFoundation\PluginGaletteEeds;
use Galette\Core\Db;
use Galette\Core\Authentication;
use Galette\Core\Login;
use Galette\Defaults\MembersFields;
use Galette\Defaults\MembersFieldsCategories;
use Galette\Entity\Adherent;
use Galette\Entity\FieldsConfig;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Select;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;

use function Safe\define;
use function Safe\json_decode;
use function Safe\unlink;

require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Access/EedsScope.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/AffiliationService.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Security/EedsExportPolicy.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/MemberLifecycle.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/DuplicatePreview.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/RenewalService.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/StructureService.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/StructureGroupMapper.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/EedsExportService.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/Domain/EedsExportRoleInitializer.php';
require_once dirname(__DIR__, 3) . '/galette/plugins/eeds/lib/EEDSFoundation/PluginGaletteEeds.php';

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
        $database->method('inTransaction')->willReturnOnConsecutiveCalls(false, true);
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
        $result->initialize([['id_structure' => 1, 'parent_structure' => null]]);

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
     * National root initialization leaves an enclosing transaction to its caller.
     */
    public function testNationalRootInitializationPreservesCallerTransaction(): void
    {
        $result = new ResultSet();
        $result->initialize([['id_structure' => 1, 'parent_structure' => null]]);

        $select = $this->createStub(Select::class);
        $select->method('columns')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $database = $this->createMock(Db::class);
        $database->method('inTransaction')->willReturn(true);
        $database->expects($this->never())->method('beginTransaction');
        $database->expects($this->once())->method('select')
            ->with('eeds_structures')
            ->willReturn($select);
        $database->method('execute')->willReturn($result);
        $database->expects($this->never())->method('commit');
        $database->expects($this->never())->method('rollback');
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
     * Group synchronization preview lists EEDS structures parent-first.
     */
    public function testGroupMappingPreviewOrdersStructuresParentFirst(): void
    {
        $structures = new ResultSet();
        $structures->initialize([
            [
                'id_structure' => 5,
                'parent_structure' => 4,
                'structure_name' => 'Synthetic Unit',
                'structure_type' => 'unite',
            ],
            [
                'id_structure' => 3,
                'parent_structure' => 2,
                'structure_name' => 'Synthetic District',
                'structure_type' => 'district',
            ],
            [
                'id_structure' => 1,
                'parent_structure' => null,
                'structure_name' => 'Nation',
                'structure_type' => 'national',
            ],
            [
                'id_structure' => 4,
                'parent_structure' => 3,
                'structure_name' => 'Synthetic Local Group',
                'structure_type' => 'groupe_local',
            ],
            [
                'id_structure' => 2,
                'parent_structure' => 1,
                'structure_name' => 'Synthetic Region',
                'structure_type' => 'region',
            ],
        ]);
        $empty = new ResultSet();
        $empty->initialize([]);

        $select = $this->createStub(Select::class);
        $select->method('columns')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $database = $this->createStub(Db::class);
        $database->method('select')->willReturn($select);
        $execute_count = 0;
        $database->method('execute')->willReturnCallback(
            static function () use (&$execute_count, $structures, $empty): ResultSet {
                return $execute_count++ === 0 ? $structures : $empty;
            }
        );

        $messages = (new StructureGroupMapper())->preview($database);
        $this->assertSame(
            [
                'Structure #1 "Nation" would create a Galette group under its mapped EEDS parent.',
                'Structure #2 "Synthetic Region" would create a Galette group under its mapped EEDS parent.',
                'Structure #3 "Synthetic District" would create a Galette group under its mapped EEDS parent.',
                'Structure #4 "Synthetic Local Group" would create a Galette group under its mapped EEDS parent.',
                'Structure #5 "Synthetic Unit" would create a Galette group under its mapped EEDS parent.',
            ],
            $messages
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
     * EEDS permissions cannot be limited to Galette groups without an approved mapping.
     */
    public function testEedsPermissionsRejectGaletteGroupAssignments(): void
    {
        $plugin = new PluginGaletteEeds($this->createStub(Db::class));

        foreach ($plugin->getPermissions() as $permission) {
            $this->assertFalse($permission->hasGroupScope(), $permission->name);
        }
    }

    /**
     * XLSX export fails closed without an organizational scope.
     */
    public function testExportFailsClosedWithoutScope(): void
    {
        $database = $this->createMock(Db::class);
        $database->expects($this->never())->method('select');

        $this->expectException(\InvalidArgumentException::class);
        (new EedsExportService())->export(
            $database,
            $this->createStub(Login::class),
            $this->createStub(FieldsConfig::class),
            []
        );
    }

    /**
     * A missing EEDS table must make the plugin eligible for installation.
     */
    public function testPluginDetectsMissingDatabase(): void
    {
        $database = $this->createMock(Db::class);
        $database->expects($this->once())->method('getTables')->willReturn([]);

        $this->assertFalse((new PluginGaletteEeds($database))->isInstalled());
    }

    /**
     * An unrelated existing role must not acquire export rights by name alone.
     */
    public function testExportRoleInitializationRejectsNameCollision(): void
    {
        $role = new ResultSet();
        $role->initialize([['id_role' => 42]]);
        $empty = new ResultSet();
        $empty->initialize([]);

        $select = $this->createStub(Select::class);
        $select->method('columns')->willReturnSelf();
        $select->method('where')->willReturnSelf();
        $database = $this->createMock(Db::class);
        $database->method('select')->willReturn($select);
        $database->method('execute')->willReturnOnConsecutiveCalls($role, $empty);
        $database->method('inTransaction')->willReturnOnConsecutiveCalls(false, true);
        $database->expects($this->once())->method('rollback');
        $database->expects($this->never())->method('insert');

        try {
            (new EedsExportRoleInitializer())->initialize($database);
            $this->fail('An existing role without the expected permissions must be reviewed.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('review it before initialization', $e->getMessage());
        } finally {
            if (isset($GLOBALS['galette_log_var'])) {
                $GLOBALS['galette_log_var'] = null;
            }
        }
    }

    /**
     * Scoped XLSX export respects field visibility and records the actual columns.
     */
    #[DataProvider('exportAccessLevels')]
    public function testScopedExportCreatesXlsxAndAudit(int $access_level, bool $info_visible): void
    {
        $members = new ResultSet();
        $members->initialize([['nom_adh' => '=1+1', 'info_adh' => 'restricted']]);
        $empty = new ResultSet();
        $empty->initialize([]);

        $select = $this->createMock(Select::class);
        $expected_columns = $info_visible ? ['nom_adh', 'info_adh', 'gpgid'] : ['nom_adh', 'gpgid'];
        $select->expects($this->once())->method('columns')->with($expected_columns)->willReturnSelf();
        $select->method('join')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $audit_fields = null;
        $insert = $this->createMock(Insert::class);
        $insert->expects($this->once())->method('values')->with($this->callback(
            static function (array $values) use (&$audit_fields): bool {
                $audit_fields = json_decode($values['export_fields'], true);
                return true;
            }
        ))->willReturnSelf();

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

        $login = $this->createStub(Login::class);
        $login->method('__get')->willReturn(7);
        $login->method('getAccessLevel')->willReturn($access_level);
        $fields_config = $this->createStub(FieldsConfig::class);
        $fields_config->method('isVisibleFor')->willReturnCallback(
            static fn(string $field, int $level): bool => FieldsConfig::isGranted(
                match ($field) {
                    'nom_adh', 'gpgid', 'fingerprint' => FieldsConfig::USER_WRITE,
                    'info_adh' => FieldsConfig::STAFF,
                    default => FieldsConfig::NOBODY,
                },
                $level
            )
        );
        $export = (new EedsExportService())->export($database, $login, $fields_config, [10]);

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
            $this->assertSame(1, $export['row_count']);
            $this->assertContains('national_number', $export['fields']);
            $this->assertContains('gpgid', $export['fields']);
            $this->assertSame($info_visible, in_array('info_adh', $export['fields'], strict: true));
            $this->assertNotContains('fingerprint', $export['fields']);
            $this->assertNotContains('mdp_adh', $export['fields']);
            $this->assertSame($export['fields'], $audit_fields);
            $this->assertSame($export['fields'], $sheet->rangeToArray(
                'A1:' . $sheet->getHighestColumn() . '1',
                null,
                true,
                true,
                false
            )[0]);
            $loaded->disconnectWorksheets();
        } finally {
            unlink($export['path']);
        }
    }

    /**
     * @return array<string, array{int, bool}>
     */
    public static function exportAccessLevels(): array
    {
        return [
            'ordinary user' => [Authentication::ACCESS_USER, false],
            'staff' => [Authentication::ACCESS_STAFF, true],
        ];
    }

    /**
     * Exercise scope, duplicate lookup and XLSX export using synthetic members.
     */
    public function testScopedExportAndDuplicatesAgainstDatabase(): void
    {
        global $zdb;
        if (!$zdb instanceof Db || !$zdb->tableExists('eeds_member_affiliations')) {
            $this->markTestSkipped('An installed EEDS plugin is required.');
        }

        $suffix = bin2hex(random_bytes(6));
        $zdb->beginTransaction();
        $path = null;
        try {
            $root = $zdb->select('eeds_structures');
            $root->columns(['id_structure'])->where(['structure_type' => 'national']);
            $root_row = $zdb->execute($root)->current();
            $this->assertNotFalse($root_row);
            $root_id = (int)$root_row->id_structure;

            $structures = [];
            foreach (['inside', 'outside', 'child'] as $label) {
                $code = 'TEST-' . $label . '-' . $suffix;
                $insert = $zdb->insert('eeds_structures');
                $insert->values([
                    'parent_structure' => $label === 'child' ? $structures['inside'] : $root_id,
                    'structure_type' => $label === 'child' ? 'district' : 'region',
                    'structure_code' => $code,
                    'structure_name' => 'Synthetic ' . $label,
                    'recognition_status' => 'recognized',
                    'effective_start' => '2020-01-01',
                    'created_at' => '2020-01-01 00:00:00',
                ]);
                $zdb->execute($insert);
                $select = $zdb->select('eeds_structures');
                $select->columns(['id_structure'])->where(['structure_code' => $code]);
                $structures[$label] = (int)$zdb->execute($select)->current()->id_structure;
            }

            $status = $zdb->select('statuts');
            $status->columns(['id_statut'])->limit(1);
            $status_row = $zdb->execute($status)->current();
            $this->assertNotFalse($status_row);
            $members = [];
            foreach (['first', 'second', 'outside', 'expired'] as $label) {
                $login = 'synthetic-' . $label . '-' . $suffix;
                $insert = $zdb->insert('adherents');
                $insert->values([
                    'id_statut' => (int)$status_row->id_statut,
                    'nom_adh' => '=1+1',
                    'prenom_adh' => 'Synthetic',
                    'sexe_adh' => 0,
                    'adresse_adh' => '',
                    'email_adh' => $suffix . '@example.invalid',
                    'login_adh' => $login,
                    'mdp_adh' => 'not-a-real-password',
                    'date_crea_adh' => '2020-01-01',
                    'date_modif_adh' => '2020-01-01',
                ]);
                $zdb->execute($insert);
                $select = $zdb->select('adherents');
                $select->columns(['id_adh'])->where(['login_adh' => $login]);
                $members[$label] = (int)$zdb->execute($select)->current()->id_adh;

                $insert = $zdb->insert('eeds_member_affiliations');
                $insert->values([
                    'id_adh' => $members[$label],
                    'id_structure' => $structures[match ($label) {
                        'outside' => 'outside',
                        'second' => 'child',
                        default => 'inside',
                    }],
                    'affiliation_status' => 'active',
                    'effective_start' => '2020-01-01',
                    'effective_end' => $label === 'expired' ? '2020-01-02' : null,
                    'created_at' => '2020-01-01 00:00:00',
                ]);
                $zdb->execute($insert);
            }

            $insert = $zdb->insert('eeds_member_scopes');
            $insert->values([
                'id_adh' => $members['first'],
                'id_structure' => $structures['inside'],
                'scope_permission' => 'eeds:export',
                'effective_start' => '2020-01-01',
                'created_at' => '2020-01-01 00:00:00',
            ]);
            $zdb->execute($insert);

            $scope = EedsScope::forMember($zdb, $members['first'], 'eeds:export');
            $this->assertSame([$structures['inside'], $structures['child']], $scope);
            $candidates = (new DuplicatePreview())->preview($zdb, $scope);
            $this->assertCount(1, $candidates);
            $this->assertSame($members['first'], $candidates[0]['left_member_id']);
            $this->assertSame($members['second'], $candidates[0]['right_member_id']);

            $login = $this->createStub(Login::class);
            $login->method('__get')->willReturn($members['first']);
            $login->method('getAccessLevel')->willReturn(Authentication::ACCESS_MANAGER);
            $fields_config = new FieldsConfig(
                $zdb,
                Adherent::TABLE,
                MembersFields::get(),
                MembersFieldsCategories::get()
            );
            $export = (new EedsExportService())->export($zdb, $login, $fields_config, $scope);
            $path = $export['path'];
            $this->assertSame(2, $export['row_count']);
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $login_column = array_search('login_adh', $export['fields'], strict: true);
            $name_column = array_search('nom_adh', $export['fields'], strict: true);
            $this->assertIsInt($login_column);
            $this->assertIsInt($name_column);
            $exported_logins = [
                $sheet->getCell([$login_column + 1, 2])->getValue(),
                $sheet->getCell([$login_column + 1, 3])->getValue(),
            ];
            sort($exported_logins);
            $this->assertSame(['synthetic-first-' . $suffix, 'synthetic-second-' . $suffix], $exported_logins);
            $this->assertSame('=1+1', $sheet->getCell([$name_column + 1, 2])->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell([$name_column + 1, 2])->getDataType());
            $this->assertNotContains('info_adh', $export['fields']);
            $this->assertNotContains('id_adh', $export['fields']);
            $this->assertContains('gpgid', $export['fields']);
            $this->assertNotContains('fingerprint', $export['fields']);
            $spreadsheet->disconnectWorksheets();

            $audit = $zdb->select('eeds_export_audit');
            $audit->where(['actor_id' => $members['first']]);
            $audit_row = $zdb->execute($audit)->current();
            $this->assertNotFalse($audit_row);
            $this->assertSame(2, (int)$audit_row->row_count);
            $this->assertSame($scope, json_decode((string)$audit_row->structure_ids, true));
            $this->assertSame($export['fields'], json_decode((string)$audit_row->export_fields, true));
        } finally {
            try {
                if ($path !== null && file_exists($path)) {
                    unlink($path);
                }
            } finally {
                $zdb->rollback();
            }
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
