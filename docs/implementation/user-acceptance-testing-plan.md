# User acceptance testing plan

## R1 approval state

- UAT owner: project owner
- Target acceptance date: 2026-12-15
- Approved scenario set: structure/member creation, lifecycle,
  transfer/status, duplicate preview, scoped search/export and denied
  cross-scope access
- Owner-requested primary UAT environment: synthetic MySQL/MariaDB

PostgreSQL compatibility remains mandatory for implementation and migration;
the owner should separately approve any reduced PostgreSQL UAT evidence.

## Scope

UAT is release-gated and must use synthetic data. No production member,
guardian or minor data may be copied into test environments.

## Roles

Owner/product approver, national administrator, regional/district/group/unit
administrator, educational adviser, adult member, youth representative,
guardian where approved, and independent security/QA observers.

## Core scenarios

1. Create and recognize a structure and verify hierarchical visibility.
2. Create, admit, renew, transfer and retire a synthetic member.
3. Attempt access from sibling and unrelated structures; expect denial.
4. Create, expire and revoke a mandate; verify authorization changes.
5. Create and reconcile a contribution, exemption and receipt.
6. Record an approved progression event and verify validator scope.
7. Record training or camp attendance and verify restricted participant data.
8. Export only an approved field set and verify the audit log.
9. Exercise no automatic age transition, duplicate merge or health-data collection.
10. Verify responsive, keyboard and localization behavior for approved screens.

## Acceptance evidence

- PHPUnit and database integration results
- Playwright and accessibility evidence
- Migration evidence on MySQL/MariaDB and PostgreSQL
- Security/authorization negative-test evidence
- UAT sign-off identifying the approved scope version
- Defect disposition and owner approval

## Résultats UAT techniques vérifiés

Environnement temporaire MySQL/MariaDB du 2026-11-22 :

1. La connexion administrateur atteint le tableau de bord.
2. Après synchronisation des permissions plugin, le compte synthétique
   `morpheus` dispose de `eeds:read`, `eeds:structure-create`,
   `eeds:validate`, `eeds:member-edit` et `eeds:export`.
3. Une structure synthétique nommée `Structure nationale UAT synthétique`
   a été créée dans la base jetable avec le code
   `UAT-SYNTH-NATIONAL`, ainsi qu’un périmètre `eeds:read` daté pour
   `morpheus`.
4. `/plugins/eeds/` répond HTTP 200 à `morpheus` et affiche la structure
   synthétique.
5. Le compte synthétique `luke.skywalker`, sans permission/perimètre EEDS,
   est redirigé (HTTP 302).
6. Le super-administrateur reçoit HTTP 403 sans périmètre EEDS. Cette
   observation confirme que le contrôle de périmètre reste distinct du
   contrôle ACL.

7. La route `/plugins/eeds/duplicates` répond HTTP 200 à un utilisateur
   autorisé et retourne une liste vide dans la base synthétique sans candidats.
   Pour le membre synthétique sans droit EEDS, elle est redirigée en HTTP 302.
8. La route `/plugins/eeds/export` accepte une requête contenant uniquement
   les champs `id`, `name`, `structure_id` et la structure synthétique 1 :
   HTTP 200 avec `{"success":true}`.
9. La même route refuse le champ `national_number` : HTTP 403.
10. L'appel autorisé crée une ligne dans `galette_eeds_export_audit` pour
   l'acteur synthétique 7, avec les champs, la structure 1 et un nombre de
   lignes déclaré égal à zéro.

Ces résultats ne prouvent pas un export de données PDF/CSV réel : le code
EEDS actuel enregistre uniquement les métadonnées de l'appel. Aucun fichier
exporté n'a été vérifié.

Le filtre Twig `|trans` du modèle EEDS a été corrigé avec les fonctions
`_T(...)` utilisées par Galette. `twigcs` ne signale plus de violation.

Ces résultats ne constituent pas une acceptation métier ni une UAT complète.
Ils ne couvrent pas la création/mutation réelle, les transferts inter-périmètre,
les exports, les mineurs, les doublons ni PostgreSQL.

## Defect and release process

Defects are classified as blocker, major, minor or observation. Blockers and
high-risk privacy/authorization defects prevent release approval. Approval
belongs to the named owner or board and must specify the released scope,
data migration state and known limitations.
