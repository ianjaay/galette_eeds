# Repository audit

## Mise à jour au 2026-11-22

Cet audit initial est historique : il décrivait l'état antérieur à l'ajout du
plugiciel EEDS. La vérification actuelle constate un plugiciel EEDS R1,
des tables MySQL/PostgreSQL, des services et une route de registre. Ces
fondations ne constituent ni une UAT complète ni une implémentation approuvée
de fonctions, mandats ou profils EEDS.

## Executive summary

This checkout is a customized repository based on Galette, a PHP membership-management application. The checked-in implementation is Galette, not the Frappe/ERPNext application mentioned as historical context in the EEDS dossier. An EEDS plugin is now present as R1 technical foundation, while mandates and full organizational profiles remain out of scope. The EEDS dossier is the current product reference.

The existing application already provides member, group, membership-fee, contribution, transaction, mailing, document, role, and plugin capabilities. Extend these capabilities only after mapping the EEDS requirement to the current domain and confirming that Galette's model fits. Do not equate the dossier's proposed EEDS objects with implemented application features.

## Technology stack

- **Backend:** PHP 8.3+; Slim 4; PHP-DI; PSR-7; Laminas DB; Twig.
- **Database:** MySQL/MariaDB and PostgreSQL, with both database platforms maintained.
- **Frontend:** Twig-rendered pages, JavaScript, Fomantic UI/Semantic UI, CSS; Gulp builds source assets into `galette/webroot/`.
- **Dependencies:** Composer, with vendor packages in `galette/vendor/`; npm, with the lockfile at the repository root.
- **Tests and quality:** PHPUnit, Playwright, PHPStan, PHP-CS-Fixer, PHPCS, Rector, TwigCS, and project-specific checks.
- **Localization:** gettext catalogs under `galette/lang/`; compiled MO files are generated.

## Repository structure

- `galette/lib/Galette/`: application controllers, entities, repositories, filters, services, access control, middleware, and plugin interfaces.
- `galette/includes/`: application bootstrap, dependency definitions, legacy integration, and ACL configuration.
- `galette/templates/`: Twig views.
- `galette/install/scripts/`: initial schemas and versioned upgrade scripts for MySQL and PostgreSQL.
- `galette/plugins/`: installed/checked-in plugin area; the core discovers and loads active plugins.
- `galette/webroot/`: public entry point and built assets.
- `ui/`: frontend source files.
- `tests/Galette/`: PHPUnit tests; `tests/e2e/` and `playwright.config.ts` cover browser tests.
- `bin/`: dependency installation, console, serving, and development scripts.
- `.github/workflows/`: CI, E2E, nightly, and release-announcement workflows.
- `docs/`: project-specific engineering and product context added by this bootstrap; Galette's upstream docs remain under `galette/docs/`.

No `.devcontainer/`, repository custom-agent profiles, or path-specific Copilot instruction files existed at audit time. The repository already had `AGENTS.md` and `.github/copilot-instructions.md`; these are retained and extended rather than replaced.

## Existing architecture

`galette/webroot/index.php` and the include/bootstrap layer initialize the application. `Galette\Core\SlimApp` builds a PHP-DI container, creates the Slim app, configures middleware, and loads routes. Core controllers live under `Galette\Controllers`; PHP attributes describe routes, and `Galette\Core\Routing\RouteLoader` discovers them. Plugins can supply their own controllers and routes.

Business and persistence responsibilities are split across entities, repositories, filters, and services, with some legacy globals still exposed to older code. The design is modular enough to extend, but new work must follow the actual service/container and plugin patterns rather than assume a completely isolated modern MVC stack.

## Main existing functionality

The codebase contains member records and relationships, groups, membership due dates, contributions, transactions, payment types, documents, mailings, saved searches, dynamic fields, role and permission management, history, PDFs, maps, two-factor authentication, and plugin extension points. This list describes code present, not approval of any EEDS-specific workflow.

## Database architecture

The application uses Laminas DB and supports MySQL/MariaDB and PostgreSQL. Initial schema scripts and historical upgrade scripts exist for both platforms under `galette/install/scripts/`. Schema changes must preserve both backends and follow the repository's existing installer/upgrade conventions. Use parameterized query-building APIs and inspect existing repository patterns before adding SQL.

## API architecture

The application is a server-rendered Slim web application with attribute-declared routes, controllers, middleware, and plugin routes. The repository contains AJAX and integration endpoints, but the audit did not establish a general public REST API contract. Do not claim or create one without a requirement and design.

