# Engineering governance

## Human approval gates

The project owner must approve:

- major architecture, domain-model, or customization-boundary changes;
- production deployments and release readiness;
- destructive schema/data operations or irreversible imports/deletions;
- significant dependency changes;
- authentication/authorization architecture or privilege changes;
- changes affecting existing member or youth data;
- unresolved business rules, especially those the EEDS dossier labels as proposals or to confirm;
- security-sensitive infrastructure changes.

An agent cannot grant itself or another agent that approval.

## Agent autonomy and collaboration

Agents may inspect code, propose designs, implement scoped reversible changes, write tests, update documentation, run non-destructive checks, and review changes. They must stop before an approval gate, explain the decision needed, and avoid hiding blocked work behind an assumed default. Use minimum necessary profiles and explicit task boundaries.

## Quality gates

A deliverable is complete only when:

- acceptance criteria are satisfied or remaining work is explicitly blocked;
- implementation is consistent with the approved design and repository patterns;
- relevant tests and checks are executed and results recorded;
- security/privacy risks have been reviewed when applicable;
- review findings are resolved or explicitly accepted by the owner;
- directly related documentation is accurate and updated;
- no unrelated worktree change is overwritten or omitted from the final report.

## Data and security governance

Follow Galette's existing access and data-handling patterns, but independently validate the intended EEDS access scope. Use least privilege, data minimization, auditability, safe exports, and approved retention. Treat minors, guardians, contact details, health/emergency data, incidents, consent records, and authentication credentials as high-impact information. Do not collect or expose such data without an approved purpose and safeguards.

Never put credentials, tokens, private keys, real member records, or production data in source, prompts, tests, logs, or generated documentation. Use synthetic fixtures.

## Git and release governance

Preserve unrelated changes; avoid force pushes and destructive commands; do not automatically merge or push to protected branches. A local `bin/serve` instance is a development environment, not a production deployment. Production changes require explicit human approval and the actual deployment process.

## Decision records

For a material architecture or domain-model decision, add an ADR under `docs/engineering/adr/` using [`templates/technical-design-template.md`](templates/technical-design-template.md) as a starting point. Record status as proposed until the owner approves it; do not create placeholder ADRs.
