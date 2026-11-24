# Identité Visuelle & Charte Graphique — SuguPro

> Version 1.0 — Novembre 2026
> Objectif : une interface lisible sur écran tactile, rapide à comprendre, crédible pour un gérant et confortable pour un caissier debout toute la journée.

---

## 1. La marque

### 1.1 Nom

**SuguPro** — contraction de *sugu* (« marché », en bambara) et de *Pro* (professionnalisme, logiciel métier).

Le nom est court, prononçable en français comme en bambara, et disponible en `.com`/`.app` (à vérifier au dépôt).

### 1.2 Slogan

> **« Votre supermarché, piloté au franc près. »**

Variantes courtes (tickets, réseaux) :
- « La caisse qui parle FCFA. »
- « Stock, caisse, crédit : tout est clair. »

### 1.3 Positionnement visuel

| Attribut | Traduction graphique |
|---|---|
| **Fiable** | Vert profond, formes stables, pas d'effets gratuits |
| **Rapide** | Grands boutons, contrastes nets, animations < 200 ms |
| **Africain & moderne** | Motif géométrique inspiré du bogolan, sans folklore |
| **Accessible** | Typographie large, icônes doublées de libellés |
| **Commercial** | Chiffres mis en avant, badges de statut colorés |

### 1.4 Concept de logo

**Symbole** : un panier stylisé dont les anses forment un « S » continu, dessiné en trait épais (8 px) avec un coin arrondi, posé sur un carré vert émeraude. Une petite étincelle orange en haut à droite évoque la vente conclue.

**Lockup** : symbole à gauche, mot-symbole « Sugu**Pro** » à droite (`Sugu` en 700, `Pro` en 800 couleur orange).

**Zone de protection** : hauteur du « S » du symbole sur tous les côtés.
**Taille minimale** : 24 px (symbole seul), 96 px (lockup horizontal).
**Fond** : version principale sur blanc ; version inverse sur bleu nuit ; jamais sur photo chargée.

**Fichiers attendus** : `logo.svg`, `logo-white.svg`, `logo-mark.svg`, `favicon.ico`, `og-image.png` (1200×630).

---

## 2. Palette de couleurs

### 2.1 Couleurs de marque

| Rôle | Nom | Hex | Usage |
|---|---|---|---|
| **Primaire** | Émeraude | `#0D9488` | Boutons principaux, liens, navigation active, en-têtes |
| Primaire foncé | Émeraude 700 | `#0F766E` | Survol, pression |
| Primaire clair | Émeraude 50 | `#F0FDFA` | Fonds de cartes sélectionnées, badges doux |
| **Secondaire** | Bleu nuit | `#0F172A` | Texte principal, barre latérale, pied de ticket |
| **Accent** | Ambre | `#F59E0B` | CTA secondaires, promotions, alertes douces |
| Accent foncé | Ambre 600 | `#D97706` | Survol de l'accent |

### 2.2 Couleurs de statut

| Statut | Hex | Fond associé | Usage |
|---|---|---|---|
| Succès | `#16A34A` | `#F0FDF4` | Vente validée, stock OK, paiement reçu |
| Alerte | `#DC2626` | `#FEF2F2` | Rupture, impayé, annulation, erreur |
| Attention | `#F59E0B` | `#FFFBEB` | Stock bas, DLC proche, remise élevée |
| Information | `#0284C7` | `#F0F9FF` | Notes, aides, synchronisation |
| Neutre | `#64748B` | `#F1F5F9` | Textes secondaires, désactivé |

### 2.3 Neutres et surfaces

| Token | Hex | Usage |
|---|---|---|
| `--sp-bg` | `#F8FAFC` | Fond général de l'application |
| `--sp-surface` | `#FFFFFF` | Cartes, modales, tableaux |
| `--sp-border` | `#E2E8F0` | Bordures 1 px |
| `--sp-text` | `#0F172A` | Texte principal |
| `--sp-text-muted` | `#64748B` | Texte secondaire, légendes |
| `--sp-text-inverse` | `#F8FAFC` | Texte sur fond sombre |

### 2.4 Couleurs des modes de paiement

