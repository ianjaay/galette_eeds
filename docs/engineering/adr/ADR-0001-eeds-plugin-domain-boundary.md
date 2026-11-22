# Technical design / ADR

- **Decision ID:** ADR-0001
- **Title:** EEDS Galette plugin/domain boundary
- **Status:** APPROVED
- **Date:** 2026-11-22
- **Decision owner:** Project owner

## Context

The current checkout is Galette PHP 8.3+, Slim, PHP-DI, Laminas DB and Twig.
It contains reusable member, group, status, contribution, role, document,
history and plugin capabilities. It does not contain an EEDS domain model.

The dossier remains a business reference, not an approved specification.
Several proposed workflows involve youth data and require safeguarding,
privacy and authorization decisions before implementation.

## Problem

EEDS has domain concepts that do not map safely to Galette's generic group,
status and role entities. An isolated plugin avoids imposing those concepts
on unrelated Galette installations, but it must remain integrated with
Galette identity and authorization.

## Requirements and constraints

- Preserve Galette core identity and session handling.
- Preserve Galette plugin discovery and upgrade mechanisms.
- Support MySQL/MariaDB and PostgreSQL.
- Enforce permission and organizational scope at route, object and list levels.
- Keep health, incident and youth data out of scope until separately approved.
- Preserve member and affiliation history.
- Avoid broad core refactoring.

## Options considered

| Option | Benefits | Costs and risks |
|---|---|---|
| Extensive Galette core changes | One domain model and one UI | High maintenance; imposes EEDS rules on Galette core |
| **Scoped plugin/domain extension** | Isolation, reuse and upgrade separation | Integration contracts and plugin migrations must be maintained |
| Separate application/service | Full domain independence | Identity duplication, synchronization and deployment complexity |
| Dynamic fields and generic groups only | Fast initial customization | Cannot enforce lifecycle, mandate, delegation or safeguarding invariants |

## Decision

Use a scoped Galette plugin/domain extension for EEDS-specific entities and
workflows. This decision was approved on 2026-11-22.

The plugin should reuse Galette member IDs and core authentication, sessions,
RBAC, repositories, UI and plugin infrastructure. It must not duplicate a
second person identity unless the project owner separately approves that
model.

## Component boundaries and data flow

- Galette core owns login, session and baseline member identity.
- The plugin owns EEDS structures, affiliations and domain workflows.
- The plugin resolves both plugin permissions and EEDS organizational scope.
- Stable Galette member IDs connect domain appointments and affiliations.
- Plugin routes use existing attribute or `_routes.php` conventions.
- Plugin permissions extend the Galette permission catalog.

## Data model, permissions, and migration

- Plugin-owned tables must use MySQL/MariaDB and PostgreSQL migration scripts.
- Each table must have explicit stable IDs and effective-dated history where
  applicable.
- Mandates must remain distinct from broad RBAC role assignments.
- Minor and guardian relationships require separate safeguarding review.
- Authorization must fail closed for routes, list queries, objects and exports.
- Exports require a domain field/row allowlist and an audit record.

## Consequences

The EEDS plugin will require explicit integration contracts with Galette.
Core changes should be limited to general plugin authorization or migration
capabilities that all plugins can use safely.

## Risks and mitigations

| Risk | Mitigation |
|---|---|
| Plugin and core models diverge | Stable IDs and focused integration tests |
| Plugin permissions become overly broad | Permission plus resolved organizational scope |
| Plugin migrations corrupt existing history | Additive migration, backup and dual-engine validation |
| Youth data escapes scope | Exclude sensitive fields until separate approval |

## Validation and rollback

R0 requires an architecture spike and documentation only. It must not migrate
real data or implement EEDS workflows. Future implementation will require
fresh-install, repeat-upgrade and rollback evidence on both supported engines.

## References

- [EEDS project context](../../product/project-context.md)
- [R0 release specification](../../implementation/release-specifications/release-00-foundation.md)
- [Plugin definition API](../../../galette/lib/Galette/Core/Plugins/PluginDefinition.php)
- [Plugin route loader](../../../galette/lib/Galette/Core/Routing/RouteLoader.php)
