# Analyse des écarts organisationnels EEDS

- **Statut :** lecture comparative
- **Sources :** dossier EEDS, code Galette, plugiciel EEDS, UAT technique synthétique

## 1. Structure réelle du dossier

Les niveaux principaux sont National, Région, District, Groupe local et Unité de base. Les organes statutaires sont nommés, mais leurs rattachements exacts et les personnes titulaires ne sont pas fournis comme données approuvées.

Les noms, limites, statuts, rattachements et mandats doivent être administrables. Les structures libres/universitaires restent à confirmer.

## 2. Structure configurée dans Galette

### Natif

- membres Galette ;
- groupes Galette ;
- hiérarchie `parent_group` ;
- rôles et permissions ;
- permissions globales ou par groupe ;
- gestionnaires de groupe ;
- interfaces CRUD des groupes.

### Plugiciel EEDS

- structures EEDS avec `parent_structure` ;
- affiliations effective-datées ;
- périmètres EEDS effective-datés ;
- statut courant et historique ;
- renouvellements ;
- numéro membre ;
- aperçu des doublons ;
- audit d'export.

## 3. Matrice des écarts

| Domaine | Natif | Configuration | Adaptation | Développement | Statut |
|---|---|---|---|---|---|
| Membres | Existe | Réutilisable | Liaison EEDS | Workflow complet | Partiel |
| Groupes | Existe | Modèle générique | Pas de correspondance automatique | Liaison/choix à valider | Douteux |
| Hiérarchie | `parent_group` | Peut représenter un exemple | Mapping type/niveau | Historique, cycle, UI | Partiel |
| Structures EEDS | `eeds_structures` | Types/codes/noms/dates | Référentiels officiels | Reparentage, cycle, fermeture | Partiel |
| Branches/unités | Champs libres | Références à charger | Branches/âges | Validation/configuration | Proposé |
| Organes | Aucun | Aucun | Mapping à définir | Modèle d'organe | Absent |
| Fonctions | Aucun | Aucun | Aucun | Modèle fonction/nomination | Absent |
| Mandats | Aucun | Aucun | Aucun | Dates, renouvellement, délégation | Absent |
| Permissions | Existe | Rôles et groupes | Mapping EEDS | Contrôle objet/périmètre | Insuffisant |
| Export | Existent des exports génériques | Champ/table générique | Interdire l'export générique pour EEDS | Export réel en liste blanche | Gate R1 non implémenté |
| UI arborescente | Arbre de groupes dans Galette | Styles accessibles | Reprendre les conventions | Arbre EEDS, fiches, historique | Absent |
| Reporting | Galette dispose de listes/graphiques | Références à définir | Périmètres EEDS | Effectifs/renouvellements | Absent |

## 4. Fonctionnalités natives déjà disponibles

- CRUD de groupes avec arbre et détachement ;
- rôles système et permissions ;
- gestion globale ou par groupe ;
- historique des membres ;
- statut et cotisation Galette ;
- export/PDF/CSV générique ;
- plugin, routes, menu et interfaces Twig ;
- migrations MySQL/PostgreSQL.

## 5. Fonctionnalités nécessitant seulement une configuration

Sous réserve d'une source approuvée :

- référentiel des quatre branches ;
- types de structure autorisés ;
- noms de structure réels ;
- rattachements géographiques ;
- champs/droits d'un profil approuvé ;
- liste blanche d'export ;
- dates effective des affectations.

## 6. Fonctionnalités nécessitant une adaptation

- joindre les structures EEDS aux groupes Galette sans les confondre ;
- filtrer les listes au niveau SQL ;
- étendre les outils de reparentage ;
- appliquer les permissions sur les objets ;
- filtrer les candidats de doublon ;
- remplacer l'audit d'export par un fichier réel ;
- afficher les structures fermées/suspendues selon leur date.

## 7. Fonctionnalités nécessitant un développement

- historique des relations parent/enfant ;
- garde de cycles ;
- reconnaissance/suspension/fermeture ;
- fonction/nomination/mandat/délégation ;
- responsables visibles par structure ;
- vue arborescente et navigation ;
- reporting territorial ;
- tests inter-périmètre ;
- transactions atomiques pour renouvellement ;
- export réel borné par périmètre.

## 8. Risques architecturaux

1. Confondre groupe Galette, structure EEDS et mandat peut donner trop de droits.
2. Le champ `parent_structure` seul ne garantit pas l'historique.
3. Les dates ne protègent pas les périmètres contre les structures fermées.
4. Les mutations actuelles ne vérifient pas les objets et structures cibles.
5. Les doublons exposent potentiellement des données hors périmètre.
6. Les exports ne produisent pas de données mais enregistrent des métadonnées client.
7. Aucun modèle de fonction ne permet de déterminer qui peut décider quoi.

## 9. Priorités

1. Autorisation objet/périmètre sur toutes les routes.
2. Source des structures réelles et autorité compétente.
3. Modèle historique de relation structurelle.
4. Fonctions/mandats hors R1 mais dépendance R2.
5. Interface arborescente.
6. Export réel avec audit serveur.
7. Reporting territorial.