| Mode | Couleur | Justification |
|---|---|---|
| Espèces | `#16A34A` | Référence locale, liquidité |
| Orange Money | `#FF7900` | Couleur de marque opérateur |
| Wave | `#1DC3F3` | Couleur de marque opérateur |
| Moov Money | `#0057B8` | Couleur de marque opérateur |
| Carte bancaire | `#0F172A` | Neutre premium |
| Crédit client | `#7C3AED` | Distinct, à surveiller |

> Les couleurs opérateurs ne servent qu'à identifier le bouton de paiement ; elles ne remplacent jamais la couleur primaire de l'interface.

### 2.5 Règles d'usage

- Maximum **3 couleurs de marque** visibles simultanément par écran.
- L'ambre ne sert **jamais** à un bouton de validation finale (risque de confusion avec une alerte).
- Le rouge est réservé aux destructions/erreurs, pas aux promotions.
- Contraste minimum **4,5:1** pour le texte, **3:1** pour les composants.

---

## 3. Typographie

### 3.1 Familles

| Usage | Police | Graisses | Fallback |
|---|---|---|---|
| Titres, interface | **Plus Jakarta Sans** | 500, 600, 700, 800 | `system-ui, sans-serif` |
| Chiffres, tableaux, montants | **Inter** | 400, 500, 600, 700 | `Segoe UI, Roboto, sans-serif` |
| Code, références | `ui-monospace` | 400 | `Consolas, monospace` |

Chargement via `@fontsource` (npm) ou Google Fonts avec `display=swap` et préconnexion.

### 3.2 Échelle typographique (mobile → desktop)

| Token | Mobile | Desktop | Usage |
|---|---|---|---|
| `display` | 32 / 1.15 | 40 / 1.1 | Titre de page marketing |
| `h1` | 24 / 1.25 | 30 / 1.2 | Titre d'écran |
| `h2` | 20 / 1.3 | 24 / 1.25 | Section |
| `h3` | 18 / 1.35 | 20 / 1.3 | Carte, modale |
| `body` | 15 / 1.5 | 16 / 1.55 | Texte courant |
| `small` | 13 / 1.45 | 13 / 1.45 | Légendes, aides |
| `caption` | 11 / 1.4 | 12 / 1.4 | Badges, métadonnées |
| `amount-lg` | 28 / 1.1 | 36 / 1.05 | Total panier, CA |
| `amount` | 18 / 1.2 | 20 / 1.2 | Prix produit, lignes |

### 3.3 Règles de composition

- **Montants FCFA** : `Inter`, graisse 600, `font-variant-numeric: tabular-nums`, sans décimales : `15 500 FCFA` (espace insécable avant `FCFA`).
- Titres : `letter-spacing: -0.01em`, jamais en majuscules intégrales au-delà de 3 mots.
- Corps de texte : largeur max **72 caractères**.
- Ne jamais justifier le texte (problèmes de lisibilité sur petits écrans).
- Les libellés de boutons sont à l'infinitif : « Encaisser », « Ajouter », « Clôturer ».

---

## 4. Espacements, rayons et ombres

### 4.1 Échelle d'espacement (base 4 px)

| Token | Valeur | Usage |
|---|---|---|
| `space-1` | 4 px | Icône ↔ texte |
| `space-2` | 8 px | Padding interne compact |
| `space-3` | 12 px | Gap de grille mobile |
| `space-4` | 16 px | Padding de carte |
| `space-5` | 20 px | Section compacte |
| `space-6` | 24 px | Section standard |
| `space-8` | 32 px | Séparation de blocs |
| `space-12` | 48 px | Marge de page desktop |

### 4.2 Rayons

| Token | Valeur | Usage |
|---|---|---|
| `radius-sm` | 8 px | Badges, inputs |
| `radius-md` | 12 px | Boutons, cartes produits |
| `radius-lg` | 16 px | Modales, panneaux |
| `radius-full` | 9999 px | Pastilles, avatars |

### 4.3 Ombres

