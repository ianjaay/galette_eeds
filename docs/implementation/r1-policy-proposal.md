# R1 policy proposal

- **Status:** APPROVED AS DRAFTED by the project owner on 2026-11-22
- **Decision owner:** Project owner
- **Release:** R1 - core membership and structures
- **Purpose:** Specify the R1 parameters needed before implementation
- **Décision :** Le propriétaire a explicitement approuvé cette proposition
  telle que rédigée dans la conversation du 2026-11-22. Cette approbation
  porte sur les paramètres de politique R1 présentés ici ; elle ne transforme
  pas les propositions ni les questions ouvertes du dossier EEDS en faits
  statutaires et ne vaut pas attribution nominative de rôles ou de périmètres.

## 1. R1 MVP scope

### Proposed value

R1 is limited to:

- configurable EEDS structure hierarchy and effective affiliation history;
- branch and unit reference data;
- one national person/member register linked to Galette member identity;
- admission, renewal, transfer and status-transition workflows;
- duplicate preview and reconciliation;
- scoped member search and approved headcount/reporting views;
- approved exports with a field allowlist;
- typed audit/history for lifecycle decisions.

R1 excludes mandates and governance, full accounting, progression and badges,
training, activities/camps, projects and portals.

### Rationale

The dossier places the national register and membership cycles first, and
Galette already supplies member, group, status, contribution, RBAC, history
and reporting primitives.

### Consequences

The release delivers usable member and structure management but will not
include office permissions, fee collection, education or mobile portal
features.

### Explicit approval required

Approve or change this MVP boundary and confirm whether full admission,
renewal and transfer are all mandatory in R1.

## 2. Existing Galette feature disposition

### Proposed value

Retain Galette core:

- `Adherent` member identity;
- status and due-date fields;
- groups and group membership;
- existing role/permission infrastructure;
- member search/edit/import/export routes;
- dynamic fields where a field is not a workflow invariant;
- history and the plugin framework.

Replace or isolate for R1:

- generic groups used as EEDS structures should be linked to a plugin-owned
  structure model rather than being treated as the complete structure register;
- Galette groups are a synchronized, one-to-one projection of EEDS structures;
  active EEDS affiliations populate their corresponding groups and all
  ancestors, while mapped groups are read-only through native Galette screens;
- broad status labels should not be the sole membership lifecycle record;
- unrestricted table exports must not remain an EEDS export mechanism.

Hide or defer EEDS-specific UI until R1 screens are designed and approved.

### Rationale

Galette's member and group tables are useful foundations, but the
`MembersScope` model covers only the login, direct children and directly
assigned Galette groups. It does not represent EEDS ancestry or effective
mandates.

### Consequences

R1 adds a plugin-owned structure and affiliation model rather than encoding
the national hierarchy only as generic Galette groups.

### Explicit approval required

Approve the reuse boundary and confirm whether existing core status labels,
import/export pages and generic group screens remain accessible.

## 3. Structure authority and structure model

### Proposed value

Create one configurable structure record per national, regional, district,
local-group or unit structure. Include:

- stable identifier;
- approved structure type;
- parent structure;
- code and display name;
- recognition/status;
- effective start and end dates;
- responsible/authorized user information only when approved separately.

Structure changes require a national structure administrator or another
explicitly delegated authority; the authority must be effective for the
structure and date.

No structure deletion should permanently erase historical affiliations.
Free/autonomous/university structures remain configurable proposals and are
excluded from the initial data-constraint set until approved.

### Rationale

The dossier defines hierarchy and asks for configurable, historically
preserved structures. Galette groups are generic and have no EEDS structure
type, recognition status or effective history.

### Consequences

Renaming or reparenting a structure requires effective history; reports and
permissions must resolve the structure as of the reporting date.

### Explicit approval required

Approve the authoritative structure source and the person/role authorized to
create, recognize, suspend, reparent and retire structures.

## 4. Branch and unit reference model

### Proposed value

