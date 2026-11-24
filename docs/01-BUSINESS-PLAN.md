# Business Plan — SuguPro

> **SuguPro** — « Votre supermarché, piloté au franc près. »
> ERP + POS SaaS pour supérettes et supermarchés d'Afrique de l'Ouest (UEMOA).
> Document de référence — Version 1.0 — Novembre 2026

| Élément | Valeur |
|---|---|
| Nom commercial | **SuguPro** (de *sugu*, « marché » en bambara) |
| Forme juridique cible | SARL de droit malien (OHADA), extension UEMOA |
| Siège | Bamako, Mali |
| Marchés pilotes | Bamako (Mali), Dakar (Sénégal), Abidjan (Côte d'Ivoire) |
| Devise de facturation | Franc CFA (XOF), sans décimales |
| Modèle | Abonnement SaaS mensuel/annuel + frais d'installation + matériel |
| Ticket d'entrée | 25 000 FCFA / mois (Pack Starter) |
| Horizon du prévisionnel | 3 ans (2027 – 2029) |

---

## 1. Executive Summary

Le commerce de détail alimentaire ouest-africain repose encore massivement sur le **cahier de caisse**, le **calcul mental** et des **tableurs Excel** partagés par WhatsApp. Résultat : démarque inconnue de 2 à 6 % du chiffre d'affaires, ruptures de stock sur les produits à forte rotation (riz, huile, sucre, lait), ventes à crédit non tracées et impossibilité de connaître la marge réelle par rayon.

**SuguPro** est une application web responsive (utilisable sur PC de caisse, tablette et smartphone) qui digitalise l'intégralité du cycle de vente d'un supermarché :

1. **Caisse (POS)** tactile avec scan code-barres, multi-modes de paiement (espèces, Orange Money, Wave, Moov Money, carte) et ticket thermique 58/80 mm ;
2. **Stock temps réel** avec lots, dates de péremption et alertes de rupture ;
3. **Achats et fournisseurs** avec suivi des dettes ;
4. **Clients et ardoise numérique** (vente à crédit) avec relances automatiques ;
5. **Reçus et alertes WhatsApp/SMS** ;
6. **Tableau de bord gérant** : CA, marge, top produits, performance caissiers.

Le modèle économique est un **abonnement SaaS en FCFA** (25 000 à 100 000 FCFA/mois), complété par des frais d'installation et la revente de matériel de caisse. L'objectif est de **600 supermarchés abonnés en 3 ans**, soit un revenu récurrent annuel (ARR) d'environ **288 millions FCFA**, avec un point mort atteint au **15ᵉ mois**.

**Demande de financement : 18 000 000 FCFA** pour 18 mois (produit, déploiement commercial, matériel de démonstration).

---

## 2. Le problème

### 2.1 Douleurs constatées sur le terrain

| # | Douleur | Impact mesurable |
|---|---|---|
| P1 | **Démarque inconnue** (vol, casse, erreurs de prix, tickets non émis) | 2 à 6 % du CA, soit 600 000 à 1 800 000 FCFA/mois pour un magasin à 30 M FCFA de CA |
| P2 | **Ruptures et surstocks** faute de suivi | 8 à 15 % du CA perdu sur les références à forte rotation |
| P3 | **Péremption** non anticipée (lait, yaourt, huile, conserves) | 1 à 3 % du CA en produits jetés |
| P4 | **Crédits clients non tracés** (ardoises papier) | 3 à 10 % du CA immobilisé, impayés 5 à 12 % |
| P5 | **Multi-caisses non consolidées** | Clôture de journée manuelle, 1 à 3 h perdues, écarts de caisse |
| P6 | **Aucune visibilité sur la marge par produit/rayon** | Décisions d'achat à l'aveugle, prix mal positionnés |
| P7 | **Coupures internet/électricité fréquentes** | Une caisse 100 % cloud devient inutilisable |

### 2.2 Pourquoi les solutions existantes échouent

- **ERP internationaux (Odoo, Sage, Microsoft Dynamics)** : puissants mais chers (licences + intégrateur), mal localisés (TVA, FCFA, Mobile Money), formation lourde.
- **Logiciels de caisse génériques** : pensés pour l'Europe (TVA multi-taux, centimes, TPE bancaire), pas pour le rendu de monnaie en FCFA ni le crédit client.
- **Excel / cahier** : gratuit mais non fiable, non partagé, non auditable.
- **Applications mobiles isolées** : pas de gestion multi-caisses, pas de fournisseurs, pas de comptabilité.

**Fenêtre d'opportunité** : la pénétration du smartphone et du Mobile Money dépasse 60 % dans les grandes villes UEMOA, tandis que l'offre logicielle locale reste quasi inexistante sous la barre des 50 000 FCFA/mois.

---

## 3. La solution

SuguPro couvre **7 modules** dans une seule application :

| Module | Promesse | Indicateur de succès |
|---|---|---|
| **POS / Caisse** | Encaisser en moins de 20 secondes, ticket imprimé ou WhatsApp | Temps moyen de transaction < 45 s |
| **Stock & Produits** | Savoir ce qu'il reste, où, et à quelle date il périme | Taux de rupture < 3 % |
| **Achats & Fournisseurs** | Commander au bon moment, payer au bon moment | Délai moyen de réappro < 7 j |
| **Clients & Crédit** | Ardoise numérique, relances automatiques | Impayés < 3 % du CA |
| **Notifications** | Reçu WhatsApp, alerte stock, relance SMS | 80 % des tickets dématérialisés |
| **Rôles & Sécurité** | Chaque employé voit ce qu'il doit voir | 0 accès non autorisé |
| **Pilotage** | CA, marge, top produits, Z de caisse | 1 rapport quotidien automatique |

### Différenciateurs

1. **Conçu FCFA-first** : montants entiers, rendu de monnaie assisté, coupures 500/1 000/2 000/5 000/10 000.
2. **Tolérance réseau** : caisse utilisable en mode dégradé, synchronisation différée (file d'attente locale), impression indépendante du cloud.
3. **WhatsApp natif** : le reçu et les relances passent par le canal réellement utilisé par le client final.
4. **Prix adapté au marché informel** : à partir de 25 000 FCFA/mois, sans engagement long.
5. **Déploiement en 48 h** : import du catalogue Excel, paramétrage des rayons, formation d'une demi-journée.

---

## 4. Étude de marché UEMOA

### 4.1 Cadrage macro

| Pays | Population | PIB/hab. (ordre de grandeur) | Pénétration smartphone urbaine | Priorité |
|---|---|---|---|---|
| Mali | ~23 M | ~900 USD | ~55 % | **Pilote** |
| Sénégal | ~18 M | ~1 700 USD | ~70 % | Phase 2 |
| Côte d'Ivoire | ~29 M | ~2 700 USD | ~65 % | Phase 2 |
| Burkina Faso | ~23 M | ~900 USD | ~50 % | Phase 3 |
| Bénin / Togo | ~13 M / ~9 M | ~1 400 USD | ~55 % | Phase 3 |

### 4.2 Univers adressable (hypothèses explicites)

| Niveau | Définition | Nombre d'établissements | ARPU annuel | Valeur |
|---|---|---|---|---|
| **TAM** | Supérettes, supermarchés, mini-marchés et boutiques multi-rayons formalisés dans les 5 pays | 18 000 | 480 000 FCFA | **8,64 Md FCFA/an** |
| **SAM** | Établissements équipés d'au moins un PC/tablette et d'une connexion, prêts à payer un logiciel | 3 600 (20 %) | 480 000 FCFA | **1,73 Md FCFA/an** |
| **SOM 3 ans** | Part de marché visée (17 % du SAM) | 600 | 480 000 FCFA | **288 M FCFA/an (ARR)** |

> Hypothèse ARPU blendé : 40 000 FCFA/mois (mix Starter 60 %, Pro 30 %, Enterprise 10 %).

### 4.3 Segments clients

| Segment | CA mensuel | Besoin dominant | Pack |
|---|---|---|---|
| **Boutique de quartier / mini-marché** | 3 – 10 M FCFA | Caisse simple, stock, ticket | Starter |
| **Supérette** | 10 – 40 M FCFA | Multi-caisses, crédit client, fournisseurs | Pro |
| **Supermarché / chaîne (2-10 points de vente)** | 40 M+ FCFA | Consolidation, rôles, API, reporting | Enterprise |

### 4.4 Saisonnalité à intégrer au produit

- **Ramadan / Tabaski** : pics de 3 à 5× sur les céréales, huiles, boissons, viande.
- **Rentrée scolaire (septembre-octobre)** : fournitures, cartables, lait en poudre.
- **Fêtes de fin d'année** : boissons, conserves, produits importés.
- **Saison des pluies** : baisse de fréquentation, hausse des produits de conservation.

Le produit doit donc gérer des **campagnes promotionnelles datées** et des **objectifs de stock saisonniers**.

---

## 5. Concurrence

| Concurrent | Type | Prix indicatif | Forces | Faiblesses exploitables |
|---|---|---|---|---|
| Odoo POS | ERP open source | 0 – 500 000 FCFA/an + intégration | Complet, communauté | Complexe, pas de FCFA/Mobile Money natif, intégrateur requis |
| Sage 100 / Sage Gestion | ERP comptable | 1 – 3 M FCFA | Comptabilité solide | Non pensé POS, lourd, cher |
| Logiciels de caisse locaux | Desktop | 300 000 – 800 000 FCFA (licence) | Connus des comptables | Pas de cloud, pas de WhatsApp, sauvegardes manuelles |
| Excel / cahier | Manuel | 0 | Gratuit | Erreurs, pas de temps réel |
| **SuguPro** | **SaaS web** | **25 000 – 100 000 FCFA/mois** | FCFA-first, WhatsApp, mobile, prix | À construire : notoriété |

**Positionnement** : « le logiciel de caisse que le gérant comprend en une heure, et que le caissier utilise sans formation ».

---

## 6. Modèle économique

### 6.1 Grille tarifaire

| Offre | Prix mensuel | Prix annuel (−17 %) | Inclus |
|---|---|---|---|
| **Starter** | 25 000 FCFA | 250 000 FCFA | 1 caisse, 1 000 produits, 2 utilisateurs, stock, reçus WhatsApp (500/mois) |
| **Pro** | 50 000 FCFA | 500 000 FCFA | 3 caisses, produits illimités, 10 utilisateurs, crédit client, fournisseurs, relances SMS (1 000/mois) |
| **Enterprise** | 100 000 FCFA | 1 000 000 FCFA | Caisses illimitées, multi-magasins, API, rapports consolidés, support prioritaire |
| **Installation** | 150 000 FCFA (unique) | — | Import catalogue, paramétrage, formation ½ journée, 1 mois de support |
| **Matériel** | à la vente | — | Douchette USB 35 000, imprimante 58 mm 85 000, tiroir-caisse 60 000, tablette 180 000 |

### 6.2 Revenus annexes

- **Crédits WhatsApp/SMS** vendus par packs (5 000 FCFA / 1 000 messages).
- **Personnalisation** (marque, tickets, rapports) : 100 000 – 300 000 FCFA.
- **Formation avancée** : 50 000 FCFA/session.
- **Hébergement dédié / sauvegarde externalisée** : 15 000 FCFA/mois.

### 6.3 Structure de coûts (annuelle, ordre de grandeur)

| Poste | Détail | Coût annuel |
|---|---|---|
| Infrastructure | VPS 2 (app + DB), domaine, SSL, sauvegardes, CDN | 1 800 000 FCFA |
| WhatsApp Cloud API | Conversations utilitaires (tarif Meta) | 1 200 000 FCFA (Y1) → 4 800 000 (Y3) |
| SMS | Agrégateur local, ~25 FCFA/SMS | 900 000 FCFA |
| Équipe technique | 2 développeurs | 12 000 000 FCFA |
| Commercial / support | 1 commercial terrain + 1 support | 7 200 000 FCFA |
| Marketing | Réseaux, radio, foires, WhatsApp Business | 6 000 000 FCFA |
| Administration / juridique | Comptable, OHADA, assurances | 2 400 000 FCFA |
| Matériel de démonstration | 10 kits de caisse | 2 000 000 FCFA |

---

## 7. Prévisionnel financier 3 ans (en FCFA)

### 7.1 Hypothèses

- Démarrage commercial : janvier 2027 (M1).
- ARPU blendé : **40 000 FCFA/mois**.
- Churn mensuel : 3 % en année 1, 2 % en année 2, 1,5 % en année 3.
- Conversion prospects → clients : 25 % (Y1), 30 % (Y2), 35 % (Y3).
- Revenu d'installation : 150 000 FCFA par nouveau client (60 % des clients le prennent).

### 7.2 Compte de résultat simplifié

| Rubrique | Année 1 (2027) | Année 2 (2028) | Année 3 (2029) |
|---|---|---|---|
| Clients actifs (fin d'année) | 80 | 250 | 600 |
| ARR (fin d'année) | 38 400 000 | 120 000 000 | 288 000 000 |
| Abonnements encaissés | 19 200 000 | 76 800 000 | 201 600 000 |
| Installations & matériel | 8 400 000 | 15 000 000 | 24 000 000 |
| **Chiffre d'affaires total** | **27 600 000** | **91 800 000** | **225 600 000** |
| Coûts variables (API, SMS, hébergement) | 3 900 000 | 9 800 000 | 22 000 000 |
| Charges de personnel | 19 200 000 | 30 000 000 | 54 000 000 |
| Marketing & commercial | 6 000 000 | 10 000 000 | 18 000 000 |
| Administration & divers | 4 400 000 | 6 200 000 | 10 000 000 |
| **Total charges** | **33 500 000** | **56 000 000** | **104 000 000** |
| **EBITDA** | **−5 900 000** | **35 800 000** | **121 600 000** |
| Marge EBITDA | −21 % | 39 % | 54 % |

### 7.3 Trésorerie et seuil de rentabilité

| Indicateur | Valeur |
|---|---|
| Investissement initial | 18 000 000 FCFA |
| Trésorerie la plus basse | −9 500 000 FCFA (M10) |
| **Point mort mensuel** | **≈ 2 350 000 FCFA de CA récurrent**, atteint au **M15** |
| Nombre de clients au point mort | ≈ 59 abonnés Pro/Starter (mix) |
| Retour sur investissement | **M27** |

### 7.4 Utilisation des fonds (18 M FCFA)

| Poste | Montant | % |
|---|---|---|
| Développement produit (6 mois) | 9 000 000 | 50 % |
| Go-to-market (commercial, marketing) | 4 000 000 | 22 % |
| Infrastructure & APIs (12 mois) | 2 000 000 | 11 % |
| Matériel de démonstration | 1 500 000 | 8 % |
| Juridique, comptabilité, imprévus | 1 500 000 | 9 % |

---

## 8. Stratégie de mise sur le marché

### 8.1 Canaux

| Canal | Action | Coût / mois | Objectif |
|---|---|---|---|
| **Terrain Bamako** | 1 commercial, 15 visites/jour, démo sur tablette | 600 000 | 25 clients/mois (Y2) |
| **WhatsApp Business** | Catalogue, témoignages, groupe de gérants | 50 000 | 40 % des leads |
| **Partenariats grossistes** | Recommandation aux clients épiceries | 0 (commission) | 20 % des leads |
| **Comptables & cabinets** | Prescription, apport d'affaires 10 % | 0 (commission) | 15 % des leads |
| **Radio & affichage** | Spot matinal bambara/français | 300 000 | Notoriété |
| **Foires & salons** | SEMA, Foire de Bamako | 500 000 (ponctuel) | Crédibilité |

### 8.2 Séquence de lancement

1. **M1–M3** : 10 magasins pilotes à Bamako (gratuit 2 mois contre témoignage vidéo).
2. **M4–M6** : lancement commercial payant, prix Starter à 20 000 FCFA (offre de lancement).
3. **M7–M12** : ouverture Dakar (partenaire local), 80 clients.
4. **M13–M24** : Abidjan, offre Enterprise multi-magasins.
5. **M25–M36** : Burkina, Bénin, Togo via revendeurs.

### 8.3 Rétention

- Onboarding assisté obligatoire (import catalogue + formation).
- Rapport WhatsApp quotidien au gérant (habitude d'usage).
- Support téléphone/WhatsApp en bambara, français, wolof.
- Contrat annuel avec 2 mois offerts → sécurise la trésorerie.

---

## 9. Risques et plan de mitigation

| Risque | Probabilité | Impact | Mitigation |
|---|---|---|---|
| Coupures internet | Élevée | Élevé | Mode caisse dégradé + file locale, synchronisation différée, impression locale |
| Coupures électriques | Élevée | Moyen | Onduleur recommandé, autosave, reprise de session |
| Faible culture informatique | Élevée | Moyen | UI tactile 4 boutons, formation ½ journée, vidéos en bambara |
| Fraude caissier | Moyenne | Élevé | Journal d'audit, Z de caisse, contrôle des remises, alertes annulations |
| Coût WhatsApp | Moyenne | Moyen | Fallback SMS, templates utilitaires, cache des reçus |
| Concurrence d'un acteur local | Moyenne | Moyen | Verrouillage par la donnée (catalogue, historique) + prix |
| Impayés d'abonnement | Moyenne | Moyen | Prépaid 3 mois, suspension douce, relance automatique |
| Dépendance à un développeur | Moyenne | Élevé | Code documenté, tests CI, dépôt GitHub, revue de code |

---

## 10. Indicateurs de pilotage (KPIs)

| KPI | Cible Y1 | Cible Y3 |
|---|---|---|
| MRR | 2 800 000 FCFA | 24 000 000 FCFA |
| Churn mensuel | < 3 % | < 1,5 % |
| CAC | < 75 000 FCFA | < 50 000 FCFA |
| LTV | > 600 000 FCFA | > 1 200 000 FCFA |
| Ratio LTV/CAC | > 8 | > 20 |
| NPS | > 30 | > 50 |
| Taux d'activation (1ʳᵉ vente en 7 j) | 70 % | 85 % |
| Tickets dématérialisés | 40 % | 80 % |

---

## 11. Feuille de route produit alignée sur le business

| Trimestre | Livrable | Objectif business |
|---|---|---|
| T1 2027 | POS, stock, produits, tickets, rôles | 10 pilotes |
| T2 2027 | Achats, fournisseurs, crédit client, WhatsApp | Monétisation Starter/Pro |
| T3 2027 | Multi-caisses, Z de caisse, rapports, PWA | Réduction du churn |
| T4 2027 | API REST, multi-magasins, SMS | Offre Enterprise |
| 2028 | Comptabilité simplifiée, Mobile Money API, mode hors-ligne avancé | Expansion Dakar/Abidjan |
| 2029 | Place de marché fournisseurs, scoring de crédit, IA de réassort | Nouveaux revenus |

---

## 12. Conclusion

Le marché existe, la douleur est mesurable et monétisable, et aucune solution locale ne combine **POS tactile**, **stock**, **crédit client** et **WhatsApp** à un prix accessible en FCFA. SuguPro vise 600 magasins et 288 M FCFA d'ARR en 3 ans, avec un point mort au 15ᵉ mois et une marge EBITDA supérieure à 50 % en régime de croisière.
