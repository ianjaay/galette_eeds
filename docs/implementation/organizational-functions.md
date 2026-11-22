# Fonctions organisationnelles EEDS

- **Statut :** inventaire documentaire et proposition
- **Périmètre R1 :** les fonctions et mandats ne sont pas approuvés ni implémentés

## 1. Fonctions institutionnelles mentionnées

Le dossier cite notamment ([lignes 362–375](../../EEDS_2529_Dossier_reference_application.md#L362-L375)) :

- Commissaire Général ;
- Commissaires Nationaux ;
- responsable de pool / secteur ;
- Commissaire Régional ;
- Commissaire de District ;
- assistants de District ;
- responsables / chefs de Groupe ;
- responsables / assistants d'Unités ;

Les appellations exactes, les secteurs, les périmètres et les responsabilités détaillées peuvent évoluer. Elles ne constituent pas seules une affectation réelle ni une autorisation informatique.

Les responsables pédagogiques/conseillers de progression et les formateurs sont cités dans les profils et parcours proposés ([lignes 819–826](../../EEDS_2529_Dossier_reference_application.md#L819-L826)). Ils ne sont pas ajoutés à la liste stricte des fonctions statutaires [ligne 362–375](../../EEDS_2529_Dossier_reference_application.md#L362-L375) ; leur modèle reste donc une proposition à valider.

### Complément de l’annexe consolidée

L’annexe issue du dépôt historique GalleApp1/Hitobito énumère d’autres postes candidats : Président, Commissaire International, Trésorier National, Secrétaire Général, responsables de communication, digital, formation et programme, ainsi que des fonctions de trésorerie, secrétariat, matériel et relations avec les parents aux niveaux territoriaux ([annexe, lignes 2242–2254](../../EEDS_2529_Dossier_reference_application_CONSOLIDE_GITHUB.md#L2242-L2254)). Elle cite également Njiit, Reefaan et Rambeen pour les unités. L’annexe qualifie elle-même ces listes d’exemples non exhaustifs ; leur source et leur statut ne permettent pas de les traiter comme une nomenclature statutaire approuvée. Elles sont à rapprocher des statuts et à valider avant toute configuration.

L’annexe propose aussi un mapping technique PBS/Hitobito, des types de rôles et des permissions. Ces éléments sont historiques et spécifiques à Hitobito ; ils ne constituent ni des fonctions effectivement configurées dans Galette ni une matrice d’autorisations approuvée pour EEDS.

## 2. Modèle proposé

Le dossier propose de distinguer :

- personne ;
- fonction ;
- structure d'exercice ;
- nomination ;
- mandat ;
- dates du mandat ;
- autorité de nomination ;
- statut ;
- document justificatif ;
- remplacement ;
- délégation ;
- révocation ou fin de mandat.

Ce modèle est proposé aux [lignes 376–415](../../EEDS_2529_Dossier_reference_application.md#L376-L415). Il n'est pas présent dans le plugiciel R1 actuel.

## 3. Implémentation actuelle

### Absence de modèle de fonction

Aucune table EEDS de fonction ou mandat n'est présente. Aucun service ou route n'implémente :

- création ou modification d'une fonction ;
- nomination ;
- fin de mandat ;
- renouvellement ;
- cumul ;
- délégation ;
- intérim ;
- révocation motivée ;
- historique des responsables.

Les tables R1 contiennent des structures, périmètres techniques, affiliations, statuts et renouvellements. Elles ne représentent pas un poste statutaire.

### Périmètre technique distinct

`eeds_member_scopes` associe un membre, une structure et une permission effective. Cette donnée constitue un contrôle technique, pas un mandat :

- pas de fonction ;
- pas d'autorité de nomination ;
- pas de motif ;
- pas de durée de mandat ;
- pas de révocation historique structurée.

La même fonction peut exister dans plusieurs structures. Elle doit être contextuelle à la structure. Le code actuel ne relie pas une fonction à une structure.

## 4. Fonctions à chaque niveau

| Niveau | Fonctions citées | État |
|---|---|---|
| National | Commissaire Général, Commissaires Nationaux, responsables sectoriels/pools | Inventaire documentaire ; aucun titulaire ni mandat |
| Régional | Commissaire Régional, commissaires/assistants sectoriels | Inventaire documentaire ; périmètres ouverts |
| District | Commissaire de District, assistants et responsables sectoriels | Inventaire documentaire ; périmètres ouverts |
| Groupe local | Chef/responsable de groupe, responsables d'unité | Inventaire documentaire ; nombre et durée ouverts |
| Unité | Chef d'unité, assistants, responsables pédagogiques | Inventaire documentaire ; modèle de mandat absent |
| Pédagogique | Responsable pédagogique/conseiller, formateur | Proposé ; hors R1 |
| Organe | Président, membres de Comités, commissions | Documenté en partie ; modèle de mandat absent |

## 5. Règles à décider avant implémentation

1. Liste officielle des fonctions actives.
2. Autorité compétente pour chaque nomination.
3. Durée de mandat.
4. Conditions de renouvellement.
5. Autorisations de cumul.
6. Gestion des conflits d'intérêts.
7. Délégation et intérim.
8. Motif et niveau requis pour une révocation.
9. Fin automatique des droits à l'expiration.
10. Séparation entre mandat associative et permission informatique.

## 6. Recommandation

Ne pas traduire un titre EEDS en droit Galette sans validation. Le modèle de mandat relève clairement d'une phase ultérieure ou d'une sous-fonction R2, tel que prévu par [la feuille de route R1/R2](implementation-roadmap.md).

