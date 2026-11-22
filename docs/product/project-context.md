# EEDS application project context

## Source and status

This context is derived from [`EEDS_2529_Dossier_reference_application.md`](../../EEDS_2529_Dossier_reference_application.md), a consolidated business and functional reference. The dossier itself says it is not a final specification. Its labels are preserved:

- **Confirmed/documented:** stated in the dossier as `[CONFIRMÉ]` or as a statutory fact.
- **To confirm:** stated as `[À CONFIRMER]`, or a business decision the dossier says must be validated.
- **Proposal:** stated as `[PROPOSITION]`; not an approved requirement.

The dossier references prior Frappe/ERPNext exploration. The current repository contains Galette (Slim/PHP), not Frappe/ERPNext; its current code and approved decisions take precedence for technical implementation. No EEDS application-specific code was found during the repository audit. This document must be updated when the project owner approves decisions or scope.

## Vision and objectives

The Éclaireuses et Éclaireurs du Sénégal (EEDS) are a national, secular, voluntary, non-political and non-confessional Scout association founded in 1937, and a member of WOSM. Their motto is **SERVIR** and their watchword is **PRÊT À SERVIR**.

The vision in the dossier is to be Senegal's most inspiring and inclusive youth association, creating transformative learning experiences for every young person, everywhere. The application should support the educational and community-development purpose of Scouting rather than reduce the movement to an administrative register.

Documented objectives include a reliable national membership/structure reference, coherent management of memberships and changes, role- and structure-scoped access, educational progression, adult leadership and training, activities and projects, contribution tracking, reporting, accountability, and strategic digital transformation. The dossier also connects the work to GSAT governance and impact measurement.

## Stakeholders and users

The dossier identifies national, regional, district, local-group, unit, youth, and adult volunteer contexts. Candidate roles include system/national administrators, national and sector commissioners, regional and district commissioners, group and unit leaders, educational advisers, trainers, adult members, young members, former members, friends/sympathizers, and parents/guardians.

These are role concepts, not an approved permission matrix. Access must combine a validated role with an organizational scope and delegated authority; a title alone must not grant country-wide access. Guardian and youth portal access is a proposal requiring validation.

## Organizational and educational model

The reference hierarchy is national → region → district → local group → base unit, alongside statutory and executive bodies such as the General Assembly, committees, and teams. Free/autonomous and university structures are also contemplated. Names, boundaries, parent relationships, recognition, statuses, and mandates must be administrable and adaptable rather than fixed in code.

Four statutory branch references and their corresponding units are listed:

| Branch | Member term | Age reference | Unit |
|---|---|---:|---|
| Jiwu wi | Caat | 5–11 | Mbootaay |
| Lawtan wi | Arunga | 12–15 | Kayon |
| Toor-Toor wi | Jàmbaar | 16–18 | Ñawka |
| Meññeef mi | Mawdo | Over 18 | Gàlle |

The dossier records unit capacities and mixity rules, but whether these are hard constraints or indicative is to be confirmed. Age bands and transitions must be configurable; age alone must not silently trigger a branch transfer. Branch-specific subgroups and educational vocabularies should be configurable if adopted.

## Functional domains and scope

The dossier describes the following domains. Unless marked documented, the feature ideas below are proposals and need prioritization and acceptance criteria before implementation.

1. **Foundation / membership register:** structures, affiliations, people, membership requests/renewals, transfers, status history, duplicate detection, search, controlled import/export, and basic headcount reporting.
2. **Leadership and governance:** positions, appointments, terms, delegation, statutory bodies, meetings, decisions, minutes, and mandate tracking.
3. **Contributions:** annual membership fees and receipts. Full accounting is a separate scope decision.
4. **Educational program:** branch frameworks, individual/collective progress, evidence, validation, advisers, awards/jeegos, and history. Detailed criteria require approved pedagogical references.
5. **Adult development and training:** recruitment, assignments, courses, applications, attendance, assessment, certification, and development pathways.
6. **Activities and camps:** schedules, structures, participants, adult teams, permissions, attendance, resources, and reports; safeguarding and incident data require separate access controls.
7. **Strategic plans, projects, and reporting:** action plans, partners/conventions, locations, indicators, deliverables, periodic reporting, and dashboards.
8. **Communication and documents:** institutional resources, versions, targeted announcements, and notifications. Private communications involving youth are not assumed.

The dossier's indicative roadmap places a national structure/member register, role and scope controls, membership cycles, history, reporting, and controlled import/export first; leadership/governance second; then fees, education, training, activities, strategic reporting, and member/guardian portals. It is not an approved delivery commitment.

## Documented rules and proposals

