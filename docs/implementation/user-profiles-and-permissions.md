# Profils utilisateurs et permissions

- **Statut :** inventaire technique et sécurité
- **Décision :** les quatre profils nommés ci-dessous sont approuvés uniquement pour l'export R1. Les titulaires, mandats et autres droits restent hors R1.

## 1. Mechanismes natifs Galette

Galette utilise des rôles système, des rôles explicites, des permissions nommées et un résolveur de droits. Les permissions peuvent être globales ou affectées à des groupes.

Le super-administrateur reçoit toutes les permissions. Les rôles système reçoivent les permissions nouvelles selon leur niveau d'accès. Un rôle de groupe peut accorder une permission sur un groupe seulement.

## 2. Profils et rôles d'export R1

Le propriétaire a approuvé l'accès à l'export XLSX pour les profils suivants :

- Administrateur central/national ;
- Commissaire de région ;
- Commissaire de district ;
- Commissaire de groupe local.

Le plugiciel initialise un rôle Galette dédié par profil. Ces rôles reçoivent
`eeds:read` et `eeds:export`, nécessaires pour ouvrir le registre et lancer
l'export. Ils ne sont affectés à aucun membre automatiquement. Les attributions
de rôles aux personnes et les scopes EEDS effectifs doivent être configurés
séparément par l'administrateur.

Chaque scope reste limité aux structures définies dans
`eeds_member_scopes`, aux dates d'effet et aux descendants en vigueur. Aucun
nom de profil ne confère à lui seul un accès national ou territorial. Les
postes, titulaires, mandats, délégations et autres permissions restent à
concevoir en R2.

Le droit `eeds:export` n'est pas accordé par défaut au rôle système Staff.
L'upgrade du plugiciel retire cette attribution générale et initialise les
quatre rôles dédiés. Le super-administrateur Galette conserve le contournement
standard des permissions du cœur, mais le scope EEDS reste exigé par les
routes du plugiciel.

Le dossier propose par ailleurs, sans les confirmer comme matrice définitive
([lignes 757–874](../../EEDS_2529_Dossier_reference_application.md#L757-L874)) :

- administrateur système ;
- administrateur national / gestionnaire national ;
- Commissaire Général / Équipe Nationale ;
- Commissaire National / responsable de pool ;
- Commissaire Régional ;
- responsable régional / commissaire sectoriel ;
- Commissaire de District ;
- assistant de District / responsable sectoriel ;
- chef de Groupe ;
- chef d'Unité ;
- responsable pédagogique ;
- formateur ;
- membre adulte ;
- jeune membre ;
- parent/tuteur ;
- ancien/ami/sympathisant.

Ces intitulés ne valent pas droit technique ni titulaire actuel.

## 3. Permissions techniques du plugiciel

Le plugiciel expose actuellement cinq permissions :

- `eeds:read`
- `eeds:structure-create`
- `eeds:member-edit`
- `eeds:validate`
- `eeds:export`

Les permissions de lecture, création, modification et validation conservent
leurs niveaux Galette existants. `eeds:export` est déclaré comme
super-administrateur uniquement pour empêcher son attribution automatique à
tout le personnel ; les quatre rôles dédiés le reçoivent explicitement. Les
permissions EEDS peuvent s'appliquer à un scope de groupe, mais les
autorisations EEDS effectives restent résolues dans `eeds_member_scopes`.

## 4. Résultat UAT technique

Dans la base jetable MySQL du 2026-11-22 :

- `morpheus` avec un périmètre synthétique `eeds:read` a obtenu HTTP 200 sur `/plugins/eeds/`;
- `luke.skywalker` sans permission/perimètre a obtenu HTTP 302;
- le super-administrateur sans périmètre a obtenu HTTP 403;
- une structure synthétique `UAT-SYNTH-NATIONAL` a été affichée.

Ces tests prouvent seulement le comportement de la lecture de registre. Les
tests unitaires du service d'export vérifient la création XLSX, l'audit et le
traitement des cellules comme texte. Les tests route-à-route et les tests
d'intégration d'export sur MySQL/PostgreSQL restent à exécuter.

## 5. Contrôles et limites de sécurité

- Le serveur résout le scope par permission, membre, date d'effet et hiérarchie ; les identifiants de structure soumis par le client ne déterminent pas les lignes exportées.
- L'export inclut les champs applicatifs non secrets approuvés, les affiliations actives/en vigueur dans le scope et une entrée d'audit avec les champs, structures et nombre de lignes.
- Aucun rôle n'est affecté à une personne et aucun scope territorial n'est créé automatiquement ; il faut configurer ces deux éléments avant l'utilisation.
- Les données réelles ou personnelles de mineurs ne doivent pas être créées ni utilisées pour le développement ou les tests sans validation préalable.
- Les tests unitaires ne remplacent pas une recette avec données synthétiques sur les deux moteurs de base.

## 6. Délégations et héritage

- le périmètre EEDS descendant peut être étendu aux enfants ;
- le périmètre ascendant n'est pas accordé ;
- les dates du périmètre sont vérifiées ;
- les dates/statuts des structures elles-mêmes ne sont pas vérifiés par le parcours ;
- les permissions de groupes Galette sont distinctes des périmètres EEDS ;
- les délégations statutaires ne sont pas modélisées.

## 7. Matrice des autres fonctions (à valider)

| Profil proposé | Périmètre | Actions possibles | Statut |
|---|---|---|---|
| Administrateur technique | technique uniquement | plateforme, configuration, journalisation | À définir |
| Administrateur central/national | scope EEDS attribué | export R1 uniquement | Rôle d'export approuvé ; autres droits à définir |
| Commissaire de région | région et descendants attribués | export R1 uniquement | Rôle d'export approuvé ; autres droits à définir |
| Commissaire de district | district et descendants attribués | export R1 uniquement | Rôle d'export approuvé ; autres droits à définir |
| Commissaire de groupe local | groupe et descendants attribués | export R1 uniquement | Rôle d'export approuvé ; autres droits à définir |
| Chef de groupe | groupe et unités | membre/activité selon droits | À définir |
| Chef d'unité | unité | affectation, lecture, validation limitée | À définir |
| Responsable pédagogique | structures/donnees autorisées | progression | Hors R1 |
| Membre adulte | propre profil | lecture/modification des champs autorisés | À définir |
| Jeune | propre profil | portail futur | Proposition |
| Parent/tuteur | liens vérifiés | consultation limitée | Proposition |

## 8. Recommandations de sécurité

1. Centraliser le contrôle : permission + périmètre utilisateur + périmètre objet.
2. Résoudre les structures autorisées côté serveur, jamais depuis les identifiants fournis.
3. Filtrer les listes et doublons avant les données personnelles.
4. Appliquer le périmètre aux mutations.
5. Prévenir le contournement du super-administrateur avec une règle explicite de périmètre.
6. Ajouter des tests route, objet, liste, doublon et export.
7. Séparer mandat associatif et rôle informatique.

