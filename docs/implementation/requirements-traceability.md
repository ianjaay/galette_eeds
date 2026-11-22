# Requirements traceability matrix

| Requirement | Domain | Gap | Release | Task category | Test category | Acceptance |
|---|---|---|---|---|---|---|
| FND-001/003 | Foundation | None for Galette identity; EEDS national reference missing | R0/R1 | Plugin boundary; migration; structure model | Fresh/upgrade; dual-database | Consistent national reference |
| ORG-001/003 | Organization | Typed structure hierarchy absent | R1 | Structure entity/repository/routes | Cross-scope and hierarchy tests | Approved hierarchy and history |
| MEM-001/006 | Membership | Lifecycle and export policy missing | R1 | Member, transition and export services | Lifecycle, duplicate and export tests | Scoped register and controlled export |
| MIN-001/003 | Youth/privacy | Safeguarding model absent | R0/R1-R8 | Data inventory and relationship model | Negative cross-scope and export tests | No unrelated youth access |
| APP-001/002 | Governance | Mandates absent | R2 | Appointment/delegation model | Expiry/revocation and scope tests | Effective mandate permissions |
| EDU-001/003 | Education | Progression absent | R4 | Framework/evidence/history model | Pedagogical and transfer tests | Approved configurable framework |
| TRN-001 | Training | Training absent | R5 | Training catalog/session model | Eligibility, attendance and certificate tests | Audited completion |
| ACT-001/002 | Activities | Camp/authorization absent | R6 | Activity/participant/authorization model | Scope, consent and attendance tests | Safeguarded participation |
| PRJ-001/002 | Strategy | Projects absent | R7 | Plan/project/report model | Reconciliation and approver tests | Approved periodic reporting |
| FIN-001/002 | Contributions | Fee rules absent; accounting out of scope | R3 | Period/payment/receipt model | Reconciliation and audit tests | Approved membership contribution tracking |
| DOC-001/002 | Communication | Audience and version workflow limited | R8 | Document/audience/notification model | Scope, download and notification tests | Controlled communication |
| IAM-001/002, SEC-001/002 | Security | Scope/export/audit incomplete | R0-R8 | Authorization/export/audit middleware | Cross-scope, MFA and audit tests | Fail closed and auditable |