Store the four dossier branch references as configurable names and reference
age bands:

| Branch | Member term | Unit | Reference age |
|---|---|---|---|
| Jiwu wi | Caat | Mbootaay | 5-11 |
| Lawtan wi | Arunga | Kayon | 12-15 |
| Toor-Toor wi | Jàmbaar | Ñawka | 16-18 |
| Meññeef mi | Mawdo | Gàlle | over 18 |

The values are labels/reference bands only. R1 must not automatically move a
member by age.

Unit capacity and mixity remain reference data, not hard constraints, until
explicitly approved.

### Rationale

These branch names and age references are documented in the dossier, but the
dossier leaves transitions and capacity/mixity rules to confirmation.

### Consequences

A manual, authorized affiliation change can update branch/unit without
automatic age-based effects. Capacity and mixity reports may warn but not
silently block.

### Explicit approval required

Approve branch vocabulary, whether the age bands are normative, and whether
capacity/mixity should be warnings or hard constraints.

## 5. Role and organizational-scope matrix

### Proposed value

R1 authorizes actions through both a permission and resolved
organizational scope. Proposed permission classes:

- structure read/create/edit/recognize;
- member read/create/edit/status/transfer/import/export;
- report read/export;
- lifecycle validation.

Fail closed for route, direct object and list queries.

The proposed baseline scopes:

| Proposed role | Proposed scope |
|---|---|
| System administrator | Technical/system, not an automatic grant to personal EEDS data |
| National registry administrator | National, for approved R1 registry actions |
| Regional administrator | Regional and subordinate structures only |
| District administrator | District and subordinate structures only |
| Group administrator | Local-group and subordinate units only |
| Unit administrator | Unit and its assigned members only |
| Individual member | Own profile and approved self-service fields only |

The role titles above are R1 implementation candidates, not an assertion that
the dossier role matrix is approved.

### Rationale

Galette's direct group scope is insufficient for EEDS hierarchy. It is also
unsafe to grant permissions solely through a staff title.

### Consequences

The plugin must resolve hierarchy and effective delegation at query and object
level. National capability and export rights remain separate permissions.

### Explicit approval required

Approve the role names, scope ancestry, delegated authority and every
create/edit/validate/export/delete permission.

## 6. Admission and status workflow

### Proposed value

Use a configurable, typed lifecycle with at least:

- draft/pre-registration;
- pending validation;
- active;
- suspended;
- inactive;
- resigned;
- expelled;
- deceased;
- sympathizer/non-member;
- former member.

R1 stores a separate transition record with previous status, new status,
effective date, actor, reason and validation evidence reference.

Proposed transition baseline:

- pre-registration/draft -> pending requires authorized creation;
- pending -> active requires an authorized validator;
- active -> suspended/resigned/expelled/deceased requires the approved
  competent authority;
- suspended -> active requires explicit reinstatement approval;
- no transition may erase previous affiliations or statuses.

Age alone must not trigger status or branch changes.

### Rationale

The dossier lists candidate statuses but marks exact statuses and transitions
as proposals requiring validation. Galette stores only a current status plus
priority and due data.

### Consequences

Status change becomes a workflow event rather than a mutable label. Invalid
transitions are denied and logged.

### Explicit approval required

Approve every lifecycle state, transition direction, validator, reason
requirement and the competent authority for suspension, reinstatement,
resignation, expulsion and deceased status.

## 7. Renewal policy

### Proposed value

R1 should open a renewal period only for a complete membership period
approved by the owner. Renewal is proposed as a separate lifecycle event:

- source period;
- target period;
- renewal request date;
- validation date;
- validator;
- fee status reference;
- resulting status.

An existing active member remains active until an approved renewal rule says
otherwise. No automatic lapse should occur solely through missing status
configuration.

### Rationale

Annual renewal is part of the indicative MVP, but period, grace and
good-standing rules are not approved.

### Consequences

Renewal creates historical evidence and reporting periods rather than silently
overwriting a due date.

### Explicit approval required

