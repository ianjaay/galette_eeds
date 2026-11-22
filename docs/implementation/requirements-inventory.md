# Functional requirements inventory

Status legend: **D** documented/confirmed, **TC** to confirm, **P** proposal,
and **R** security/privacy/architectural recommendation.

| ID | Domain | Requirement | Status | Acceptance intent |
|---|---|---|---|---|
| FND-001 | Foundation | Central national member and structure reference | D | Authorized users maintain one consistent reference |
| FND-002 | Foundation | Preserve configurable French/Wolof statutory terminology | D/R | Labels are configurable and approved |
| FND-003 | Foundation | Preserve Galette upgrades and both SQL backends | D | Fresh and upgrade workflows work on MySQL/MariaDB and PostgreSQL |
| FND-004 | Foundation | Responsive and extensible application | P | Approved workflows pass supported viewport and accessibility checks |
| ORG-001 | Organization | National, region, district, local group and base-unit hierarchy | D | Parent/child relationships preserve effective history |
| ORG-002 | Organization | Statutory bodies and bodies of governance | D/P | Implement only after competent-authority rules are approved |
| ORG-003 | Organization | Configurable structure names, boundaries and statuses | P/TC | No rigid national structure names or states |
| MEM-001 | Membership | National person/member register | P/TC | Identity, member identifier and duplicate policy are approved |
| MEM-002 | Membership | Admission, renewal and status history | D/P/TC | Approved transition workflow records actor, date and reason |
| MEM-003 | Membership | Transfer/mutation and affiliation history | P/TC | Previous affiliations remain available |
| MEM-004 | Membership | Annual contribution as condition of good standing | D/TC | Fee and grace rules remain configurable |
| MEM-005 | Membership | Loss by death, resignation or expulsion | D/TC | Competent authority and evidence are enforced |
| MEM-006 | Membership | Search/filter and controlled exports | P/TC | Scope and field allowlist are enforced |
| MIN-001 | Youth/privacy | Restrict minor records | R/TC | Unrelated users cannot view or export minor data |
| MIN-002 | Youth/privacy | Verify guardian relationship and authority | TC | No portal access before verification and revocation rules |
| MIN-003 | Youth/privacy | Record participation/communication authorizations separately | P/TC | Authorized purpose and retention are explicit |
| APP-001 | Governance | Separate position, appointment and delegation | P/TC | Appointments are dated and retain history |
| APP-002 | Governance | Mandates determine effective scope | P/R | Expiry/revocation removes permissions |
| EDU-001 | Education | Branch framework and progression | P/TC | Configurable pedagogical reference |
| EDU-002 | Education | Evidence and adviser/counsellor validation | P/TC | Validator scope and evidence are authorized |
| EDU-003 | Education | Preserve progression history | P | Transfer does not erase historical records |
| TRN-001 | Training | Course, session, application and certificate management | P/TC | Approved rules determine selection and completion |
| ACT-001 | Activities | Activity/camp registration and attendance | P/TC | Capacity and authorization rules are explicit |
| ACT-002 | Activities | Protect minor participation and incident data | R/TC | No health/incident data or weak permissions in MVP |
| PRJ-001 | Strategy | Projects, conventions, plans and indicators | P/TC | Reports reconcile to source records |
| PRJ-002 | Strategy | Periodic validation and revision workflow | P/TC | Approver authority is enforced |
| FIN-001 | Contributions | Period, amount, exemption and receipt rules | D/TC | Approved rules and audit are enforced |
| FIN-002 | Contributions | No full accounting module in MVP | D boundary | MVP is membership administration, not a general ledger |
| DOC-001 | Communication | Institutional document versions and audience | P/TC | Publication and retention rules are explicit |
| DOC-002 | Communication | Notifications and announcements | P | Delivery scope and logs are controlled |
| IAM-001 | Security | Role plus organizational scope | D/R | Cross-scope actions fail closed |
| IAM-002 | Security | Separate view/edit/validate/export/delete rights | D/R | Export allowlist is independent of view permission |
| SEC-001 | Security | MFA, audit, session revocation and incident response | R/TC | Deployment guarantees are documented |
| SEC-002 | Security | Retention, consent and minimization | R/TC | Data classes have approved purposes and expiry |

The dossier's statutory branch references are documented facts, but age
transitions, capacities and mixity remain to confirm and must not become
automatic enforcement.
