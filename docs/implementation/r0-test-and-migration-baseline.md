# R0 test and migration baseline

## Test baseline

R0 establishes test conventions for future releases. It does not create a
feature test suite or claim database validation has passed.

### Required PHP tests

- Entity, repository and service behavior
- Route authentication and permission checks
- Direct object authorization
- Member-list and EEDS organizational-scope filtering
- Cross-region, sibling-region and unrelated-unit negative cases
- Expired mandate and revoked delegation cases
- Invalid transitions and duplicate/conflict previews
- Export field allowlists and unauthorized export denial
- Failure and rollback behavior

### Database tests

Future migrations must be tested as:

1. fresh install
2. upgrade from the preceding documented version
3. repeat execution
4. rollback or fail-safe recovery where supported
5. preservation of existing member, affiliation and audit records

Both MariaDB/MySQL and PostgreSQL are required.

### Browser tests

Use synthetic data and no real youth or guardian records. Playwright scenarios
must cover the approved role/scope matrix, responsive UI and keyboard
navigation. Accessibility should follow an explicitly approved standard.

## Migration convention

EEDS plugin migrations should use Galette plugin database version tracking.
Every plugin release that adds database behavior must declare a database
version and provide paired or appropriately compatible database update
classes/scripts for the supported engines.

Rules:

- Additive migrations are preferred.
- Existing member and affiliation IDs must remain stable.
- No migration deletes or de-identifies real personal data without explicit
  owner approval.
- A migration must fail visibly and log why it failed.
- Upgrade scripts must be repeat-safe or rejected safely.
- A migration release requires a backup and restoration exercise.
- Plugin database-version behavior is governed by
  `Galette\Core\PluginInstall` and `Galette\Core\Plugins`.

## Synthetic fixture baseline

Future tests should include only synthetic people and structures:

- national, regional and district organizations
- at least two sibling territories
- local groups and units
- adult and youth synthetic members
- synthetic guardians where guardian behavior is separately approved
- active, expired and revoked mandates
- branch references where explicitly approved

## Validation commands

Documentation-only changes do not require the full application suite.
PHP changes must use:

```sh
DB=mysql galette/vendor/bin/phpunit --test-suffix=.php tests/Galette/
DB=pgsql galette/vendor/bin/phpunit --test-suffix=.php tests/Galette/
```

## R0 validation evidence

- PHP syntax checks passed for all R0 plugin and test files.
- `EedsFoundationTest` passed on MySQL/MariaDB: 3 tests and 11 assertions.
- PHP-CS-Fixer dry-run passed for the R0 PHP files.
- PHPCS passed for the R0 PHP files.
- Targeted PHPStan passed with no errors.
- The throwaway Galette instance returned HTTP 301 and was stopped cleanly.
- MySQL/MariaDB focused PHPUnit passed: 4 tests and 13 assertions.
- PostgreSQL focused PHPUnit passed: 4 tests and 13 assertions.
- The no-schema R0 migration script executed successfully through both
  database containers.
- R0 intentionally creates no domain tables or business workflow.
