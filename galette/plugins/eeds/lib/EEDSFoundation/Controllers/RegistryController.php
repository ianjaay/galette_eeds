<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Controllers;

use Safe\DateTimeImmutable;
use EEDSFoundation\Domain\AffiliationService;
use EEDSFoundation\Domain\DuplicatePreview;
use EEDSFoundation\Domain\MemberLifecycle;
use EEDSFoundation\Domain\RegistryRepository;
use EEDSFoundation\Domain\RenewalService;
use EEDSFoundation\Domain\StructureService;
use EEDSFoundation\Domain\ExportService;
use EEDSFoundation\Access\EedsScope;
use Galette\Controllers\Attributes\Route;
use Galette\Controllers\AbstractController;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Request;

use function Safe\json_encode;

/**
 * R1 EEDS registry controller.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
class RegistryController extends AbstractController
{
    /**
     * Render the R1 registry overview.
     */
    #[Route(
        name: 'eeds_registry',
        pattern: '/',
        methods: ['GET']
    )]
    public function registry(Request $request, Response $response): Response
    {
        if (!$this->accessControl->can('eeds:read')) {
            return $response->withStatus(403);
        }

        $scope_ids = EedsScope::forMember(
            $this->zdb,
            (int)$this->login->id,
            'eeds:read'
        );
        if ($scope_ids === []) {
            return $response->withStatus(403);
        }
        $scope = new EedsScope($scope_ids);

        $repository = new RegistryRepository();
        $structures = $repository->structures($this->zdb);
        $affiliations = $repository->affiliations($this->zdb);
        $structures = array_values(array_filter(
            $structures,
            fn(array $structure): bool => $scope->allowsStructure((int)$structure['id_structure'])
        ));
        $affiliations = array_values(array_filter(
            $affiliations,
            fn(array $affiliation): bool => $scope->allowsStructure((int)$affiliation['id_structure'])
        ));

        $this->view->render(
            $response,
            '@PluginGaletteEeds/eeds_registry.html.twig',
            [
                'page_title' => _T('EEDS registry'),
                'structures' => $structures,
                'affiliations' => $affiliations,
            ]
        );
        return $response;
    }

    /**
     * Create an EEDS structure.
     */
    #[Route(
        name: 'eeds_structures_create',
        pattern: '/structures',
        methods: ['POST']
    )]
    public function createStructure(Request $request, Response $response): Response
    {
        if (!$this->accessControl->can('eeds:structure-create')) {
            return $response->withStatus(403);
        }

        $post = $request->getParsedBody();
        if (!is_array($post)) {
            $post = [];
        }

        $service = new StructureService();
        $id = $service->create(
            $this->zdb,
            isset($post['parent_structure']) ? (int)$post['parent_structure'] : null,
            (string)($post['structure_type'] ?? ''),
            (string)($post['structure_code'] ?? ''),
            (string)($post['structure_name'] ?? ''),
            (string)($post['recognition_status'] ?? 'pending'),
            (string)($post['effective_start'] ?? date('Y-m-d')),
            (int)$this->login->id
        );

        $payload = json_encode(['success' => true, 'id_structure' => $id], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    /**
     * Transition a member lifecycle state.
     */
    #[Route(
        name: 'eeds_status_transition',
        pattern: '/members/{id:\d+}/status',
        methods: ['POST']
    )]
    public function transitionStatus(Request $request, Response $response, int $id): Response
    {
        if (!$this->accessControl->can('eeds:validate')) {
            return $response->withStatus(403);
        }

        $post = $request->getParsedBody();
        if (!is_array($post)) {
            $post = [];
        }

        $ok = (new MemberLifecycle())->transition(
            $this->zdb,
            $id,
            (string)($post['status'] ?? ''),
            (string)($post['effective_date'] ?? date('Y-m-d')),
            (int)$this->login->id,
            (string)($post['reason'] ?? ''),
            isset($post['evidence_reference']) ? (string)$post['evidence_reference'] : null
        );

        $payload = json_encode(['success' => $ok], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Transfer a member to a different structure.
     */
    #[Route(
        name: 'eeds_transfer',
        pattern: '/members/{id:\d+}/transfer',
        methods: ['POST']
    )]
    public function transfer(Request $request, Response $response, int $id): Response
    {
        if (!$this->accessControl->can('eeds:validate')) {
            return $response->withStatus(403);
        }

        $post = $request->getParsedBody();
        if (!is_array($post)) {
            $post = [];
        }

        $ok = (new AffiliationService())->transfer(
            $this->zdb,
            $id,
            (int)($post['structure_id'] ?? 0),
            isset($post['branch']) ? (string)$post['branch'] : null,
            isset($post['unit']) ? (string)$post['unit'] : null,
            isset($post['subgroup']) ? (string)$post['subgroup'] : null,
            (string)($post['effective_date'] ?? date('Y-m-d')),
            (int)$this->login->id,
            (string)($post['reason'] ?? ''),
            isset($post['evidence_reference']) ? (string)$post['evidence_reference'] : null
        );

        $payload = json_encode(['success' => $ok], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Record a membership renewal event.
     */
    #[Route(
        name: 'eeds_renewal',
        pattern: '/members/{id:\d+}/renew',
        methods: ['POST']
    )]
    public function renew(Request $request, Response $response, int $id): Response
    {
        if (!$this->accessControl->can('eeds:validate')) {
            return $response->withStatus(403);
        }

        $post = $request->getParsedBody();
        if (!is_array($post)) {
            $post = [];
        }

        $ok = (new RenewalService())->renew(
            $this->zdb,
            $id,
            (string)($post['source_period'] ?? ''),
            (string)($post['target_period'] ?? ''),
            (int)$this->login->id,
            isset($post['fee_status_reference']) ? (string)$post['fee_status_reference'] : null,
            (string)($post['resulting_status'] ?? 'active'),
            isset($post['validated']) && (bool)$post['validated']
        );

        $payload = json_encode(['success' => $ok], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Preview duplicate member candidates without merging identities.
     */
    #[Route(
        name: 'eeds_duplicates',
        pattern: '/duplicates',
        methods: ['GET']
    )]
    public function duplicates(Request $request, Response $response): Response
    {
        if (!$this->accessControl->can('eeds:read')) {
            return $response->withStatus(403);
        }

        $payload = json_encode(
            ['success' => true, 'candidates' => (new DuplicatePreview())->preview($this->zdb)],
            JSON_UNESCAPED_UNICODE
        );
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Review a duplicate candidate without merging identities.
     */
    #[Route(
        name: 'eeds_duplicate_review',
        pattern: '/duplicates/{id:\d+}/review',
        methods: ['POST']
    )]
    public function reviewDuplicate(Request $request, Response $response, int $id): Response
    {
        if (!$this->accessControl->can('eeds:validate')) {
            return $response->withStatus(403);
        }

        $post = $request->getParsedBody();
        if (!is_array($post)) {
            $post = [];
        }
        $status = in_array(
            (string)($post['status'] ?? ''),
            ['confirmed', 'dismissed'],
            strict: true
        ) ? (string)$post['status'] : '';
        if ($status === '') {
            return $response->withStatus(400);
        }

        $update = $this->zdb->update('eeds_duplicate_candidates');
        $update->set([
            'candidate_status' => $status,
            'reviewer_id' => (int)$this->login->id,
            'reviewed_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);
        $update->where(['id_candidate' => $id]);
        $this->zdb->execute($update);

        $payload = json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Issue or withdraw an optional external national member number.
     */
    #[Route(
        name: 'eeds_member_number',
        pattern: '/members/{id:\d+}/number',
        methods: ['POST']
    )]
    public function memberNumber(Request $request, Response $response, int $id): Response
    {
        if (!$this->accessControl->can('eeds:validate')) {
            return $response->withStatus(403);
        }

        $post = $request->getParsedBody();
        if (!is_array($post)) {
            $post = [];
        }

        $number = trim((string)($post['national_number'] ?? ''));
        if ($number === '') {
            $update = $this->zdb->update('eeds_member_numbers');
            $update->set(['withdrawn_at' => (new DateTimeImmutable())->format('Y-m-d')]);
            $update->where(['id_adh' => $id]);
            $this->zdb->execute($update);
            $success = true;
        } else {
            $select = $this->zdb->select('eeds_member_numbers');
            $select->columns(['id_adh'])->where(['id_adh' => $id]);
            $exists = $this->zdb->execute($select)->count() > 0;
            if ($exists) {
                $update = $this->zdb->update('eeds_member_numbers');
                $update->set([
                    'national_number' => $number,
                    'issued_at' => (new DateTimeImmutable())->format('Y-m-d'),
                    'withdrawn_at' => null,
                ]);
                $update->where(['id_adh' => $id]);
                $this->zdb->execute($update);
            } else {
                $insert = $this->zdb->insert('eeds_member_numbers');
                $insert->values([
                    'id_adh' => $id,
                    'national_number' => $number,
                    'issued_at' => (new DateTimeImmutable())->format('Y-m-d'),
                    'withdrawn_at' => null,
                ]);
                $this->zdb->execute($insert);
            }
            $success = true;
        }

        $payload = json_encode(['success' => $success], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Record an allowed EEDS export.
     */
    #[Route(
        name: 'eeds_export',
        pattern: '/export',
        methods: ['POST']
    )]
    public function export(Request $request, Response $response): Response
    {
        if (!$this->accessControl->can('eeds:export')) {
            return $response->withStatus(403);
        }

        $post = $request->getParsedBody();
        if (!is_array($post)) {
            $post = [];
        }

        $fields = isset($post['fields']) && is_array($post['fields'])
            ? array_values(array_map('strval', $post['fields']))
            : [];
        $structure_ids = isset($post['structure_ids']) && is_array($post['structure_ids'])
            ? array_values(array_map('intval', $post['structure_ids']))
            : [];
        $policy = new \EEDSFoundation\Security\EedsExportPolicy(
            ['id', 'name', 'structure_id'],
            $structure_ids
        );
        if (!$policy->allows($fields, $structure_ids)) {
            return $response->withStatus(403);
        }

        $ok = (new ExportService())->record(
            $this->zdb,
            (int)$this->login->id,
            $fields,
            $structure_ids,
            isset($post['row_count']) ? (int)$post['row_count'] : 0
        );
        $payload = json_encode(['success' => $ok], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
