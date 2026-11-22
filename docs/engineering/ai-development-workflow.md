# AI-assisted development workflow

## Intake

For every request, identify the requested outcome, affected users, product scope, relevant source-of-truth documents, repository area, risk, dependencies, and acceptance evidence. Use the lightest workflow that covers the risk. Ask the owner when a material business rule, permission, privacy decision, destructive operation, or architecture choice is unresolved. For manageable ambiguity, state a bounded assumption and do not encode it as an approved rule.

## Phases

1. **Intake:** classify quick, standard, complex, security, architecture, or UI/UX work.
2. **Business analysis:** when behavior or product scope is involved, capture actors, user story, documented rules, proposals, edge cases, open questions, and Given/When/Then acceptance criteria.
3. **Repository analysis:** locate existing Galette behavior, patterns, impacted files, access checks, database paths, tests, and regression risks.
4. **Architecture:** for cross-domain, integration, or schema-sensitive work, compare options and document a proposed design. Create an ADR in `docs/engineering/adr/` only for a material decision; obtain owner approval before implementing major architectural change.
5. **Plan:** split work into scoped tasks with dependencies, acceptance criteria, tests, risk, and an owner. Reuse the development-task template.
6. **Implementation:** keep changes surgical, follow repository instructions, preserve both database backends, and write behavior-focused tests.
7. **Security:** use an independent review when authentication, authorization, personal/youth data, uploads, exports, integrations, secrets, or deployment is affected.
8. **QA:** run the smallest useful tests first, then additional coverage justified by risk. Record exact commands and results.
9. **Code review:** inspect the resulting diff independently, focusing on bugs, regressions, access control, data integrity, performance, and test evidence.
10. **Delivery:** report outcome, files, decisions/assumptions, validations, unresolved items, and deployment status. Never call a local development server a production deployment.

## Planning requirements

For standard or complex work, use [`templates/development-task-template.md`](templates/development-task-template.md). Complex tasks must state dependencies and approval gates before implementation. Split independent UI/backend/doc work only when the contracts are clear and conflicts can be avoided; otherwise sequence work.

## Validation guide

- PHP change: focused PHPUnit test with the required database and `--test-suffix=.php`; relevant PHP-CS-Fixer, PHPCS, and PHPStan checks as scope permits.
- Database change: verify upgrade behavior and test both MySQL/MariaDB and PostgreSQL where possible.
- Frontend source change: run `npm run build`; run focused Playwright tests when configured and possible.
- Twig change: run the existing TwigCS check.
- Agent/instruction/devcontainer change: run `ruby bin/check-agent-config` and inspect the resulting Git diff.
- Documentation-only change: validate links/references and Markdown structure; documentation changes do not need application test suites unless a documentation validator is part of CI.

If dependencies, databases, browsers, or credentials are unavailable, record that limitation instead of claiming the validation passed.

## Example requests

- **Feature:** “Use `engineering-orchestrator` for a standard feature. First check whether the behavior is approved in `docs/product/project-context.md`; identify Galette reuse points and write acceptance criteria before implementation.”
- **Bug:** “Use `repository-analyst` to trace the issue, then make the smallest regression-tested fix and report the exact test run.”
- **Refactor:** “Analyze the current callers and tests first. Keep public behavior unchanged; propose any cross-cutting architecture changes separately.”
- **Security review:** “Use `security-engineer` for an independent, read-only review of the specified change. Report severity, evidence, impact, and remediation; do not change code.”
- **UI review:** “Use `frontend-ui-ux-engineer` to assess the current Twig/Fomantic patterns and accessibility, then propose a scoped change.”
- **API:** “Map the request to existing Slim routes and state whether it requires a new API contract; do not assume REST behavior.”
- **Database:** “Use `database-engineer` to review both SQL platforms, migration and rollback/data risks before proposing a schema change.”
- **QA:** “Use `qa-test-engineer` to define edge cases, run the focused tests that are available, and separate executed checks from recommendations.”
- **Code review:** “Use `code-reviewer` to independently inspect the current diff and report only actionable findings with file/line evidence.”
- **Architecture:** “Use `software-architect` to compare options against the Galette code and approved EEDS scope. Record a proposed ADR; do not implement a major change without approval.”
