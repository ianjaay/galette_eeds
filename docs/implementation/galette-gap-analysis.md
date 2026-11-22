# Galette functional gap analysis

## Reuse map

| Requirement area | Existing Galette evidence | Gap |
|---|---|---|
| Members | `Adherent`, `Members`, `MembersController`, `/member/{id}` | No EEDS lifecycle, identifier or guardian model |
| Groups | `Group`, `Groups`, `GroupsController`, group/member tables | No typed hierarchy, effective dates or recognition history |
| Status | `Status`, status table | Status is not a lifecycle transition model |
| Contributions | `Contribution`, contribution/payment types and transactions | No approved fee periods, exemptions or EEDS receipt policy |
| Roles/scope | `Galette\Access\Roles`, `Role`, `MembersScope`, role tables | Mandates and EEDS ancestry are not authorization lifecycles |
| History | `Core\History`, history table/controller | General history is insufficient for typed evidence workflows |
| Dynamic fields | Dynamic field entities and tables | Dynamic fields cannot express lifecycle or safeguarding invariants |
| Documents | `Document`, `DocumentsController` | No audience/version/publication domain workflow |
| Reports | Chart/member/Csv/Pdf components | No EEDS headcount, renewal, mandate or strategic reports |
| Plugins | plugin discovery, routes and permission interfaces | EEDS domain boundary is not yet implemented |

## Current implementation status

- **Existing and sufficient:** basic people, identity, groups, statuses,
  contributions, RBAC, documents, history and UI primitives.
- **Existing but needs extension:** group hierarchy, member lifecycle,
  exports, reports and plugin menu/permission capabilities.
- **Partially existing:** role/scope access, dynamic fields and audit.
- **New development required:** mandates, branches/units, progression,
  training, camps, projects and safeguarding.
- **Architectural change required:** EEDS-specific typed scope resolution and
  mandate-effective authorization.
- **Clarification required:** every unsettled business rule in the requirements
  inventory.
- **Deferred:** youth/guardian portals, incident data and private messaging.

## Proposed implementation approach

Use a scoped plugin/domain extension linked to stable Galette member IDs.
Keep core identity, session, plugin discovery and existing UI foundations.
Plugin-owned services and repositories should own EEDS structure, lifecycle,
appointment and workflow data.

Do not implement an Frappe/ERPNext stack. Do not overload every Galette group
as an EEDS structure until the structure model and scope are approved.
