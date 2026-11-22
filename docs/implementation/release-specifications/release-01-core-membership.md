# Release R1 - Core membership and structures

## Objective

Provide the first usable national member and structure register within
approved scope.

## Scope

- Configurable structure hierarchy and status
- Branch/unit reference data
- Member register and affiliations
- Approved admission, renewal, transfer and status history
- Duplicate preview and reconciliation
- Scoped search and member reports
- Controlled exports
- Typed audit evidence

## Existing functionality to reuse

Galette member, group, status, dynamic-field, role, history, document,
repository and reporting components.

## New development

EEDS-specific structure hierarchy, branches/units, lifecycle services,
effective affiliations and scope-aware authorization.

## Dependencies

- R0 approval
- Authoritative structure data
- Identifier and duplicate policy
- Membership transition rules
- Minor and export policy
- Dual-database migration support

## Testing

Use PHPUnit for entities/services/transitions and database integration tests
on MySQL/MariaDB and PostgreSQL. Add route and object authorization tests,
cross-region/district/unit negative tests, duplicate preview tests, migration
repeat-run tests and scoped Playwright scenarios.

## Exit criteria

- Approved hierarchy and lifecycle rules pass UAT
- Cross-scope access is denied
- Previous affiliations remain inspectable
- National and territorial reports reconcile
- Minor and export controls pass security review

## Implementation audit

**Status:** IMPLEMENTED FOR TECHNICAL VALIDATION; OWNER UAT REMAINS

- Approved plugin boundary: EEDS plugin with R1 permissions and registry route
- Structure and effective affiliation tables
- Typed lifecycle and status history
- Renewal event history
- National member number issuance/withdrawal
- Duplicate preview and review-only reconciliation
- Scoped structure resolution and export allowlist/audit foundation
- Paired MySQL/MariaDB and PostgreSQL migrations

Validation evidence:

- R1 PHP syntax passed
- PHP-CS-Fixer, PHPCS and targeted PHPStan passed
- MySQL migration executed and rerun successfully
- PostgreSQL migration executed and rerun successfully
- Focused PHPUnit passed on MySQL/MariaDB: 5 tests, 18 assertions
- Focused PHPUnit passed on PostgreSQL: 5 tests, 18 assertions
