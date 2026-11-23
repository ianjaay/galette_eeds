# Mapping organisationnel EEDS vers les groupes Galette

## Décisions approuvées

- Le registre `eeds_structures` reste la source de vérité pour les structures,
  leurs types et leurs relations parent/enfant.
- Chaque structure territoriale EEDS est matérialisée par un groupe Galette
  unique, lié par identifiants. Le groupe Galette n'est pas un second registre
  modifiable indépendamment.
- L'appartenance à un groupe organisationnel Galette est dérivée des
  affiliations EEDS actives : un membre affilié à une unité appartient au
  groupe de cette unité et à tous les groupes Galette de ses ancêtres.
- Les groupes organisationnels liés sont en lecture seule dans les écrans
  Galette. Les changements passent par les structures et affiliations EEDS.
- Les groupes Galette transversaux restent non liés à `eeds_structures` et
  restent gérés par Galette. Ils ne sont jamais ajoutés automatiquement à
  l'arborescence territoriale.
- L'appartenance à un groupe n'accorde pas de permission EEDS. Les permissions
  EEDS ne sont pas assignables à un groupe Galette tant qu'une correspondance
  d'autorisation séparée n'est pas approuvée.

## Correspondance des niveaux

| Structure EEDS | Groupe Galette | Parent Galette |
|---|---|---|
| Nation (`national`) | Racine | Aucun |
| Région (`region`) | Groupe | Nation |
| District (`district`) | Groupe | Région |
| Groupe local (`groupe_local`) | Groupe | District |
| Unité (`unite`) | Groupe | Groupe local |

Le nom du groupe reprend exactement `structure_name`; aucun préfixe n'est
ajouté. Le groupe racine initial est nommé « Nation » et porte le code stable
`NATION`. Les noms des régions, districts, groupes locaux et unités ne sont
pas inventés ni préchargés : ils doivent provenir d'une source EEDS validée.
Le dossier fournit les références d'unités Mbootaay, Kayon, Ñawka et Gàlle,
mais ne donne pas une liste nominative des structures territoriales.

Les organes statutaires (Assemblée Générale, comités, équipes et forums), les
commissions, les projets, ainsi que les structures autonomes ou universitaires
ne sont pas insérés artificiellement dans cette arborescence. Leur
classification et leurs rattachements restent soumis aux décisions et règles
de reconnaissance correspondantes ; les structures spéciales citées comme
propositions dans le dossier ne sont pas activées par cette correspondance.

## Synchronisation et intégrité

La table `eeds_structure_groups` relie les identifiants sans dupliquer les
noms ou la hiérarchie. Une structure ne peut être liée qu'à un seul groupe et
un groupe à une seule structure. Les changements de nom ou de parent restent
à traiter dans le registre EEDS et doivent être répercutés dans les groupes
liés ; une divergence interrompt la synchronisation plutôt que de choisir
silencieusement une valeur.

`eeds:sync-groups` affiche d'abord un aperçu sans modification. Avec `--apply`,
il crée les groupes absents lorsque leur nom n'entre pas en conflit, puis
réconcilie les appartenances courantes. Les groupes existants portant le même
nom ne sont jamais liés automatiquement. Ils doivent être vérifiés puis liés
explicitement par IDs avec `eeds:map-structure-group`; leur nom et leur parent
doivent déjà correspondre, et ils ne doivent pas avoir de responsables ni de
rôles Galette limités à ce groupe.

La synchronisation refuse par défaut de retirer une appartenance Galette
inattendue. Après examen, `--replace-memberships` autorise le remplacement des
appartenances des seuls groupes EEDS liés ; les groupes transversaux ne sont
pas touchés. Les membres des groupes liés ne peuvent pas être modifiés via les
écrans de groupes ou de membres Galette.

Les transferts EEDS réconcilient immédiatement les groupes pour la date
courante. Un transfert à date future devient visible dans les groupes lorsque
`eeds:sync-groups --apply` est exécuté après sa date d'effet ; aucun ordonnanceur
automatique n'est configuré par cette implémentation.

## Responsables, mandats et permissions

Aucun responsable Galette n'est attribué automatiquement. Les noms de
fonctions, titulaires, mandats, périodes, délégations et droits relèvent de la
R2 et ne sont pas déduits d'un rôle de responsable de groupe. Les groupes
nouvellement liés n'ont pas de responsables ni de rôles Galette assignés.

## Limites de référence et données

Le dossier distingue les faits statutaires des propositions. Il décrit le
chemin national → région → district → groupe local → unité et les noms de
branches/unités, mais ne livre pas un annuaire à jour de régions, districts ou
groupes locaux, ni une source validée de leurs rattachements. Aucune structure
réelle ne doit donc être ajoutée sur la seule base d'exemples. Aucun nom,
compte ou autre donnée réelle de membre ou de mineur n'est utilisé par cette
implémentation.
