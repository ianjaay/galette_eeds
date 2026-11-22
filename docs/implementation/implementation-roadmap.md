# Implementation roadmap

R0 was approved by the project owner on 2026-11-22. Its approval is a
planning-and-foundation gate only.

ADR-0001 was approved on 2026-11-22. The R1 policy was subsequently approved
as drafted. R1 remains gated by final UAT, security review and unresolved
business decisions explicitly identified as open.

## Product sequence

| Release | Name | Objective | Main functionality | Hard dependencies | Validation |
|---|---|---|---|---|---|
| R0 | Foundation and decision baseline | Establish approval and implementation contracts | Scope, role/scope matrix, privacy inventory, migration spike, test fixtures | Owner decisions | Owner, privacy, security and database gate |
| R1 | Core membership and structures | Deliver the national register baseline | Structures, affiliations, branches, units, member lifecycle, history, scoped search/export | R0 decisions | Functional and cross-scope UAT |
| R2 | Mandates and governance | Manage offices and statutory responsibilities | Positions, appointments, delegations, bodies, meetings | R1 identity/scope | Governance and authorization UAT |
| R3 | Contributions and receipts | Manage approved membership contributions | Periods, payments, exemptions, receipts, reconciliation | R1; finance rules | Finance and audit UAT |
| R4 | Education and progression | Deliver approved pedagogical frameworks | Frameworks, stages, evidence, validation, jeegos | R1; pedagogical approval | Pedagogical and safeguarding UAT |
| R5 | Training | Manage adult development | Catalog, sessions, applications, attendance, certificates | R1/R2 | Adult-development UAT |
| R6 | Activities and camps | Manage participation and reports | Activities, registrations, attendance, authorizations, resources | R1/R2; safeguarding | Safeguarding UAT |
| R7 | Strategy and reporting | Connect plans and impact reporting | Projects, conventions, indicators, periodic reports | R1 and relevant domain data | Reporting approver UAT |
| R8 | Documents and portals | Controlled communication and approved access | Documents, notifications, member/guardian/youth portal decisions | R1/R6; privacy decisions | Accessibility, security and portal UAT |

## Release dependencies

The critical path is owner decisions to R0, scope and hierarchy to R1, and
member/mandate identity to later releases. R3 and R4 can proceed only after
their approved business rules exist. R5 and R6 depend on R2 mandates and R1
scope. R7 depends on the source data it reports. R8 is last because it adds
remote access and communication risk.

## Global exit criteria

Every release must have approved scope, focused tests, migration evidence,
authorization negative tests, a security review where sensitive data or
exports are involved, UAT evidence and a formal owner sign-off.