Documented statutory context includes the four branches and corresponding unit terms/age references, unit capacity/mixity statements, annual contribution as a condition of membership in good standing, the distinct status of sympathizers, and loss of membership through death, resignation, or expulsion under applicable rules. Statutory responsibilities exist at multiple organizational levels.

The following are explicitly not settled and must not be invented in code:

- exact admission, validation, renewal, fee amount, exemption, collection, and receipt rules;
- member identifier format, duplicate detection/merging, and transfer process;
- whether age triggers branch transitions, and how progression or awards are validated;
- appointment/term length, authority, renewal, combination of offices, and delegation rules;
- status and reporting of autonomous/free/university structures;
- guardian verification and permissions; collection and use of minor data;
- retention, archival, deletion, consent, and sensitive-data policy;
- suspension, expulsion, reinstatement, event selection, and report approval workflows;
- national member-list visibility and export rights.

## Non-functional requirements and safeguards

The dossier calls for a responsive web application across computer, tablet, and phone, multi-level structure-aware access, a central national reference with appropriate local visibility, extensibility, and possible support for weak connectivity and additional languages.

Security and privacy are central because the system may handle youth and minor data. Recommendations in the dossier include data minimization, role plus scope authorization, restricted access to sensitive information, secure authentication and privileged MFA where available, audit trails, controlled exports, session revocation, consent/authorization management, retention rules, backups and restore testing, and incident handling. These are design requirements to evaluate, not claims about existing deployment controls.

Avoid collecting health, emergency, incident, or other sensitive data unless justified and approved with a defined access, retention, and safeguarding policy. Do not enable unsupervised private adult-to-youth communications as an assumed feature.

## Technical constraints and current repository

- The checked-in application is Galette, implemented in PHP 8.3+, Slim 4, PHP-DI, Twig, and Laminas DB.
- MySQL/MariaDB and PostgreSQL are both supported and must remain so.
- Existing member, group, membership, access-control, plugin, and reporting concepts need to be evaluated before choosing extensions or schema.
- The current repository does not contain a Frappe/ERPNext bench or the `eeds_membership` custom app described as historical context.
- Preserve Galette's architecture and database upgrade mechanisms; avoid broad rewrites.
- Product and technical requirements must be traceable to approved source decisions. The source dossier is at the repository root.

## Dependencies, risks, and open questions

### Dependencies

- Approval of membership, structure, role, mandate, and contribution rules by EEDS decision-makers.
- Validated source data and structure boundaries.
- Approved pedagogical references and progression criteria.
- Agreed privacy, child-safeguarding, consent, retention, and export policies.
- A technical decision on how EEDS-specific behavior fits Galette's core/plugin architecture.
- An operational owner for hosting, backups, access reviews, and incident response.

### Risks

- Treating proposals as statutory rules could encode incorrect organizational practice.
- Mapping broad Galette roles directly to EEDS offices could grant excessive access.
- Handling youth, guardian, health, incident, or contact data without a governance basis increases harm and compliance risk.
- Rigidly encoding regions, ages, capacities, terms, or branch transitions may block legitimate changes.
- Unreviewed migration or import processes could corrupt or expose membership data.
- The historical ERPNext context could lead to design based on a stack that is not present in this checkout.

### Open questions requiring product decisions

1. What is the approved first-release/MVP scope and its measurable acceptance criteria?
2. Which current Galette features are retained, changed, hidden, or replaced for EEDS?
3. What is the authoritative organizational structure and who can approve structure changes?
4. How do EEDS offices map to application roles, scopes, delegations, and time-bounded appointments?
5. What are the exact admission, renewal, fee, transfer, status, and expulsion workflows?
6. What identifiers and deduplication/merge procedures are approved?
7. What data about minors and guardians is necessary, lawful, and retained for how long?
8. Are guardian accounts, youth accounts, MFA, and self-service portals in initial scope?
9. Which educational frameworks and training requirements are approved and versioned?
10. Which actions require audit logging, approval, or two-person review?
11. What hosting, availability, low-connectivity, data-residency, backup, and recovery targets apply?
12. Which languages are required at launch?

## Traceability

R0 was approved on 2026-11-22 for planning and foundation work only. It does
not approve R1 implementation or any proposal in this dossier. The owner
decision register remains in
[`docs/implementation/r0-owner-decision-register.md`](../implementation/r0-owner-decision-register.md).

The source is [`EEDS_2529_Dossier_reference_application.md`](../../EEDS_2529_Dossier_reference_application.md). It consolidates statutory material, the Toor-Toor wi branch guide, EEDS strategy and action planning, and previous project discussions. Those source artifacts are referenced by the dossier but not all are present as separate files in this repository. Do not imply those underlying documents were independently reviewed here.
