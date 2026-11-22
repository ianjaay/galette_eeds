# Audit de la structure organisationnelle EEDS

- **Statut :** audit en lecture seule
- **Date :** 2026-11-22
- **Source métier :** [dossier de référence EEDS](../../EEDS_2529_Dossier_reference_application.md)
- **Périmètre :** modèles, données techniques, interfaces, permissions et état UAT observé
- **Décision :** aucune modification de structure, fonction ou permission n'est faite par cet audit

## 1. Conclusion exécutive

Galette dispose de mécanismes natifs de groupes, de rôles et de périmètres par groupe. Ils permettent une arborescence technique limitée des groupes, mais ne constituent pas à eux seuls l'organisation EEDS.

Le dépôt contient également un plugiciel EEDS R1 avec une table de structures utilisant `parent_structure`, des affiliations effective-datées, des périmètres individuels, des statuts de membre, des doublons et un journal d'export. Cette implémentation fournit une **fondation technique d'arbre**, mais pas encore une représentation métier complète de l'association :

- hiérarchie technique multi-niveau disponible partiellement ;
- hiérarchie historique/administrative non complète ;
- fonctions, nominations, mandats, délégations et révocations absents ;
- profils du dossier non transformés en matrice de droits approuvée ;
- opérations de mutation et exports insuffisamment bornés par le périmètre ;
- interface limitée à des tableaux plats ;
- UAT partiellement réalisée sur des données synthétiques.

## 2. Source organisationnelle

Le dossier décrit, dans sa synthèse hiérarchique, une organisation **National → Région → District → Groupe local → Unité de base** ([lignes 114–154](../../EEDS_2529_Dossier_reference_application.md#L114-L154)).

Les organes cités comprennent notamment l'Assemblée Générale, le Comité Directeur, le Comité National, l'Équipe Nationale, les congrès et comités régionaux/de district, les équipes territoriales, le Conseil de groupe et les responsables d'unité ([lignes 156–215](../../EEDS_2529_Dossier_reference_application.md#L156-L215)).

Ces éléments sont documentés dans le dossier consolidé, mais les pièces statutaires originales ne sont pas toutes présentes séparément dans le dépôt. Ils ne doivent pas être déclarés comme une spécification vérifiée des sources originales sans rapprochement.

## 3. Hiérarchie attendue

| Niveau | Structure attendue | Statut |
|---|---|---|
| National | Assemblée Générale, Comité Directeur, Comité National, Équipe Nationale, Commissaire Général, commissions/secteurs/pools et instances nationales | Documenté dans le dossier ; arborescence exacte à confirmer |
| Régional | Région, Congrès régional, Comité régional, Équipe régionale, Commissaire régional et responsable(s) sectoriel(s) | Documenté ; rattachements et pouvoirs à confirmer |
| District | District, Congrès de District, Comité de District, Équipe de District, Commissaire de District et assistants | Documenté ; périmètres réels à configurer |
| Groupe local | Groupe, Conseil de groupe, chefs/responsables et unités dépendantes | Documenté ; divergence « une ou plusieurs unités » / « deux unités ou plus » |
| Unité | Mbootaay, Kayon, Ñawka ou Gàlle selon la branche | Terme et âge documentés ; capacités/mixité à confirmer |
| Sous-groupe | Clan, patrouille, ensemble ou sous-groupe selon branche | Proposition à valider ; aucune donnée exhaustive |
| Structure particulière | Structure libre/autonome/universitaire | Proposition ; statut et rattachement ouverts |

## 4. Matrice de vérification

| Niveau | Structure attendue | Structure existante | Parent | Sous-structures | Fonctions | Profils/droits | Statut |
|---|---|---|---|---|---|---|---|
| National | Niveaux et organes du dossier | Table EEDS `eeds_structures`, sans données nationales réelles | Aucun par défaut | Relation `parent_structure` possible | Non modélisées | Cinq permissions techniques | **Partiellement conforme** |
| Régional | Région réelle | Le plugin peut créer une structure type, nom et code | Parent possible | Relation parent/enfant possible | Hors périmètre R1 | Permissions + périmètre EEDS | **Nécessite une décision** |
| District | District réel | Même modèle générique | Peut avoir un parent | Peut avoir des enfants | Hors périmètre R1 | Non reliées aux fonctions | **Non implémenté pour l'organisation réelle** |
| Groupe local | Groupe réel avec unités | Table EEDS distincte des groupes Galette | Peut avoir un parent | Peut avoir des unités | Hors périmètre R1 | Pas de droit fonctionnel EEDS réel | **Partiellement conforme** |
| Unité | Mbootaay/Kayon/Ñawka/Gàlle | Champs membres `branch`/`unit` libres | Peut être reliée à une structure | Sous-groupes libres | Hors périmètre R1 | Aucun contrôle détaillé | **Proposition / non vérifié** |
| Organe | AG, Comités, Équipes, Congrès | Aucun type/mode EEDS dédié | Aucun modèle dédié | Aucun modèle dédié | Aucun modèle de mandat | Aucun mapping approuvé | **Non implémenté** |
| Mandat | Nomination, durée, autorité, remplacement | Absent | N/A | N/A | Absent | N/A | **Proposé pour R2** |
| Groupes Galette natifs | Groupes indépendants ou enfants | Implémentés dans Galette | `parent_group` | Hiérarchie native limitée | Gestionnaires de groupe | Rôles par groupe | **Natif mais distinct** |

## 5. État réellement créé

### Structures réelles du dossier

Aucune structure réelle ou territoire nommé n'a été créé à partir du dossier. Aucun utilisateur titulaire, aucune région/district/groupe réel ni aucune fonction n'a été configuré.

### Structure synthétique UAT

Dans la base jetable MySQL du 2026-11-22 ont été créés :

- une structure synthétique `UAT-SYNTH-NATIONAL` ;
- un périmètre synthétique `eeds:read` pour le compte de test `morpheus`;
- 55 membres Galette de fixtures fictives.

Ces éléments servent à démontrer techniquement le registre et le périmètre. Ils ne représentent ni l'organisation réelle des EEDS ni une validation métier.

## 6. Conclusion de conformité

- **Conforme :** identité membre Galette réutilisée ; table `parent_structure` ; clés étrangères ; périmètre descendant techniquement résolu ; trois états d'accès observés ;
- **Partiellement conforme :** hiérarchie parent/enfant ; historique des affiliations ; rôles et permissions ;
- **Non implémenté :** fonctions, mandats, délégations, organes, reparenting administré, arbre et responsables visibles ;
- **Non vérifié :** structure réelle des territoires, minimum d'unités par groupe, autorité de chaque fonction, règles de mandat ;
- **Nécessite une décision :** arborescence historique, types de relations autorisés, règles de cycle, données exportables, droits de chaque profil.

## 7. Documents associés

- [Hiérarchie organisationnelle](organizational-hierarchy.md)
- [Fonctions organisationnelles](organizational-functions.md)
- [Profils et permissions](user-profiles-and-permissions.md)
- [Analyse des écarts](organizational-gap-analysis.md)
- [Plan d'implémentation](organizational-implementation-plan.md)

