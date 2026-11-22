# R0 owner decision register

## Approval

- **R0 status:** Approved for planning and foundation work
- **Approval date:** 2026-11-22
- **Approved scope:** R0 foundation and decision baseline
- **Not approved at the time of R0 approval:** R1 business implementation or
  any later release. The subsequent R1 policy approval is recorded below.
- **Approved by the owner:** the proposed plugin architecture
  [ADR-0001](../engineering/adr/ADR-0001-eeds-plugin-domain-boundary.md) status
  `APPROVED`

## Décisions restant à préciser pour l’implémentation et l’UAT R1

| Decision | Owner evidence required | Status |
|---|---|---|
| MVP scope and excluded features | Owner-approved R1 policy | Approuvé tel que rédigé le 2026-11-22 ; résultats mesurables d’UAT à consigner |
| Existing Galette features retained/hidden/replaced | Detailed product disposition | Principe de réutilisation R1 approuvé ; écrans et permissions Galette à préciser |
| Authoritative structure source and update authority | Governance decision | Ouvert : source nominative et autorité de mise à jour non identifiées |
| Structure type, recognition and effective history | Governance decision | Modèle historisé R1 approuvé ; valeurs réelles et autorité compétente à préciser |
| Role and office to permission/scope mapping | Authorization matrix | Principe permission + périmètre hiérarchique approuvé ; correspondance fonctions/droits et titulaires ouverte |
| Mandate, term, renewal and delegation rules | Governance decision | Hors périmètre R1 ; à décider pour une phase ultérieure |
| Admission, renewal, transfer and status transitions | Membership policy | Modèle d’événements R1 approuvé ; autorités, périodes et validations détaillées à préciser |
| Identifier and duplicate/merge procedure | Data-governance decision | Identifiant technique Galette, aperçu des doublons et absence de fusion automatique approuvés ; critères détaillés ouverts |
| Minor, guardian, consent and retention policy | Safeguarding/privacy approval | Portails et données sensibles non approuvés pour R1 ; champs minimum, consentement et conservation ouverts |
| Export fields and national/territorial rights | Security/product approval | Principe de liste blanche, périmètre et audit approuvé ; champs et droits d’export détaillés ouverts |
| Fee, exemption and good-standing policy | Finance approval | Référence seulement dans R1 ; politique financière à décider pour R3 |
| MFA, privileged access and session policy | Security approval | Ouvert |
| Hosting, backup, recovery and incident policy | Operations approval | Ouvert |
| Launch languages and accessibility standard | Product/owner approval | Ouvert |

Les valeurs de politique R1 sont détaillées dans
[r1-policy-proposal.md](r1-policy-proposal.md). Le propriétaire a approuvé
cette proposition telle que rédigée le 2026-11-22. Cette décision de projet
ne ferme pas les précisions encore ouvertes et ne transforme pas les
propositions du dossier EEDS en faits statutaires.

## R1 approval updates

- **ADR-0001 plugin/domain boundary:** Approved 2026-11-22.
- **Politique R1 :** approuvée telle que rédigée par le propriétaire le
  2026-11-22. Les règles explicitement laissées ouvertes et les affectations
  nominatives restent à décider ; les propositions du dossier ne deviennent
  pas des faits statutaires par cette approbation.
- **R1 UAT approver:** Project owner stated “i approve”.
- **R1 UAT scenarios:** Structure/member creation, lifecycle, transfer/status,
  duplicate preview, scoped search/export and denied cross-scope access.
- **Target acceptance date:** 2026-12-15.
- **Requested UAT environment:** Synthetic MySQL/MariaDB only.

The UAT environment is not an approval to omit PostgreSQL implementation
compatibility. Dual-engine migration and regression coverage remain required
by Galette architecture and the project constraints.

No open decision may be silently implemented as an approved requirement.