Approve renewal period, grace period, who may validate, whether contribution
is required for activation and what happens at period end.

## 8. Transfer and affiliation history

### Proposed value

Use a dated affiliation event for structure, unit and branch changes. On
transfer:

- close the prior affiliation with an effective date;
- create the new affiliation;
- require approved validator authority;
- retain previous and new identifiers, actor and reason;
- preserve progression/history where it is introduced in later releases;
- update reporting at the effective date.

No automatic transfer is proposed for age, status or renewal changes.

### Rationale

The dossier requires preserving the affiliation history for mutations. Galette
groups record current membership and are not an effective-dated affiliation
history.

### Consequences

The same member may belong to several structures only over different
effective periods, and reporting must use a selected date.

### Explicit approval required

Approve competent authorities, required documents, whether transfer is
self-requested, and whether dual affiliation is ever allowed temporarily.

## 9. National member identifier

### Proposed value

R1 retains Galette's stable `id_adh` as the immutable technical identity.
An optional, externally meaningful EEDS member number is a separate unique
field. Its format is configurable and initially not auto-generated.

Proposed format only after approval:

- no format assumed;
- no reuse or recycling;
- uniqueness across active and historical records;
- issue and withdrawal produce audit history.

### Rationale

`Adherent` already has `id_adh` and a general `num_adh` field, but no EEDS
identifier format or uniqueness policy.

### Consequences

Reports and integrations use the stable Galette ID internally and may display
the approved national number.

### Explicit approval required

Approve format, issuer, uniqueness scope, reuse policy and the relationship
between technical ID and national number.

## 10. Duplicate detection and merge

### Proposed value

Duplicate detection is proposal-only and starts with a preview:

- exact national number;
- exact normalized name plus birth date;
- exact login/email;
- phone in a normalized approved form.

The preview produces confidence candidates only. R1 must not automatically
merge or delete any identity.

An approved merge requires:

- two-person review for youth or sensitive records;
- explicit source/target selection;
- preservation of both histories;
- reassignment or conflict review of affiliations, statuses and accounts;
- audit and rollback plan.

### Rationale

The dossier requires duplicate detection but leaves criteria and merge process
unapproved. Automatic merges are unsafe for personal data and affiliations.

### Consequences

Duplicate candidates remain separate until approved reconciliation.
Development adds a conflict review screen but no automatic destructive action.

### Explicit approval required

Approve match fields and thresholds, merge authority, two-person review,
which field wins and whether a tombstone record is retained.

## 11. Minor and guardian policy

### Proposed value

R1 collects only fields already necessary to an approved R1 member register:

- identity and date of birth;
- approved contact fields;
- unit/structure affiliation;
- approved status and history.

Guardian verification, guardian account access and youth portal access remain
disabled. Health, emergency, incident, photo and private communication data
remain out of scope.

If guardian data is approved later, it must be a separate verified
relationship with scope, consent/authorization, effective dates and revocation.
Existing `parent_id` must not be treated as verified guardianship.

### Rationale

The dossier identifies minors and guardians as requiring stronger protection,
but it does not approve the necessary fields or guardian rights.

### Consequences

R1 can manage youth records only under national/minimization rules and does
not provide guardian or youth self-service.

### Explicit approval required

Approve minimum minor fields, whether birth date is retained, guardian
relationship and verification, consent, retention, incident access and portal
scope.

## 12. Export and data visibility

### Proposed value

Replace unrestricted EEDS export-table selection with a scoped XLSX domain export:

- export format: XLSX;
- fields: all non-secret Galette member fields and active EEDS domain fields
  available to the export service, in the first R1 release;
- no password, Galette's internal `fingerprint` import/fixture marker, or
  unrelated database tables;
- only members in the caller's resolved EEDS scope;
- separate `eeds:export` permission;
- export access is assigned by the association's approved profile/permission
  matrix, not inferred from a job title;
- the initial approved profile set is: central/national administrator,
  regional commissioner, district commissioner and local-group commissioner;