| Token | Valeur | Usage |
|---|---|---|
| `shadow-sm` | `0 1px 2px rgba(15,23,42,.06)` | Cartes au repos |
| `shadow-md` | `0 4px 12px rgba(15,23,42,.08)` | Cartes survolées, dropdowns |
| `shadow-lg` | `0 12px 32px rgba(15,23,42,.14)` | Modales, panier mobile |

Pas d'ombre sur les éléments plats (tableaux, onglets).

---

## 5. Composants UI (Bootstrap 5)

### 5.1 Boutons

| Variante | Fond | Texte | Bordure | Usage |
|---|---|---|---|---|
| Primaire | `#0D9488` | blanc | aucune | Action principale unique par écran |
| Secondaire | blanc | `#0F172A` | `#E2E8F0` | Actions neutres |
| Accent | `#F59E0B` | `#0F172A` | aucune | Promotion, action commerciale |
| Danger | `#DC2626` | blanc | aucune | Suppression, annulation |
| Ghost | transparent | `#0D9488` | aucune | Actions tertiaires |

- Hauteur : **48 px** (mobile/tactile), **40 px** (desktop dense).
- Padding horizontal : 20 px. Rayon : 12 px.
- État pressé : `transform: scale(.98)` + fond foncé, 120 ms.
- Focus visible : anneau `0 0 0 4px rgba(13,148,136,.25)`.
- Bouton icône seul : 44 × 44 px minimum, avec `aria-label`.

### 5.2 Cartes produits (POS)

```
┌──────────────────────────┐
│  [image 1:1, coins 8px]  │
│  Riz parfumé 25 kg       │  body 15/600, 2 lignes max
│  18 500 FCFA             │  amount, émeraude 700
│  [ Stock 42 ]  [Sac]     │  badges
└──────────────────────────┘
```

- Largeur min 150 px, ratio image 1:1, `object-fit: cover`.
- Bordure 1 px `#E2E8F0`, rayon 12 px, ombre `sm`.
- Rupture : image en niveaux de gris + overlay « Rupture ».
- Stock bas : liseré supérieur ambre 4 px.

### 5.3 Badges de statut

| Statut | Style |
|---|---|
| Payé / Validé | fond `#F0FDF4`, texte `#16A34A`, point 6 px |
| En attente | fond `#FFFBEB`, texte `#B45309` |
| Rupture / Annulé | fond `#FEF2F2`, texte `#DC2626` |
| Crédit | fond `#F5F3FF`, texte `#7C3AED` |

### 5.4 Tableaux

- En-tête : fond `#F8FAFC`, texte 12 px majuscules `#64748B`, sticky.
- Ligne : 56 px de haut, séparateur 1 px, survol `#F8FAFC`.
- Montants alignés à droite, tabulaires.
- Sur mobile : transformation en cartes empilées (pas de scroll horizontal).
- Actions : menu `⋯` à droite, jamais plus de 2 icônes visibles.

### 5.5 Formulaires

