# Plan d'implémentation organisationnelle EEDS

- **Statut :** proposition pour validation
- **Application :** aucune modification appliquée par ce plan
- **Portée :** hiérarchie, fonctions, permissions, mandats, interfaces et reporting

## 1. Principe

Ne pas créer automatiquement la structure réelle des EEDS à partir du dossier consolidé. Toute structure nommée, toute fonction, tout titulaire, toute autorité de mise à jour et tout périmètre doit provenir d'une source approuvée.

Conserver l'identité membre Galette et isoler les concepts EEDS dans le plugiciel, conformément à l'ADR approuvée. Ne pas confondre groupes, structures et mandats.

## 2. Décisions bloquantes

Avant toute donnée réelle :

1. liste officielle des structures nationales/régionales/district/groupe/unités ;
2. parents exacts et limites de chaque structure ;
3. seuil d'unité minimal par groupe ;
4. autorité de création, reconnaissance, suspension, fermeture et reparentage ;
5. types de relations autorisés ;
6. gestion des structures libres/universitaires ;
7. fonctions exactes et titulaires ;
8. autorité de nomination et durée des mandats ;
9. règles de cumul, délégation et révocation ;
10. champs d'export autorisés ;
11. périmètre national/territorial des profils ;
12. politique des mineurs, tuteurs et conservation.

Les fonctions, titulaires, mandats, délégations et révocations sont distincts des gates R1 ci-dessus : ils constituent des décisions R2 et ne doivent pas être encodés pour satisfaire R1. Les gates de gouvernance des données et des accès restent obligatoires avant toute donnée réelle.

## 3. Phase A — sécurisation technique

### Travaux

- centraliser permission + périmètre + objet ;
- vérifier structure source, structure cible et membre avant mutation ;
- appliquer le périmètre aux doublons ;
- calculer le périmètre serveur pour les exports ;
- rendre le renouvellement atomique ;
- ajouter garde de cycle et vérification effective des structures ;
- ajouter tests négatifs route/objet/liste/export.
- produire un export réel limité aux champs approuvés, avec audit calculé côté serveur ;
- ne pas présenter un simple journal d'audit comme un export fonctionnel.

### Critères d'entrée

- spécification des permissions approuvée ;
- compte synthétique national/régional/district/groupe/unité ;
- structures synthétiques sans données réelles.

### Critères de sortie

- aucune mutation hors périmètre ;
- aucun candidat doublon hors périmètre ;
- aucun export hors périmètre ;
- aucune écriture partielle ;
- migrations MySQL/PostgreSQL valides.
- export réel de données synthétiques selon le périmètre ;
- champs interdits absents du fichier ;
- audit concordant avec les lignes réellement exportées.

## 4. Phase B — modèle d'historique structure

### Travaux

- conserver `parent_structure` pour le modèle court terme ;
- ajouter une relation historique parent/enfant si validé ;
- ajouter effective start/end à la relation ;
- enregistrer acteur, autorité, motif et justificatif ;
- prévoir reconnaissance, suspension, fermeture et reparentage ;
- empêcher les cycles ;
- filtrer par date effective.

### Critères de sortie

- arborescence reconstruite à une date ;
- reparentage historique ;
- structures fermées exclues selon la date ;
- aucune modification silencieuse ;
- tests MySQL/PostgreSQL.

## 5. Phase C — fonctions et mandats

Ces fonctions sont hors périmètre R1 approuvé et ne doivent pas être intégrées tant qu'elles ne sont pas approuvées dans une release R2.

### Modèle proposé

- fonction ;
- personne ;
- structure ;
- mandat ;
- autorité ;
- dates ;
- statut ;
- document ;
- remplacement ;
- délégation ;
- révocation.

### Contrôle

La fin ou la révocation d'un mandat doit fermer les droits associés, tandis qu'un titre historique reste consultable.

## 6. Phase D — interface

- vue arborescente nationale à locale ;
- parent, enfants, chemin ancestral ;
- filtrage par date effective ;
- fiche structure ;
- liste des responsables ;
- listes membres filtrées ;
- historique des modifications ;
- navigation responsive et accessible ;
- aucun contrôle visible hors périmètre.

## 7. Phase E — reporting

Développer uniquement les champs approuvés :

- effectifs par structure/date ;
- effectifs par branche/unité ;
- affiliations actives ;
- renouvellements ;
- structures reconnues/suspendues/fermées ;
- audit des modifications ;
- export PDF/CSV avec liste blanche.

L'export réel limité est un gate R1, pas une fonctionnalité différée aux mandats R2. Il peut être développé et testé avant ou indépendamment du modèle de mandats, selon le plan approuvé.

## 8. Ordre recommandé

1. Décisions de structure et autorités.
2. Sécurisation du périmètre objet/liste/export.
3. Correction des défauts techniques identifiés.
4. Historisation des relations.
5. Interface arborescente.
6. Données réelles/import validé.
7. Mandats R2.
8. Reporting territorial.

## 9. Validation requise

- validation propriétaire de l'arborescence réelle ;
- revue sécurité ;
- revue base de données ;
- tests MySQL/MariaDB ;
- tests PostgreSQL ;
- UAT synthétique ;
- revue accessibilité ;
- revue finale du code ;
- sign-off formel avant toute recette métier finale.