- export request and result audit;
- the export audit records fields, structure IDs and generated row count;
- optional privileged re-authentication/MFA remains open for a later hardening
  requirement.

Parameter exports and PDF/CSV exports must resolve the same scope before rows
are generated.

### Rationale

`CsvController::doExport()` currently accepts database table names and exports
full result sets. That behavior is not suitable for the sensitive EEDS register
without an explicit domain allowlist.

### Consequences

Existing generic table export behavior remains outside R1 unless separately
approved. R1 export fields and row scope are maintained explicitly.

### Explicit approval required

The owner approved the XLSX format and the initial all-fields principle on
2026-11-22. The exact future configurable field selector, export-file
retention and MFA requirement remain open. The export implementation must not
include secrets or unrelated tables.

## 13. Contribution and good-standing boundary

### Proposed value

R1 may display and link to an existing Galette contribution/due status only
as a reference. R1 does not implement a fee amount, period, exemption,
receipt, collection or good-standing policy.

No member status should automatically change because of a contribution state
unless the owner approves a future R3 policy.

### Rationale

The annual contribution principle is documented, but amount, period,
exemption and receipt rules are explicitly to confirm.

### Consequences

R1 can preserve existing contribution data and show it only when scoped, but
will not calculate or enforce EEDS good standing.

### Explicit approval required

Approve the R1/R3 boundary, and separately approve the fee policy before R3.

## 14. Data retention and audit

### Proposed value

R1 retains:

- current member and affiliation values;
- typed membership and transfer history;
- status transition actor/date/reason;
- export/audit events;
- migration evidence.

Use no general deletion for member or affiliation history. Retention and
archival periods require separate approval.

Sensitive/administrative values use existing storage-sanitization rules:
HTML-capable fields use `Html::clean()` where explicitly permitted; plain text
uses `Html::strip()`.

### Rationale

The dossier requires history and privacy controls, but retention periods and
archival rules are unresolved.

### Consequences

R1 is additive and preserve-history focused until retention policy exists.

### Explicit approval required

Approve retention, archival, deletion and records that require two-person
review.

## 15. R1 acceptance gates

### Proposed value

R1 is complete only after:

- policy values in this document are formally approved;
- fresh and upgrade database migrations pass on MySQL/MariaDB and PostgreSQL;
- no proposal is treated as an implemented fact;
- route, object and list scope tests deny sibling and unrelated structures;
- duplicate preview has no automatic merge;
- export fields and rows are allowlisted and audited;
- minor and guardian fields remain out of scope until approved;
- Playwright UAT uses synthetic data and follows the owner-specified scenarios;
- formal owner UAT sign-off is recorded.

MySQL/MariaDB may be the primary release UAT environment, but PostgreSQL
compatibility remains mandatory before implementation sign-off unless the
owner formally changes that constraint.

## Approval checklist

| Parameter | Proposed value | Owner decision |
|---|---|---|
| MVP boundary | Structures/register/lifecycle/transfer/preview/search/export | Approve/change |
| Existing feature disposition | Retain Galette identity and plugin/UI foundations | Approve/change |
| Structure authority | National/delegated authorized changes | Approve/change |
| Branch units | Four documented references; no automatic transition | Approve/change |
| Authorization | Permission plus resolved hierarchy scope; fail closed | Approve/change |
| Status workflow | Typed statuses and authorized transitions | Approve/change |
| Renewal | Typed period event; no silent lapse | Approve/change |
| Transfer | Effective-dated affiliation; preserve history | Approve/change |
| Identifier | Keep `id_adh`; national number format unresolved | Approve/change |
| Duplicate/merge | Preview only; no automatic merge | Approve/change |
| Minor/guardian | Minimize; no portal or unapproved sensitive fields | Approve/change |
| Export | Allowlisted fields and scope; audited | Approve/change |
| Contribution | Reference-only in R1 | Approve/change |
| Retention/audit | Preserve history; policy unresolved | Approve/change |
