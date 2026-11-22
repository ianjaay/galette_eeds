# Profils utilisateurs et permissions

- **Statut :** inventaire technique et sécurité
- **Décision :** aucune matrice fonction → permission → périmètre n'est approuvée pour les profils nominatifs

## 1. Mechanismes natifs Galette

Galette utilise des rôles système, des rôles explicites, des permissions nommées et un résolveur de droits. Les permissions peuvent être globales ou affectées à des groupes.

Le super-administrateur reçoit toutes les permissions. Les rôles système reçoivent les permissions nouvelles selon leur niveau d'accès. Un rôle de groupe peut accorder une permission sur un groupe seulement.

## 2. Profils EEDS envisagés

Le dossier propose, mais ne confirme pas comme matrice définitive ([lignes 757–874](../../EEDS_2529_Dossier_reference_application.md#L757-L874)) :

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

Ces permissions sont déclarées avec niveau Staff et portée de groupe potentielle. Elles ne représentent pas les fonctions institutionnelles détaillées du dossier.

## 4. Résultat UAT technique

Dans la base jetable MySQL du 2026-11-22 :

- `morpheus` avec un périmètre synthétique `eeds:read` a obtenu HTTP 200 sur `/plugins/eeds/`;
- `luke.skywalker` sans permission/perimètre a obtenu HTTP 302;
- le super-administrateur sans périmètre a obtenu HTTP 403;
- une structure synthétique `UAT-SYNTH-NATIONAL` a été affichée.

Ces tests prouvent seulement le comportement de la lecture de registre. Ils ne prouvent pas les mutations, le filtrage SQL des objets ou les exports.

## 5. Points de sécurité observés

### Critique

Les opérations de création, transition, transfert, renouvellement, numéro membre, doublon et export vérifient principalement une permission générale. Elles ne vérifient pas systématiquement le périmètre EEDS de l'objet ciblé.

### Élevé

Le registre résout et filtre les structures du périmètre. Les autres routes ne le font pas avant la mutation. Une structure parent, un membre cible ou une structure destination peuvent être hors périmètre.

### Moyen

La prévisualisation des doublons charge tous les membres sans filtre de périmètre. Elle doit filtrer les membres avant comparaison.

### Moyen

L'export actuel n'émet pas de fichier de données. Il accepte des structures et un nombre de lignes déclarés par le client pour produire une entrée d'audit. La politique d'export ne résout pas le périmètre du demandeur.

## 6. Délégations et héritage

- le périmètre EEDS descendant peut être étendu aux enfants ;
- le périmètre ascendant n'est pas accordé ;
- les dates du périmètre sont vérifiées ;
- les dates/statuts des structures elles-mêmes ne sont pas vérifiés par le parcours ;
- les permissions de groupes Galette sont distinctes des périmètres EEDS ;
- les délégations statutaires ne sont pas modélisées.

## 7. Matrice proposée à valider

| Profil proposé | Périmètre | Actions possibles | Statut |
|---|---|---|---|
| Administrateur technique | technique uniquement | plateforme, configuration, journalisation | À définir |
| Administrateur national | national | structure/lire/créer selon autorité | À définir |
| Commissaire régional | région et descendants | lecture/validation dans périmètre | À définir |
| Commissaire de district | district et descendants | lecture/validation selon délégation | À définir |
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