## Authentication and authorization

Authentication is implemented in Galette core, with session middleware, login throttling, optional two-factor authentication, and an authentication middleware. Route-level access uses legacy ACL mappings and permission names resolved through the role/permission system. Member lists and entities also carry access-control behavior, including group-scoped access. Controllers may enforce additional object-level checks.

This is the existing Galette security model, not yet an EEDS-specific hierarchy or an assurance that every proposed EEDS role maps directly to current Galette roles. Any EEDS role, delegation, or organizational scope needs explicit mapping and tests.

## UI architecture

Pages are rendered using Twig and the existing Fomantic UI/Semantic UI theme. Frontend source belongs in `ui/`; generated files under `galette/webroot/` are build output and should not be edited directly. Existing layout, navigation, widgets, accessibility patterns, and translations should be reused.

## Development workflow

- Run `bin/install_deps` to install PHP/JavaScript dependencies and perform the initial frontend setup.
- Run `npm run build` after frontend source changes.
- Use `bin/serve` for a disposable local Galette instance and its isolated database. It requires Docker Compose; use its printed URL and credentials.
- PHPUnit requires an initialized MySQL/MariaDB or PostgreSQL database and a matching `DB` environment variable.
- Follow `AGENTS.md`, `.github/copilot-instructions.md`, and the focused scripts documented there. Project quality tools are installed under `galette/vendor/bin/`.

## Testing strategy

The repository includes roughly 180 tracked PHPUnit test files and Playwright E2E coverage, including fixtures and plugin tests. Database-dependent tests require a configured database. Existing CI provisions MySQL/MariaDB and PostgreSQL, runs update and unit tests, and performs build, lint, and static-analysis checks. Select the smallest relevant test first, then broaden validation based on risk.

## DevOps and GitHub configuration

Existing automation includes Linux CI, E2E, nightly, and announcement workflows. A `compose.yaml` plus `bin/serve` supports local disposable databases. No existing Codespaces devcontainer or dedicated engineering-team Copilot agents were present at audit time. This bootstrap adds a Codespaces configuration and config-validation workflow; it does not change production infrastructure.

## Security posture (configuration inventory, not a penetration test)

The codebase includes authentication and role/permission controls, CSRF checks based on browser request metadata, security headers and Content Security Policy, two-factor authentication, login throttling, and an established private vulnerability reporting policy in `SECURITY.md`. Relevant areas include `galette/lib/Galette/Access/`, `galette/lib/Galette/Middleware/`, and `galette/lib/Galette/Core/`.

This inventory is not a security audit. Member records can include personal data, and the EEDS dossier envisages minors and potentially sensitive information. Data minimization, scope checks, export restrictions, retention, consent, safeguarding, and production deployment controls require explicit review for each feature. Never infer that Galette's current configuration meets all EEDS requirements.

## Technical debt and constraints

- The core intentionally combines newer container/attribute-based code with legacy bootstrap globals and controller patterns.
- Galette's current member, group, role, and contribution concepts may not express EEDS branches, units, mandates, training, progression, or reporting without careful domain mapping.
- Every database feature must remain compatible with both supported SQL engines.
- Generated assets and locale catalogs can obscure source changes; edit source and use project build commands.
- `EEDS_2529_Dossier_reference_application.md` is a consolidated context document, explicitly not a final approved specification.
- The dossier references a historical Frappe/ERPNext exploration, but that stack is not present in the audited code.
- The open-source Galette foundation and its conventions should not be replaced or broadly refactored during an unrelated feature.

## Risks and recommendations

1. Treat the dossier's proposals as proposals; obtain decisions before committing workflows, permission rules, personal-data handling, or financial rules.
2. Before an EEDS feature, document the affected Galette entities, roles, routes, permissions, schema, and migration paths.
3. Use a domain mapping/ADR when an EEDS concept does not map cleanly to Galette. Prefer a scoped extension or plugin when that aligns with project goals; do not choose a customization boundary without review.
4. Require privacy and safeguarding review for features involving young members, guardian access, sensitive information, communications, incidents, or exports.
5. Test schema changes on both MySQL/MariaDB and PostgreSQL and preserve upgrade paths.
6. Confirm the GitHub Copilot and Codespaces features available to the repository's plan and organization; repository files configure behavior but do not grant product entitlements.
