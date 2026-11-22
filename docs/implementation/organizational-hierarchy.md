# Hiérarchie organisationnelle EEDS

- **Statut :** arborescence attendue proposée à partir du dossier ; non approuvée comme nomenclature réelle
- **Dépendances :** source géographique officielle, autorité de création, règles de réparentage

## 1. Hiérarchie documentée

La hiérarchie principale décrite par le dossier est :

```text
Hiérarchie territoriale documentée
└── National
    └── Région
        └── District
            └── Groupe local
                └── Unité de base

Instances et organes à représenter séparément
├── Assemblée Générale
├── Comité Directeur
├── Comité National
├── Équipe Nationale
├── Congrès / Comités / Équipes régionaux
├── Congrès / Comités / Équipes de district
├── Conseil de groupe
└── Forum national des jeunes (modalités à confirmer)
```

La juxtaposition ci-dessus ne signifie pas que chaque organe est un enfant direct de « National ». Les organes sont des instances ayant leur composition, leur mandat et leurs compétences propres ; leurs rattachements exacts restent à décider ([lignes 156–215](../../EEDS_2529_Dossier_reference_application.md#L156-L215)).

## 2. Structures Galette natives

Galette possède une hiérarchie native de groupes avec `parent_group`. Une entité peut avoir un parent, rechercher ses enfants, obtenir son nom complet, devenir indépendante ou être déplacée. Les mouvements peuvent être refusés en cas de cycle.

Cette hiérarchie est visible dans [Group.php](../../galette/lib/Galette/Entity/Group.php) et persistée dans les scripts SQL par `galette_groups.parent_group`. Elle reste une hiérarchie de **groupes Galette**, indépendante du modèle EEDS. L'appartenance directe d'un membre à un groupe n'implique pas automatiquement son appartenance à tous les parents ou enfants.

## 3. Modèle EEDS actuel

Le plugiciel ajoute une relation EEDS distincte :

```text
eeds_structures
└── parent_structure → eeds_structures.id_structure
```

Le champ est récursif et permet plusieurs niveaux techniques. `EedsScope` parcourt les descendants des structures affectées à l'utilisateur. Cette capacité permet une vraie relation parent-enfant en base, mais elle ne fournit pas encore :

- historique des relations parent/enfant ;
- reparentage avec motif et autorité ;
- fermeture/retrait de structure ;
- vérification des statuts et dates des descendants ;
- détection applicative complète des cycles ;
- types de relations autorisés ;
- vue d'arbre navigable ;
- fil d'Ariane ;
- responsable(s) d'une structure ;
- rendu des descendants par rapport à une date effective.

## 4. Comparaison des hiérarchies

| Besoin | Groupe Galette natif | Structure EEDS actuelle | Écart |
|---|---|---|---|
| Structure racine | Oui | Oui | Aucun écart technique de base |
| Plusieurs niveaux | Oui | `parent_structure` récursif | Historique et gouvernance absents |
| Affectation d'un membre | Oui | `eeds_member_affiliations` | Modèles distincts |
| Apparentement descendant | Dépend du périmètre de groupe | Parcours descendant dans `EedsScope` | Implémentation différente |
| Dates effective | Limité | Champs présents | Relations parent-enfant non historiquees |
| Cycle interdit | Vérifié côté entité Group | Absence de garde complète | Correctif requis |
| Navigation en arbre | Interfaces Galette génériques | Tableaux plats | Développement UI requis |
| Reparentage métier | Déplacement de groupe | Aucun workflow | Développement requis |
| Mandats | Gestionnaires de groupe, pas mandats | Aucun modèle | Hors R1 |

## 5. Branches et unités

Les quatre références du dossier peuvent être représentées comme données configurables :

| Branche | Terme | Âge de référence | Unité |
|---|---|---:|---|
| Jiwu wi | Caat | 5–11 | Mbootaay |
| Lawtan wi | Arunga | 12–15 | Kayon |
| Toor-Toor wi | Jàmbaar | 16–18 | Ñawka |
| Meññeef mi | Mawdo | Plus de 18 | Gàlle |

Ces valeurs ne doivent pas déclencher une mutation automatique ni devenir des contraintes de capacité avant décision.

## 6. Structures effectivement présentes

### Environnement UAT

Une seule structure synthétique existe dans la base jetable :

- code : `UAT-SYNTH-NATIONAL`
- nom : `Structure nationale UAT synthétique`
- type : `national`
- statut : `recognized`

Elle ne représente pas une structure nationale réelle des EEDS.

## 7. Écarts bloquants

1. La hiérarchie réelle des régions, districts et groupes n'est pas fournie comme données approuvées.
2. Aucun nom officiel ni limite territoriale n'a été chargé.
3. Les relations parent-enfant historiques ne sont pas modélisées.
4. Les mutations de structure ne sont pas prévues dans l'interface.
5. Les organes institutionnels ne sont pas distingués des structures territoriales.
6. Les structures libres/universitaires restent des propositions.

## 8. Hypothèse technique proposée, non approuvée

Conserver `eeds_structures.parent_structure` comme arborescence canonique courte terme, puis ajouter si validé :

- une table historisée des relations parent/enfant ;
- effective start/end sur les relations ;
- garde contre les cycles ;
- événement de reparentage ;
- filtrage par dates/statuts ;
- vue en arbre avec navigation ;
- rapprochement avec les groupes Galette uniquement si ce rapprochement est validé.

