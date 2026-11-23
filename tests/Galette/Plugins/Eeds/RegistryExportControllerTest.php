<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace Galette\Tests\Plugins\Eeds;

use EEDSFoundation\Controllers\RegistryController;
use Galette\Access\AccessControl;
use Galette\Entity\Adherent;
use Galette\Tests\GaletteRoutingTestCase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Slim\Views\Twig;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use function Safe\file_put_contents;
use function Safe\spl_autoload_register;
use function Safe\tempnam;
use function Safe\unlink;

spl_autoload_register(static function (string $class): void {
    if (!str_starts_with($class, 'EEDSFoundation\\')) {
        return;
    }

    $file = GALETTE_ROOT . 'plugins/eeds/lib/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

/**
 * HTTP response and scope checks for the EEDS XLSX endpoint.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
class RegistryExportControllerTest extends GaletteRoutingTestCase
{
    protected int $seed = 20261123123000;

    /**
     * The endpoint streams a scoped XLSX file with download-safe headers.
     */
    public function testExportStreamsScopedWorkbook(): void
    {
        if (!$this->hasEedsSchema()) {
            $this->markTestSkipped('An installed EEDS plugin is required.');
        }

        [$member, $structure_id] = $this->createScopedMember();
        $controller = $this->controllerWithPermission(granted: true);

        $response = $controller->export(
            $this->createExportRequest(),
            new Response()
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->getHeaderLine('Content-Type')
        );
        $this->assertSame('attachment; filename="eeds-export.xlsx"', $response->getHeaderLine('Content-Disposition'));
        $this->assertSame('no-store, private', $response->getHeaderLine('Cache-Control'));
        $this->assertStringStartsWith('PK', (string)$response->getBody());

        $path = tempnam(sys_get_temp_dir(), 'eeds-http-');
        try {
            file_put_contents($path, (string)$response->getBody());
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $headings = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];
            $login_column = array_search('login_adh', $headings, strict: true);
            $this->assertIsInt($login_column);
            $this->assertSame($this->dataAdherentOne()['login_adh'], $sheet->getCell([$login_column + 1, 2])->getValue());
            $this->assertContains($structure_id, $this->scopeFor($member->id));
            $spreadsheet->disconnectWorksheets();
        } finally {
            unlink($path);
        }
    }

    /**
     * A granted Galette permission does not replace the required EEDS scope.
     */
    public function testExportDeniesActorWithoutEedsScope(): void
    {
        if (!$this->hasEedsSchema()) {
            $this->markTestSkipped('An installed EEDS plugin is required.');
        }

        $this->getMemberOne();
        $data = $this->dataAdherentOne();
        $this->assertTrue($this->login->login($data['login_adh'], $data['mdp_adh']));

        $response = $this->controllerWithPermission(granted: true)->export(
            $this->createExportRequest(),
            new Response()
        );

        $this->assertSame(403, $response->getStatusCode());
    }

    /**
     * A denied Galette permission prevents generation, even with an EEDS scope.
     */
    public function testExportDeniesActorWithoutGalettePermission(): void
    {
        if (!$this->hasEedsSchema()) {
            $this->markTestSkipped('An installed EEDS plugin is required.');
        }

        [$member] = $this->createScopedMember();

        $response = $this->controllerWithPermission(granted: false)->export(
            $this->createExportRequest(),
            new Response()
        );

        $this->assertSame(403, $response->getStatusCode());
        $audit = $this->zdb->select('eeds_export_audit');
        $audit->where(['actor_id' => $member->id]);
        $this->assertCount(0, $this->zdb->execute($audit));
    }

    /**
     * The registry omits the export action without an export scope.
     */
    public function testRegistryHidesExportWithoutExportScope(): void
    {
        if (!$this->hasEedsSchema()) {
            $this->markTestSkipped('An installed EEDS plugin is required.');
        }

        [$member] = $this->createScopedMember(['eeds:read']);
        $access_control = $this->createStub(AccessControl::class);
        $access_control->method('can')->willReturn(true);
        $this->container->set(AccessControl::class, $access_control);

        $view = $this->createMock(Twig::class);
        $view->expects($this->once())
            ->method('render')
            ->with(
                $this->anything(),
                '@PluginGaletteEeds/eeds_registry.html.twig',
                $this->callback(static fn(array $data): bool => $data['can_export'] === false)
            )
            ->willReturnArgument(0);
        $this->container->set(Twig::class, $view);

        $response = $this->container->make(RegistryController::class)->registry(
            $this->createPsrRequest('GET', '/plugins/eeds/'),
            new Response()
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertGreaterThan(0, $member->id);
    }

    /**
     * @param list<string> $permissions
     * @return array{Adherent, int}
     */
    private function createScopedMember(array $permissions = ['eeds:export']): array
    {
        $member = $this->getMemberOne();
        $data = $this->dataAdherentOne();
        $this->assertTrue($this->login->login($data['login_adh'], $data['mdp_adh']));

        $root = $this->zdb->select('eeds_structures');
        $root->columns(['id_structure'])->where(['structure_type' => 'national']);
        $root_row = $this->zdb->execute($root)->current();
        $this->assertNotFalse($root_row);
        $structure_id = (int)$root_row->id_structure;

        $now = date('Y-m-d');
        foreach ($permissions as $permission) {
            $scope = $this->zdb->insert('eeds_member_scopes');
            $scope->values([
                'id_adh' => $member->id,
                'id_structure' => $structure_id,
                'scope_permission' => $permission,
                'effective_start' => '2020-01-01',
                'created_at' => $now . ' 00:00:00',
            ]);
            $this->zdb->execute($scope);
        }

        $affiliation = $this->zdb->insert('eeds_member_affiliations');
        $affiliation->values([
            'id_adh' => $member->id,
            'id_structure' => $structure_id,
            'affiliation_status' => 'active',
            'effective_start' => '2020-01-01',
            'created_at' => $now . ' 00:00:00',
        ]);
        $this->zdb->execute($affiliation);

        return [$member, $structure_id];
    }

    /**
     * @return list<int>
     */
    private function scopeFor(int $member_id): array
    {
        return \EEDSFoundation\Access\EedsScope::forMember(
            $this->zdb,
            $member_id,
            'eeds:export'
        );
    }

    /**
     * Create controller with a controlled Galette permission result.
     */
    private function controllerWithPermission(bool $granted): RegistryController
    {
        $access_control = $this->createStub(AccessControl::class);
        $access_control->method('can')->willReturn($granted);
        $this->container->set(AccessControl::class, $access_control);

        return $this->container->make(RegistryController::class);
    }

    /**
     * Create the POST request expected by the XLSX controller.
     */
    private function createExportRequest(): Request
    {
        return $this->createPsrRequest('POST', '/plugins/eeds/export');
    }

    /**
     * Create a PSR-7 request for a controller route.
     */
    private function createPsrRequest(string $method, string $path): Request
    {
        return new Request(
            method: $method,
            uri: (new UriFactory())->createUri($path),
            headers: new Headers(['Content-Type' => ['text/html']]),
            cookies: [],
            serverParams: [],
            body: (new StreamFactory())->createStream()
        );
    }

    /**
     * Verify the EEDS tables needed by export exist.
     */
    private function hasEedsSchema(): bool
    {
        return $this->zdb->tableExists('eeds_member_affiliations')
            && $this->zdb->tableExists('eeds_member_scopes')
            && $this->zdb->tableExists('eeds_export_audit');
    }
}
