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
2. Créer, admettre, renouveler, transférer et retirer un membre synthétique.
   Un transfert doit avoir une seule affiliation source active, dans le
   périmètre de l'acteur à la date d'effet choisie ; les affiliations
   historiques, futures ou hors périmètre ne doivent pas être modifiées.
   Une transition de statut doit également vérifier l'affiliation du membre
   à sa date d'effet, et non uniquement à la date du jour.
3. Attempt access from sibling and unrelated structures; expect denial.
4. Create, expire and revoke a mandate; verify authorization changes.
5. Create and reconcile a contribution, exemption and receipt.
6. Record an approved progression event and verify validator scope.
7. Record training or camp attendance and verify restricted participant data.
8. Export the approved XLSX field set and verify the audit log. The workbook
   must contain only the caller's active affiliations and must exclude secrets
   and unrelated database tables.
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

Observations historiques de l'environnement temporaire MySQL/MariaDB du
2026-11-22, avant les corrections ultérieures :

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

Ces résultats historiques ne prouvent pas un export de données réel : à cette
date, le code enregistrait uniquement les métadonnées de l'appel.

L’ancien comportement du durcissement R1 était délibérément HTTP 501 :
il ne déclarait pas une exportation réussie alors qu’aucun fichier n’était
produit. Depuis l’arbitrage XLSX du 2026-11-22, l’implémentation doit produire
un classeur réel borné au périmètre EEDS de l’acteur. Tous les champs
applicatifs non secrets sont retenus pour la première version ; le sélecteur
configurable relève d’une évolution ultérieure.

Le filtre Twig `|trans` du modèle EEDS a été corrigé avec les fonctions
`_T(...)` utilisées par Galette. `twigcs` ne signale plus de violation.

Le 2026-11-23, sur deux bases nouvellement installées et isolées :

- l'installation standard du plugiciel et la migration 0.2 vers 0.3 ont réussi
  sur MySQL/MariaDB et PostgreSQL ; quatre rôles d'export sont présents et le
  rôle Staff ne reçoit plus ce droit par défaut ;
- la suite ciblée a réussi sur chaque moteur (19 tests, 93 assertions), dont un
  test transactionnel avec membres synthétiques couvrant scope, doublons,
  affiliations hors périmètre ou expirées, classeur XLSX et audit.

Ces tests de service ne vérifient pas le téléchargement HTTP ni les permissions
de chaque profil. Ils ne constituent pas une acceptation métier ou une UAT
complète ; les mutations et transferts inter-périmètre restent également à
recetter. L'export filtre désormais les champs membre selon leur visibilité
Galette pour le niveau d'accès de l'acteur, sans l'exception CSV pour les
champs `id_adh` et `parent_id` configurés inaccessibles. Le champ `gpgid` est
inclus uniquement lorsqu'il est visible. Le champ `fingerprint` reste exclu,
car c'est un marqueur interne Galette utilisé par les traitements d'import et
les fixtures, pas un attribut de registre destiné à l'export.
Le propriétaire a choisi d'autoriser uniquement une liste explicitement
approuvée de champs dynamiques non sensibles ; aucun champ n'a encore été
approuvé. Les champs dynamiques et les fichiers restent donc exclus. Leur
traitement doit respecter la politique de protection des données à approuver.

Après ce filtrage, la suite ciblée a réussi sur des bases synthétiques isolées
MySQL/MariaDB et PostgreSQL : 21 tests et 135 assertions sur chaque moteur,
sans test ignoré. Elle vérifie les visibilités, les permissions non limitées
aux groupes Galette, les lignes hors périmètre,
les cellules texte et la concordance entre colonnes et audit ; elle ne remplace
pas la recette HTTP ou l'UAT.

Les tests de contrôleur ajoutés vérifient également les en-têtes du
téléchargement XLSX, le refus sans permission Galette ou scope EEDS et le
masquage du bouton d'export sans scope d'export. Ils passent sur chaque moteur
(4 tests, 48 assertions) ; ils invoquent les actions Slim directement et ne
valident donc pas encore le routage HTTP complet ni les permissions des quatre
rôles dédiés. Conformément à l'arbitrage du propriétaire, les permissions EEDS
ne peuvent pas être attribuées à un groupe Galette en l'absence de correspondance
groupe–structure approuvée.

## Defect and release process

Defects are classified as blocker, major, minor or observation. Blockers and
high-risk privacy/authorization defects prevent release approval. Approval
belongs to the named owner or board and must specify the released scope,
data migration state and known limitations.
