# Development standards

## Before changing code

1. Read `AGENTS.md` and `.github/copilot-instructions.md`.
2. Read [`docs/product/project-context.md`](../product/project-context.md) for EEDS work.
3. Trace current behavior and existing tests before adding or duplicating functionality.
4. Distinguish approved requirements from proposals and state assumptions.
5. Keep changes scoped; do not use an EEDS feature request as a reason for unrelated Galette refactoring.

## Galette-specific implementation rules

- Follow the repository's PHP, Slim, PHP-DI, Twig, Laminas DB, event, and plugin conventions.
- Maintain MySQL/MariaDB and PostgreSQL compatibility.
- Preserve existing authorization at route, controller, entity, and query/list scope as applicable; test negative access cases.
- Keep user-renderable values safe at storage time: use `Galette\Util\Html::clean()` for permitted rich HTML and `Html::strip()` for plain text, following `AGENTS.md`. Never sanitize secrets or CSS as HTML.
- Use repository logging/error-handling conventions; do not silently swallow technical failures.
- Edit frontend source under `ui/`; do not hand-edit generated frontend output.
- Keep user-facing text translatable and follow existing localization conventions.
- Make data migrations backward-aware and reversible where the architecture allows; do not delete member data without explicit authorization.

## Development commands

```sh
bin/install_deps
bin/serve
npm run build
DB=mysql galette/vendor/bin/phpunit --test-suffix=.php tests/Galette/
DB=pgsql galette/vendor/bin/phpunit --test-suffix=.php tests/Galette/
```

`bin/serve` starts a throwaway local instance and database; it requires Docker Compose. Use the exact URL it prints. PHPUnit requires an initialized database and a matching `DB` value. The repository-specific test skill and `AGENTS.md` describe further setup and quality commands.

For PHP quality tools, vendor executables are under `galette/vendor/bin/`, not a root-level `vendor/bin/`.

## Review checklist

- Are requirements and assumptions traceable?
- Is existing behavior reused and preserved?
- Are route, object, list-scope, and export permissions correct?
- Are minor/personal data and retention risks addressed?
- Are both SQL platforms supported?
- Are failure paths logged and visible?
- Are regression tests meaningful and focused?
- Are generated files avoided unless the build requires tracked artifacts?
- Is documentation accurate about actual implementation?
