# Project implementation assessment

## Status

This is a planning-only assessment. It does not approve proposals in the
[EEDS reference dossier](../../EEDS_2529_Dossier_reference_application.md),
make application changes, or authorize the implementation of a release.

## Mise à jour au 2026-11-22

Cet état antérieur ne tenait pas compte du plugiciel EEDS R1 ajouté
ultérieurement. Le dépôt contient maintenant des tables, services, routes et
permissions techniques R1. Ils restent une fondation technique non validée en
UAT métier; les mandats, fonctions et profils détaillés ne sont pas implémentés.

## Executive summary

The application should remain a Galette-based system. Galette already provides
member records, groups, statuses, contributions, documents, mailings, RBAC,
dynamic fields, history, imports/exports, reports and plugin infrastructure.
It now has a partial EEDS structure hierarchy and typed membership foundation,
but still lacks validated historical reparenting, mandates, full progression,
training, camps, projects and safeguarding domain model.

The recommended architectural boundary is a scoped Galette plugin/domain
extension. Galette core should change only for capabilities that are genuinely
general and cannot remain safely isolated. This decision remains proposed
for core changes; the plugin boundary itself was approved as ADR-0001 on
2026-11-22.

## Functional domains

1. Foundation and national member/structure register
2. Organization and governance
3. Branches and units
4. Membership and affiliation lifecycle
5. Adult leadership and mandates
6. Education and progression
7. Training
8. Activities and camps
9. Projects and strategic reporting
10. Contributions and receipts
11. Documents and communication
12. Roles, scope, exports and privacy

## Source classification

- **Confirmed/documented:** the EEDS identity and mission, hierarchy and
  statutory branch/unit references, annual contribution principle,
  sympathizer distinction, and loss of membership by death, resignation or
  expulsion.
- **To confirm:** admission, renewal, fee, transfer, identifier, duplicate,
  mandate, branch-transition, capacity, minor, retention and reporting rules.
- **Proposal:** guardian portals, youth portals, education frameworks,
  training, camps, projects, private communication and additional reporting.

## Existing Galette reuse

- [Member entity and repository](/workspaces/galette_eeds/galette/lib/Galette/Entity/Adherent.php)
- Groups and group membership
- Statuses and contributions
- Dynamic fields
- Role/permission and Galette scope concepts
- Documents and history
- CSV/PDF and chart reporting
- Plugin routes, menus and dashboard interfaces

## Major gaps

- EEDS structure types and effective hierarchy/history
- Membership admission, renewal, transfer and typed status transitions
- Mandate and delegation lifecycles
- Minor and verified guardian models
- Progression, evidence and pedagogical validation
- Training and camp/participant workflows
- Project, convention and periodic-report workflows
- Territory-aware authorization and export allowlisting

## Major risks

Treating proposals as statute, overbroad role mappings, sensitive youth data,
unrestricted exports, duplicate/merge errors, migration corruption and rigid
hierarchy models are the principal planning risks.

## Recommendation

Approve R0 to establish scope and decision baselines. Do not begin R1 until
the MVP, role/scope matrix, minor-data policy and membership rules are
approved.