- Label au-dessus, 14 px, graisse 600.
- Input : 48 px, rayon 8 px, bordure `#E2E8F0`, focus émeraude.
- Erreur : bordure `#DC2626`, message 13 px en dessous avec icône.
- Aide : 13 px `#64748B`, toujours visible (pas d'infobulle seule sur mobile).
- Champs monétaires : suffixe `FCFA` intégré à droite, saisie numérique avec séparateurs automatiques.

### 5.6 Navigation

- **Desktop** : barre latérale 240 px, fond `#0F172A`, item actif fond `#0D9488`, icône + libellé.
- **Mobile (gérant)** : barre inférieure 64 px, 5 entrées max (Accueil, Caisse, Stock, Clients, Plus).
- **Mobile (caissier)** : pas de navigation, POS plein écran avec bouton retour panier.
- Fil d'Ariane sur les écrans de détail.

### 5.7 Notifications (toasts)

- Position : haut-centre sur mobile, haut-droite sur desktop.
- Durée : 4 s (succès), 8 s (erreur), persistante si action requise.
- Couleur de bordure gauche selon statut, icône, message court, bouton « Fermer ».

---

## 6. Écrans de référence

### 6.1 POS desktop (≥ 1024 px)

```
┌──────────┬───────────────────────────────────┬──────────────────────┐
│ Sidebar  │  Recherche / scan   [Catégories]  │  Ticket #A-1042      │
│ 240 px   │  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐  │  Riz 25kg   2  37 000│
│          │  │Produit│ │Produit│ │Produit│    │  Huile 5L   1   6 500│
│          │  └─────┘ └─────┘ └─────┘ └─────┘  │  ───────────────────  │
│          │                                   │  Total      43 500   │
│          │                                   │  [Espèces] [OM] [Wave]│
│          │                                   │  [    ENCAISSER    ] │
└──────────┴───────────────────────────────────┴──────────────────────┘
```

- Colonne panier fixe 360 px, fond blanc, ombre gauche.
- Total en `amount-lg`, émeraude.
- Bouton « Encaisser » pleine largeur, 56 px de haut.

### 6.2 POS mobile (≤ 767 px)

- Onglets « Produits » / « Panier (3) ».
- Grille 2 colonnes de cartes produits.
- Barre panier flottante en bas : total + bouton « Voir le panier ».
- Pavé numérique natif pour les montants.

### 6.3 Tableau de bord gérant

Ordre de lecture :
1. 4 cartes KPI (CA du jour, tickets, panier moyen, marge).
2. Alertes (stock bas, DLC, crédits échus) en liste actionnable.
3. Graphique CA 7 jours (Chart.js, barres émeraude).
4. Top 5 produits et top 5 clients.
5. Dernières ventes.

### 6.4 Ticket de caisse (58 mm)

```
        SUGUPRO
   Supermarché La Paix
   Bamako, Hamdallaye ACI
   Tél : +223 70 00 00 00
   NIF : 123456789 A
--------------------------------
Ticket : A-1042
Date   : 20/11/2026 18:42
Caissier : Awa T.
--------------------------------
Riz parfumé 25kg
  2 x 18 500          37 000
Huile Dinor 5L
  1 x  6 500           6 500
--------------------------------
TOTAL                 43 500
Espèces               50 000
Rendu                  6 500
--------------------------------
Merci de votre visite !
Votre reçu WhatsApp :
wa.me/22370000000
--------------------------------
   [QR code vérification]
```

- Largeur imprimable 48 mm (58 mm papier), police monospace 9-10 pt.
- Pas de couleur, pas de logo complexe (noir & blanc).
- QR code vers le reçu numérique signé.

---

## 7. Iconographie et imagerie

- **Icônes** : Bootstrap Icons (trait 1,75 px), taille 16/20/24 px, toujours accompagnées d'un libellé sur mobile.
- **Images produits** : format carré, fond blanc, ombre douce, poids < 100 Ko (WebP).
- **Illustrations marketing** : aplats émeraude/ambre, formes géométriques simples, personnages africains contemporains, sans stéréotypes.
- **Motif bogolan** : utilisé en filigrane à 4 % d'opacité sur les en-têtes marketing uniquement.

---

## 8. Ton de voix

| Contexte | Ton | Exemple |
|---|---|---|
| Succès | Direct, rassurant | « Vente enregistrée. Ticket envoyé sur WhatsApp. » |
| Erreur | Explicite, sans jargon | « Stock insuffisant : il reste 2 sacs de riz. » |
| Crédit | Respectueux, non menaçant | « Rappel amical : 12 500 FCFA à régler avant le 30/11. » |
| Onboarding | Pédagogique | « Scannez un produit ou tapez son nom pour commencer. » |
| Marketing | Concret, chiffré | « Réduisez vos ruptures de 30 % en 60 jours. » |

**À éviter** : « Oups ! », « Une erreur est survenue », les anglicismes (« dashboard », « checkout ») dans l'interface caissier.

---

## 9. Design tokens (CSS)

```css
:root {
  /* Couleurs */
  --sp-primary: #0D9488;
  --sp-primary-dark: #0F766E;
  --sp-primary-soft: #F0FDFA;
  --sp-secondary: #0F172A;
  --sp-accent: #F59E0B;
  --sp-accent-dark: #D97706;

  --sp-success: #16A34A;
  --sp-success-soft: #F0FDF4;
  --sp-danger: #DC2626;
  --sp-danger-soft: #FEF2F2;
  --sp-warning: #F59E0B;
  --sp-warning-soft: #FFFBEB;
  --sp-info: #0284C7;
  --sp-info-soft: #F0F9FF;

  --sp-bg: #F8FAFC;
  --sp-surface: #FFFFFF;
  --sp-border: #E2E8F0;
  --sp-text: #0F172A;
  --sp-text-muted: #64748B;

  /* Typographie */
  --sp-font-display: "Plus Jakarta Sans", system-ui, sans-serif;
  --sp-font-body: "Inter", "Segoe UI", sans-serif;
  --sp-font-mono: ui-monospace, Consolas, monospace;

  /* Espacements */
  --sp-space-1: 4px;
  --sp-space-2: 8px;
  --sp-space-3: 12px;
  --sp-space-4: 16px;
  --sp-space-6: 24px;
  --sp-space-8: 32px;

  /* Rayons */
  --sp-radius-sm: 8px;
  --sp-radius-md: 12px;
  --sp-radius-lg: 16px;

  /* Ombres */
  --sp-shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
  --sp-shadow-md: 0 4px 12px rgba(15, 23, 42, .08);
  --sp-shadow-lg: 0 12px 32px rgba(15, 23, 42, .14);

  /* Mouvement */
  --sp-transition-fast: 120ms ease-out;
  --sp-transition-base: 180ms ease-out;
}

/* Surcharge Bootstrap 5 */
.btn-primary {
  --bs-btn-bg: var(--sp-primary);
  --bs-btn-border-color: var(--sp-primary);
  --bs-btn-hover-bg: var(--sp-primary-dark);
  --bs-btn-hover-border-color: var(--sp-primary-dark);
  --bs-btn-focus-shadow-rgb: 13, 148, 136;
  border-radius: var(--sp-radius-md);
  min-height: 48px;
  font-weight: 600;
}

.amount {
  font-family: var(--sp-font-body);
  font-variant-numeric: tabular-nums;
  font-weight: 600;
}

@media (prefers-reduced-motion: reduce) {
  * { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
```

---

## 10. Accessibilité et bonnes pratiques

- Cible tactile **≥ 44 × 44 px**, espacement ≥ 8 px entre actions.
- Ne jamais coder l'information par la couleur seule : ajouter icône ou texte.
- `aria-label` sur les boutons icônes ; `role="status"` sur les toasts.
- Contraste vérifié (WebAIM) pour chaque paire texte/fond.
- Mode « gros caractères » (racine 18 px) pour les caissiers seniors.
- Support du zoom 200 % sans perte de fonctionnalité.
- Formulaires utilisables au clavier (tabulation logique, focus visible).
- Langue déclarée : `<html lang="fr">`.

---

## 11. Déclinaisons

| Support | Déclinaison |
|---|---|
| **App** | Sidebar bleu nuit, contenu clair, accent émeraude |
| **Ticket** | Noir & blanc, monospace, QR code |
| **WhatsApp** | Message texte structuré + PDF joint, pas d'emoji excessif |
| **Réseaux sociaux** | Carré émeraude, motif bogolan 4 %, chiffre clé en ambre |
| **Présentation commerciale** | Fond `#F8FAFC`, titres Plus Jakarta Sans, captures d'écran réelles |
| **Tenue terrain** | Polo émeraude, logo brodé blanc, badge nominatif |

---

## 12. Checklist de validation avant livraison

- [ ] Tous les textes en français, sans faute, sans placeholder.
- [ ] Montants affichés avec `format_fcfa()` et chiffres tabulaires.
- [ ] Contrastes AA vérifiés sur les 6 écrans principaux.
- [ ] Cibles tactiles ≥ 44 px sur mobile.
- [ ] Aucune information portée par la couleur seule.
- [ ] Logo et favicon présents dans `public/`.
- [ ] Ticket testé sur imprimante 58 mm réelle.
- [ ] Mode gros caractères testé.
- [ ] `prefers-reduced-motion` respecté.
- [ ] Écrans testés sur 375×812, 768×1024, 1280×800, 1920×1080.
