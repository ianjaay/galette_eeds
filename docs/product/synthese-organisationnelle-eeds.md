# Synthèse organisationnelle EEDS pour le paramétrage Galette / R1

- **Statut du document :** synthèse opérationnelle, non statutaire
- **Base métier :** [dossier de référence EEDS](../../EEDS_2529_Dossier_reference_application.md)
- **Portée :** organiser les faits et les choix nécessaires au registre R1 ; ne remplace ni les statuts, ni leurs pièces sources, ni une décision formelle des EEDS.

## 1. Provenance et limites des sources

Le dossier se présente comme une consolidation de documents statutaires, du précis de branche Toor-Toor wi, de la stratégie, du plan d’action et d’échanges antérieurs. Il demande explicitement de distinguer les règles documentées, les éléments à confirmer et les propositions ([dossier, avertissement, lignes 1–8](../../EEDS_2529_Dossier_reference_application.md#L1-L8)), et précise qu’il ne constitue pas une spécification finale approuvée ([limites du dossier, lignes 1388–1400](../../EEDS_2529_Dossier_reference_application.md#L1388-L1400)).

Les pièces sources sous-jacentes ne sont pas toutes présentes séparément dans ce dépôt. La synthèse s’appuie donc sur le dossier consolidé et sur le contexte produit et technique du dépôt ; elle ne prétend pas avoir vérifié indépendamment chaque statut, précis, présentation ou plan cité ([contexte produit, ligne 132](project-context.md#L132) ; [liste des sources du dossier, à partir de la ligne 1452](../../EEDS_2529_Dossier_reference_application.md#L1452)).

Le dossier cite les originaux suivants : les statuts EEDS, le précis de branche TOOR TOOR WI — Programme national des jeunes, le document et la présentation de la nouvelle stratégie EEDS, le plan d’action EN-EEDS 2025, ainsi que l’historique des discussions et du cadrage `eeds_membership` / Frappe-ERPNext ([dossier, § 26, lignes 1452–1458](../../EEDS_2529_Dossier_reference_application.md#L1452-L1458)). Ces fichiers originaux n’ont pas été retrouvés séparément dans l’espace de travail consulté ; ils n’ont donc pas été vérifiés indépendamment.

Les catégories utilisées ci-dessous sont :

- **Fait documenté** : énoncé présenté par le dossier comme statutaire ou confirmé. À interpréter avec les sources EEDS faisant autorité.
- **Proposition** : piste de conception ou d’application, sans valeur de règle officielle tant qu’elle n’est pas approuvée.
- **Décision approuvée** : décision de projet rapportée comme approuvée, distincte d’une règle statutaire.
- **Ouvert / à confirmer** : point que les sources laissent indéterminé ou présentent de manière contradictoire.

## 2. Niveaux organisationnels, organes et responsabilités documentés

### Faits documentés dans le dossier

La vue hiérarchique décrite est **National → Région → District → Groupe local → Unité de base**. Le dossier cite à chaque niveau des organes et fonctions : notamment Assemblée Générale, Comité Directeur, Comité National et Équipe Nationale au niveau national ; congrès, comités et équipes régionaux et de district ; Conseil de groupe ; puis responsables d’unité. Il mentionne également le Forum national des jeunes, dont les modalités restent à détailler ([dossier, vue hiérarchique, lignes 114–155](../../EEDS_2529_Dossier_reference_application.md#L114-L155)).

Les responsabilités institutionnelles décrites comprennent notamment :

- **Assemblée Générale :** instance suprême, orientations du mouvement et élections conformément aux statuts.
- **Comité Directeur :** veille aux principes et orientations et à la conformité du programme avec les décisions de l’AG.
- **Comité National :** direction entre deux AG, approbation des projets de programme et de budget et contrôle de leur exécution.
- **Équipe Nationale :** organe exécutif de coordination, de mise en œuvre et de suivi.
- **Congrès et organes régionaux/de district :** fonctions d’orientation, d’élection, d’adoption ou de suivi telles que décrites dans le dossier.
- **Équipes régionale et de district :** coordination, exécution de décisions et tâches administratives ou de formation indiquées pour leurs niveaux.
- **Conseil de groupe local :** administration du groupe et coordination des responsables d’unités.

Ces formulations résument les rôles institutionnels décrits, sans attribuer de responsabilité à une personne nommée ([dossier, organes et instances, lignes 156–215](../../EEDS_2529_Dossier_reference_application.md#L156-L215)).

### Limites et propositions de modélisation

Le dossier dit que les noms, limites géographiques, rattachements et mandats réels doivent pouvoir être administrés ([dossier, lignes 149–155](../../EEDS_2529_Dossier_reference_application.md#L149-L155)). Le modèle de structures génériques, les types « structure autonome » ou « universitaire », les statuts de structure et l’historique des rattachements sont des **propositions de conception**, non des catégories toutes établies par les statuts ([proposition de modélisation, lignes 216–228](../../EEDS_2529_Dossier_reference_application.md#L216-L228)).

Le dossier ne fournit pas la liste des régions ou districts réels, leurs limites, leurs titulaires, ni une délégation nominative. Il ne faut donc pas créer de géographie nommée, d’utilisateur, de responsable individuel ou d’autorisation par supposition.

## 3. Branches, unités, terminologie et âges

### Faits documentés dans le dossier

| Branche | Terme pour le membre | Âge de référence du dossier | Unité |
|---|---|---:|---|
| Jiwu wi | Caat | 5–11 ans | Mbootaay |
| Lawtan wi | Arunga | 12–15 ans | Kayon |
| Toor-Toor wi | Jàmbaar | 16–18 ans | Ñawka |
| Meññeef mi | Mawdo | plus de 18 ans | Gàlle |

Références : [dossier, § 4.1 et terminologie, lignes 232–267](../../EEDS_2529_Dossier_reference_application.md#L232-L267).

Le dossier indique également les capacités et règles de mixité suivantes : Mbootaay, 32 caat, mixte ; Kayon, 32 arunga, non mixte ; Ñawka, 15 jàmbaar, unité mixte avec sous-groupes non mixtes ; Gàlle, 15 mawdo, mixte ([dossier, § 4.2, lignes 245–254](../../EEDS_2529_Dossier_reference_application.md#L245-L254)).

### Paramétrage proposé ou à confirmer

- Les âges sont des références à conserver, mais les seuils et transitions doivent rester configurables. Le dossier exclut une mutation automatique de branche fondée seulement sur l’âge.
- Le caractère normatif des capacités n’est pas décidé : le dossier demande de confirmer si elles sont indicatives ou contraignantes.
- Sous-groupes, clans, patrouilles, ensembles, leurs responsables et les règles de mixité paramétrables relèvent de propositions ([dossier, lignes 232–275](../../EEDS_2529_Dossier_reference_application.md#L232-L275)).
- Le précis de branche est résumé dans le dossier ; les intitulés pédagogiques détaillés ne doivent pas être reconstruits ni figés sans référence validée.

## 4. Catégories et cycle du membre

### Faits documentés dans le dossier

La qualité de membre est décrite comme reposant notamment sur l’adhésion libre et volontaire, l’acceptation des statuts et du règlement intérieur, l’appartenance à une unité ou structure, et la cotisation annuelle. Le dossier distingue les sympathisants, qui peuvent participer à certaines activités sans les mêmes obligations que les membres ([dossier, § 5.1, lignes 280–290](../../EEDS_2529_Dossier_reference_application.md#L280-L290)). Il indique aussi la perte de la qualité de membre par décès, démission ou radiation, cette dernière suivant les règles statutaires ([dossier, § 17, lignes 1154–1164](../../EEDS_2529_Dossier_reference_application.md#L1154-L1164)).

### Propositions, non décisions statutaires

Les champs envisagés pour une fiche nationale, les statuts détaillés (pré-inscrit, en attente, actif, suspendu, etc.), l’historique technique des changements et le parcours d’adhésion/renouvellement sont des propositions du dossier. Celui-ci demande de décider le processus d’adhésion et de validation, les pièces, les cotisations, le rôle du parent/tuteur et l’identifiant national ([dossier, lignes 291–357](../../EEDS_2529_Dossier_reference_application.md#L291-L357)).

En particulier, ne pas déduire du seul terme « membre », d’une tranche d’âge, d’une cotisation Galette ou d’un statut technique qu’une personne a été admise, renouvelée, transférée, radiée ou déclarée en règle selon une politique EEDS approuvée.

## 5. Profils, rôles et fonctions envisagés

Le dossier cite les fonctions institutionnelles suivantes : Commissaire Général, Commissaires Nationaux, Commissaire Régional, Commissaires Régionaux et assistants, Commissaire de District, assistants de district, responsables de groupe, responsables et assistants d’unité, responsables de secteurs ou de pools. Il précise que les titres peuvent évoluer et que fonctions, responsabilités et périmètres doivent pouvoir être configurés ([dossier, § 6.1, lignes 362–375](../../EEDS_2529_Dossier_reference_application.md#L362-L375)).

Le dossier propose de distinguer personne, fonction, nomination, structure, mandat, statut de nomination et document associé ; il propose aussi de conserver l’autorité, les dates, le motif de clôture et les remplacements ou délégations éventuels. Il décrit un cycle adulte d’identification, mobilisation, affectation, formation, accompagnement, reconnaissance, évaluation, renouvellement ou fin de fonction. Ce modèle et cet historique sont des **propositions**, non la preuve qu’un mandat ou une délégation particulière existe ([dossier, § 6.2–6.4, lignes 376–415](../../EEDS_2529_Dossier_reference_application.md#L376-L415)).

Les 16 profils ci-dessous sont **envisagés** par le dossier. Leurs responsabilités indiquées ne constituent pas une matrice de droits approuvée ; le dossier demande de croiser rôle et périmètre, mais sa matrice est explicitement indicative ([dossier, § 14, lignes 757–874](../../EEDS_2529_Dossier_reference_application.md#L757-L874)).

| Profil envisagé | Responsabilités ou accès décrits dans le dossier | Statut |
|---|---|---|
| Administrateur système | Configuration technique, comptes, permissions, référentiels, plateforme et journalisation des données sensibles | Profil envisagé ; droits à définir |
| Administrateur national / gestionnaire national | Registre national, structures, référentiels, reporting et workflows nationaux | Profil envisagé ; droits à définir |
| Commissaire Général / Équipe Nationale | Supervision nationale des structures, membres, stratégie, projets, rapports, nominations et décisions | Profil envisagé ; droits à définir |
| Commissaire National / responsable de pool | Périmètre sectoriel : activités, formations, projets, indicateurs et rapports | Profil envisagé ; périmètre et droits à définir |
| Commissaire Régional | Région, districts, cadres, groupes, unités, validations, activités et reporting | Profil envisagé ; périmètre et droits à définir |
| Responsable régional / commissaire sectoriel régional | Activités d’un secteur régional avec accès limité | Profil envisagé ; délégation à définir |
| Commissaire de District | District, groupes, responsables, unités, effectifs, validations, suivi et coordination | Profil envisagé ; périmètre et droits à définir |
| Assistant de District / responsable sectoriel | Actions dans son périmètre, selon délégation | Profil envisagé ; délégation à définir |
| Chef de Groupe / responsable de groupe local | Unités, membres, responsables, adhésions, cotisations, activités et reporting | Profil envisagé ; périmètre et droits à définir |
| Chef d’Unité / responsable d’unité | Membres, progression, activités, présences, informations administratives, communication et reporting | Profil envisagé ; périmètre et droits à définir |
| Responsable pédagogique / conseiller de progression | Suivi et validation de progression, données pédagogiques nécessaires | Profil envisagé ; périmètre et droits à définir |
| Formateur | Sessions, participants, présences, évaluations et données de formation | Profil envisagé ; périmètre et droits à définir |
| Membre adulte | Profil, informations personnelles, nominations, formations, progression, activités et candidatures | Profil envisagé ; accès à définir |
| Jeune membre | Profil limité, progression, activités, candidatures, ressources pédagogiques et communications ; pas d’accès aux autres membres | **Proposition** du dossier |
| Parent / tuteur | Accès limité aux enfants rattachés, adhésions, activités, autorisations et cotisations ; pas de visibilité sur les autres membres | **Proposition à valider** |
| Ancien / ami / sympathisant | Accès limité aux informations ou activités autorisées ; pas d’assimilation automatique à un membre actif ou à un cadre | Profil envisagé ; accès à définir |

Références : [dossier, profils, lignes 764–852](../../EEDS_2529_Dossier_reference_application.md#L764-L852), [matrice indicative, lignes 853–874](../../EEDS_2529_Dossier_reference_application.md#L853-L874). Aucun titulaire nominatif ni aucune attribution de compte n’est documenté.

**Conséquence pour Galette :** ne pas convertir directement un titre institutionnel en rôle Galette ni attribuer des droits à un utilisateur avant d’avoir une décision sur l’autorité, le périmètre, la délégation, la durée et les permissions correspondantes. Le dossier ne désigne aucun titulaire nominal.

## 6. Règles documentées à préserver

Sous réserve de validation auprès des sources statutaires originales, le dossier consigne les règles de référence suivantes :

1. Quatre branches et leurs termes de membre, unités et tranches d’âge de référence.
2. Des indications statutaires de capacité et de mixité des unités, dont le caractère contraignant reste à clarifier.
3. La cotisation annuelle comme élément de la qualité de membre en règle ; les sympathisants n’ont pas nécessairement les mêmes obligations.
4. Une perte de la qualité de membre possible par décès, démission ou radiation, suivant le niveau compétent et les règles applicables.
5. Des instances, fonctions et responsabilités distinctes à plusieurs niveaux organisationnels.
6. Le besoin de protection renforcée des données des mineurs et de ne pas présumer les droits d’un parent/tuteur, présenté par le dossier comme une exigence de conception et de protection, non comme une règle statutaire.

Références : [branches et unités, lignes 232–254](../../EEDS_2529_Dossier_reference_application.md#L232-L254) ; [qualité de membre, lignes 280–290](../../EEDS_2529_Dossier_reference_application.md#L280-L290) ; [règles documentées, lignes 1152–1164](../../EEDS_2529_Dossier_reference_application.md#L1152-L1164) ; [protections des mineurs, lignes 347–357](../../EEDS_2529_Dossier_reference_application.md#L347-L357).

## 7. Propositions de projet pour R1

La proposition R1 du dépôt cadre le registre des structures et des membres, les rattachements historisés, les références branches/unités, le cycle d’adhésion, les transitions, le transfert, l’examen de doublons et les recherches/exports contrôlés ; elle exclut notamment les mandats et la gouvernance, la comptabilité complète, la progression, la formation, les activités, les projets et les portails ([proposition R1, lignes 1–37](../implementation/r1-policy-proposal.md#L1-L37)).

Ces éléments sont ici traités comme **périmètre de conception R1 approuvé selon le contexte de décision fourni par le propriétaire**, et non comme règles statutaires. Le propriétaire a explicitement approuvé la proposition R1 « telle que rédigée » dans la conversation précédente. Cette approbation ne valide pas les rôles nominaux, les titulaires, les autorisations particulières, ni les règles que le dossier laisse ouvertes ; elle ne vaut pas non plus preuve de recette utilisateur (UAT).

Le statut de cette décision a été reporté dans [la proposition R1](../implementation/r1-policy-proposal.md#L1-L13) et [le registre des décisions](../implementation/r0-owner-decision-register.md#L32-L45). Cette approbation est une décision de projet, pas une règle statutaire ; les règles métier encore ouvertes et les affectations nominatives restent à décider.

## 8. Questions ouvertes et divergences à trancher

| Sujet | État, question ou divergence | Référence |
|---|---|---|
| Hiérarchie des groupes | La vue générale mentionne une ou plusieurs unités dans un groupe local, tandis que la description du Conseil dit que le groupe comprend deux unités ou plus. Quel seuil fait autorité ? | [lignes 149–153](../../EEDS_2529_Dossier_reference_application.md#L149-L153) et [211–214](../../EEDS_2529_Dossier_reference_application.md#L211-L214) |
| Géographie et structures | Quels sont les noms, limites, rattachements, statuts et autorités de mise à jour réels ? Aucune géographie nommée ne peut être extrapolée du dossier. | [lignes 149–155](../../EEDS_2529_Dossier_reference_application.md#L149-L155) |
| Capacités et mixité | Les valeurs sont indiquées comme statutaires mais leur usage comme seuils bloquants ou repères configurables est renvoyé à confirmation. | [lignes 245–254](../../EEDS_2529_Dossier_reference_application.md#L245-L254) |
| Âges et transitions | Les références d’âge sont listées, mais la règle de passage de branche et l’autorité de validation ne sont pas fixées ; pas de transfert automatique par âge. | [lignes 232–254](../../EEDS_2529_Dossier_reference_application.md#L232-L254) et [1165–1184](../../EEDS_2529_Dossier_reference_application.md#L1165-L1184) |
| Adhésion, renouvellement, cotisation | Processus, période, pièces, montants, exemptions, reçus, délai de grâce et effet d’un impayé restent à décider. | [lignes 332–345](../../EEDS_2529_Dossier_reference_application.md#L332-L345) et [1165–1172](../../EEDS_2529_Dossier_reference_application.md#L1165-L1172) |
| Statuts et radiation | Les catégories détaillées sont proposées ; les transitions, l’autorité compétente, la suspension, la réactivation et les conséquences de radiation ne sont pas spécifiées complètement. | [lignes 316–345](../../EEDS_2529_Dossier_reference_application.md#L316-L345) et [1160–1184](../../EEDS_2529_Dossier_reference_application.md#L1160-L1184) |
| Rôles, mandats et permissions | Les profils et la matrice sont indicatifs. Il faut approuver les rôles, titulaires, autorités de nomination, mandats, cumuls, délégations et droits par périmètre. | [lignes 362–402](../../EEDS_2529_Dossier_reference_application.md#L362-L402) et [757–874](../../EEDS_2529_Dossier_reference_application.md#L757-L874) |
| Identifiants, doublons et transferts | Format d’identifiant, critères de doublon, procédure de rapprochement et règles de mutation restent à valider. | [adhésion et identifiant, ligne 345](../../EEDS_2529_Dossier_reference_application.md#L345) ; [règles à valider, lignes 1165–1184](../../EEDS_2529_Dossier_reference_application.md#L1165-L1184) |
| Mineurs et conservation | Données nécessaires, relation parent/tuteur, autorisations, accès, conservation, archivage et suppression exigent une décision de protection et de gouvernance. | [lignes 347–357](../../EEDS_2529_Dossier_reference_application.md#L347-L357) et [1165–1184](../../EEDS_2529_Dossier_reference_application.md#L1165-L1184) |
| Approbation R1 | La politique R1 est approuvée comme décision de projet ; cette approbation ne ferme pas les questions métier explicitement laissées ouvertes par le dossier et n’approuve pas de titulaires individuels. | [proposition R1, lignes 1–13](../implementation/r1-policy-proposal.md#L1-L13) ; [registre, lignes 32–45](../implementation/r0-owner-decision-register.md#L32-L45) |

## 9. Implications Galette et validation UAT

### Configuration et limites d’implémentation

- L’architecture approuvée du dépôt est un domaine EEDS isolé dans un plugiciel Galette, qui réutilise notamment l’identité membre Galette ; c’est une décision d’architecture, pas une règle d’organisation EEDS ([ADR-0001, décision et modèle de données](../engineering/adr/ADR-0001-eeds-plugin-domain-boundary.md#L1-L57)).
- Le code présent contient une base de registre avec tables et services de structures, périmètres, affiliations, cycle membre, renouvellements, doublons et journal d’export ([définition du plugiciel](../../galette/plugins/eeds/_define.php#L18-L31) ; [schéma MySQL](../../galette/plugins/eeds/scripts/mysql.sql#L7-L142)). Leur présence ne signifie pas que tous les parcours ou politiques R1 sont validés en UAT.
- Les noms logiques `eeds_*` utilisés dans le code sont préfixés automatiquement par Galette ; les tables physiques `galette_eeds_*` dans la base observée sont donc attendues avec `PREFIX_DB=galette_`. Ce n’est pas un défaut de préfixe ([services et noms logiques](../../galette/plugins/eeds/lib/EEDSFoundation/Domain/StructureService.php#L48-L68) ; [schéma physique](../../galette/plugins/eeds/scripts/mysql.sql#L7-L25)).
- Garder les références d’âge, de branche et d’unité configurables, sans migration automatique liée à l’âge. Ne pas traiter capacités ou mixité comme des contraintes bloquantes avant décision.
- Le principe R1 « permission et périmètre » est approuvé ; les titres métiers du dossier ne déterminent toutefois pas, à eux seuls, les permissions effectives. Ne pas attribuer de comptes, titulaires ou rattachements géographiques sans source et autorité validées, ni configurer des accès mineurs sur la base de cette synthèse seule.
- Maintenir les validations MySQL/MariaDB et PostgreSQL prescrites pour les changements de base ([spécification R1, critères et validation](../implementation/release-specifications/release-01-core-membership.md#L53-L75)).

### État UAT rapporté et interprétation du 403

Les observations vérifiées communiquées pour l’instance temporaire sont :

1. L’instance `bin/serve` avec MySQL sur le port **8128** répond.
2. La connexion administrateur atteint le tableau de bord.
3. `/plugins/eeds/` renvoie **403**.
4. Les tables `galette_eeds_*` sont présentes ; les tables de structures et de périmètres sont vides (**0 ligne**).

Le diagnostic le plus probable du 403 est l’absence de périmètre EEDS : le contrôle ACL semble passer, mais la route renvoie aussi 403 si la résolution du périmètre est vide ([contrôleur, ACL et test du périmètre, lignes 40–57](../../galette/plugins/eeds/lib/EEDSFoundation/Controllers/RegistryController.php#L40-L57) ; [résolution via `eeds_member_scopes`, lignes 69–98](../../galette/plugins/eeds/lib/EEDSFoundation/Access/EedsScope.php#L69-L98)). Cela reste une **inférence à confirmer**, pas un diagnostic établi. Le fait que les tables soient vides est cohérent avec cette hypothèse, sans la démontrer à lui seul.

### Suite de validation UAT

| Scénario | Prérequis | Résultat attendu / preuve à consigner |
|---|---|---|
| Distinguer ACL et périmètre | Utilisateurs et données synthétiques ; vérifier d’abord sans périmètre, puis avec un périmètre daté valide | Établir si le contrôle ACL passe et si la résolution du périmètre change le résultat de `/plugins/eeds/` ; consigner le statut observé |
| Parcours du registre | Structure et membre fictifs, uniquement dans la base de test et clairement marqués comme synthétiques | Vérifier création de structure, rattachement, recherche limitée, transfert, transition de statut et historique ; ne pas présenter ces données comme la structure réelle |
| Accès hors périmètre | Structures sœurs/non autorisées synthétiques | Confirmer le refus sur les listes et objets hors périmètre |
| Doublons et export | Membres synthétiques, champ d’export explicitement autorisé | Confirmer aperçu sans fusion automatique, export limité et trace d’audit |
| Compatibilité de base | Environnements de test MySQL/MariaDB et PostgreSQL | Vérifier migrations à blanc/répétées et préserver l’historique |

**Résultats vérifiés dans l’environnement temporaire :**

- Connexion administrateur : HTTP 200 sur le tableau de bord.
- Création synthétique et limitée : une structure `UAT-SYNTH-NATIONAL` et
  un périmètre `eeds:read` daté ont été ajoutés dans la base jetable.
- Compte synthétique `morpheus` : `/plugins/eeds/` retourne HTTP 200 et affiche
  la structure UAT.
- Compte synthétique `luke.skywalker` sans permission ni périmètre : HTTP 302.
- Super-administrateur sans périmètre : HTTP 403.
- Compte synthétique autorisé : aperçu des doublons HTTP 200 avec une liste
  vide dans les données UAT ; compte sans droit redirigé en HTTP 302.
- Liste blanche d'export : appel autorisé HTTP 200 avec trace d'audit ;
  champ `national_number` refusé en HTTP 403.
- La trace d'export existe dans `galette_eeds_export_audit`, mais aucun
  fichier PDF/CSV réel n'a été produit ni vérifié.
- Correction du template EEDS : remplacement du filtre Twig `|trans` par
  `_T(...)`; `twigcs` passe.

**Non exécuté dans cette synthèse :** les scénarios ci-dessus sont des contrôles à réaliser, pas des résultats acquis. N’utiliser aucune donnée réelle de jeunes ou de tuteurs. Consigner séparément les observations, les échecs et les décisions ; l’UAT métier complète n’est pas réputée acquise.

## Références de projet

- [Contexte produit](project-context.md)
- [Proposition de politique R1](../implementation/r1-policy-proposal.md)
- [Registre des décisions propriétaire](../implementation/r0-owner-decision-register.md)
- [Spécification R1 et audit d’implémentation](../implementation/release-specifications/release-01-core-membership.md)
- [Plan UAT](../implementation/user-acceptance-testing-plan.md)
- [ADR-0001 — périmètre du plugiciel EEDS](../engineering/adr/ADR-0001-eeds-plugin-domain-boundary.md)
