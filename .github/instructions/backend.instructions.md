---
applyTo: "**/*.php"
---

Follow `AGENTS.md` and the existing Galette conventions. Use strict types, PSR-12/project formatting, the Slim/PHP-DI architecture, established error logging, and parameterized Laminas DB patterns. Preserve MySQL/MariaDB and PostgreSQL compatibility. Check route, object, list-scope, and export permissions. Add focused regression tests for behavior changes. For user-renderable stored values, follow the `Galette\Util\Html::clean()` / `Html::strip()` rules in `AGENTS.md`; never sanitize secrets or CSS as HTML. Do not silently swallow technical errors.
