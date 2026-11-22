# Release R0 - Foundation and decision baseline

**Status:** APPROVED for planning and foundation work  
**Owner approval:** Approved 2026-11-22  
**Implementation boundary:** R0 does not approve R1 or any EEDS business workflow.

## Objective

Establish an approved scope, data model, authorization matrix and migration
baseline without implementing unsettled EEDS workflows.

## Scope

- Owner-approved MVP and excluded capabilities
- Requirement status classification
- Role plus organizational-scope matrix
- Minor, guardian, consent and retention inventory
- Plugin/domain boundary
- Database migration convention
- Synthetic fixtures and authorization baseline
- Security/export review

## Dependencies

- Owner decisions
- Dossier and statutory source validation
- Safeguarding/privacy/security participants
- Database test environments

## Exit criteria

- First-release member/structure/status mapping is approved
- Export fields and scope policy are approved
- Fresh-install and upgrade migration conventions pass on both SQL engines
- Cross-scope denial tests exist
- No proposal is implemented without approval

## Implementation audit

**Overall status:** PARTIALLY IMPLEMENTED

| Exit criterion | Evidence | Status |
|---|---|---|
| Owner-approved MVP and scope mapping | Planning and owner decision registers exist; several R1 policy values remain open or proposed | Partial |
| Requirement status classification | Requirements inventory and dossier status labels exist | Implemented in planning |
| Role plus organizational-scope matrix | Architecture requirements are documented; no executable R1 scope matrix or authorization implementation exists | Not implemented |
| Minor, guardian, consent and retention inventory | Risks and proposed boundaries exist; no approved inventory or executable policy exists | Not implemented |
| Plugin/domain boundary | [ADR-0001](../../engineering/adr/ADR-0001-eeds-plugin-domain-boundary.md) is approved; an inert EEDS plugin and scope/export contracts are present | Implemented for R0 spike |
| Database migration convention | Paired no-schema MySQL/PostgreSQL scripts exist and execute; plugin version is 0.1 | Implemented for R0 |
| Synthetic fixtures and authorization baseline | A fail-closed scope/export contract test exists with synthetic scope values | Implemented for R0 |
| Security/export review | A minimal export allowlist contract exists, while [CSV export behavior](../../../galette/lib/Galette/Controllers/CsvController.php) remains unchanged | Partial |
| Cross-scope denial tests | Synthetic sibling/unrelated structure denial assertions exist; no route or database scope integration exists | Partial |

R0's planning and architectural ADR artifacts are implemented. The R0 plugin
boundary spike, paired migration convention and fail-closed scope/export
contract tests are implemented and passing on MySQL/MariaDB and PostgreSQL.
Route/database integration negative tests are not possible until R1 defines
its approved structure and member routes.

## Not included

No business workflow, migration of real member data or creation of a youth
portal is included in R0.
