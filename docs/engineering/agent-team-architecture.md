# AI engineering team architecture

## Purpose and boundaries

This repository defines specialist Copilot agent profiles for EEDS product work on the existing Galette codebase. The profiles are reusable role instructions, not autonomous workers, a permission system, or a claim that every role is automatically invoked. A human or an orchestrating agent selects the smallest useful set for each task.

The approved source hierarchy is:

1. Explicit project-owner decisions.
2. Approved EEDS requirements and the project context's documented/confirmed rules.
3. Approved technical designs and ADRs.
4. Existing Galette implementation and repository instructions.
5. Clearly labeled assumptions.

The EEDS dossier is important context but is not a final specification. Never convert a proposal into a business rule silently.

## Roles

| Profile | Primary responsibility | Default boundary |
|---|---|---|
| `engineering-orchestrator` | Intake, task mode, coordination, quality gates, delivery report | Does not invent requirements or skip approvals |
| `product-business-analyst` | Requirements, stories, rules, scope, acceptance criteria | No business-rule invention |
| `repository-analyst` | Existing code, impact, reuse, regressions | Read-only analysis |
| `software-architect` | Alternatives, design, contracts, ADRs | Obtain owner approval for major architecture |
| `backend-developer` | PHP application behavior and backend tests | Scoped changes following Galette patterns |
| `frontend-ui-ux-engineer` | Twig/UI, accessibility, responsive behavior | Reuse current Fomantic/Semantic UI patterns |
| `database-engineer` | Models, SQL, migrations, data integrity | No destructive migration without explicit approval |
| `security-engineer` | Independent threat and security review | Review-only; escalate critical/high findings |
| `devops-platform-engineer` | Codespaces, CI, operations, deployment readiness | No production infrastructure changes without approval |
| `qa-test-engineer` | Test strategy and execution | Report actual test results only |
| `code-reviewer` | Independent review of changed files | Review-only; findings first |
| `technical-documentation-engineer` | Accurate engineering and user documentation | Do not document unimplemented behavior as fact |

Each profile is stored in `.github/agents/*.agent.md`, the native repository custom-agent format. Tool lists are restricted where practical; availability still depends on the Copilot environment and product configuration.

## Collaboration

- Orchestrator assigns only roles needed by scope and risk.
- For a complex feature, separate requirement analysis, repository analysis, design, implementation, security, QA, and review where those are meaningful.
- Security and code reviews must be independent of the implementing profile; do not ask the implementer to self-approve.
- Parallel work is permitted only on independent files or clearly separated deliverables. The orchestrator owns integration and conflict resolution.
- A role handoff includes objective, boundaries, relevant source docs, dependencies, expected artifact, acceptance criteria, and stop condition.
- Agents report assumptions, blockers, tests, and changed files; they do not claim approval or deployment without evidence.

## Task modes

- **Quick task:** focused bug or minor change; repository analysis, implementation, targeted test, review appropriate to risk.
- **Standard feature:** business analysis → repository impact → implementation → QA → review; add architecture/security/database work when relevant.
- **Complex feature:** full workflow in [`ai-development-workflow.md`](ai-development-workflow.md), with explicit plan and owner decisions before high-risk implementation.
- **Security audit:** independent review and prioritized findings; no automatic security-sensitive remediation.
- **Architecture review:** recommendations and ADRs; no major refactor without approval.
- **UI/UX review:** assess existing design and accessibility, propose substantial redesign before implementation.

## Outputs and quality gates

Use the templates under [`templates/`](templates/). A change is complete only when scope and acceptance criteria are addressed, relevant tests are executed or explicitly marked blocked, security risks are handled, review findings are resolved or accepted by the owner, and related documentation is updated.

## Product and repository references

- Product context: [`docs/product/project-context.md`](../product/project-context.md)
- Repository audit: [`docs/engineering/repository-audit.md`](repository-audit.md)
- Workflow: [`docs/engineering/ai-development-workflow.md`](ai-development-workflow.md)
- Governance: [`docs/engineering/engineering-governance.md`](engineering-governance.md)
- Standards: [`docs/engineering/development-standards.md`](development-standards.md)
- Root instructions: [`AGENTS.md`](../../AGENTS.md) and [`.github/copilot-instructions.md`](../../.github/copilot-instructions.md)

## Platform limitations

GitHub Copilot custom-agent behavior and available tools vary between VS Code/Codespaces, GitHub.com coding agent, and other supported clients. The profiles use portable frontmatter and tool aliases and intentionally do not declare MCP servers, secrets, or organization entitlements. Test profile discovery in the target Copilot client. A profile definition does not by itself grant repository access, tool permissions, or approval authority.
