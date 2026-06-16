---
name: FamilyTribute
description: A living family album — warm, celebratory, story-driven.
colors:
  hearthlight: "#bf8028"
  hearthlight-deep: "#8b5c18"
  album-rose: "#c47070"
  inkwell: "#1c1410"
  faded-ink: "#584038"
  old-binding: "#8c7468"
  aged-edge: "#d4c2b4"
  divider: "#ece8e4"
  surface: "#ffffff"
typography:
  display:
    fontFamily: "Gwendolyn, cursive"
    fontSize: "clamp(3rem, 8vw, 6rem)"
    fontWeight: 700
    lineHeight: 1.05
    letterSpacing: "-0.01em"
  headline:
    fontFamily: "Gwendolyn, cursive"
    fontSize: "clamp(2rem, 5vw, 4rem)"
    fontWeight: 700
    lineHeight: 1.1
    letterSpacing: "normal"
  title:
    fontFamily: "Open Sans, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 700
    lineHeight: 1.3
    letterSpacing: "normal"
  body:
    fontFamily: "Open Sans, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.65
    letterSpacing: "normal"
  label:
    fontFamily: "Open Sans, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "normal"
rounded:
  sm: "4px"
  md: "8px"
  lg: "12px"
  pill: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "40px"
  2xl: "64px"
components:
  button-primary:
    backgroundColor: "{colors.hearthlight}"
    textColor: "{colors.surface}"
    rounded: "{rounded.md}"
    padding: "16px 40px"
  button-primary-hover:
    backgroundColor: "{colors.hearthlight-deep}"
    textColor: "{colors.surface}"
  button-ghost:
    backgroundColor: "transparent"
    textColor: "{colors.hearthlight}"
    rounded: "{rounded.md}"
    padding: "12px 32px"
  card:
    backgroundColor: "{colors.surface}"
    rounded: "{rounded.lg}"
    padding: "{spacing.md}"
  nav:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.inkwell}"
---

# Design System: FamilyTribute

## 1. Overview

**Creative North Star: "The Living Family Album"**

FamilyTribute is a family tribute archive — a place to celebrate lives, share photographs, and pass stories from one generation to the next. The visual system should feel like being handed a well-loved photo album by someone who loved the people in it: warm, intimate, and alive with personality. Not a product. Not a service. A gift.

The palette is built around hearthlight amber — the color of warm lamplight, old gold frames, and the hour just before sunset. Gwendolyn, a flowing handwritten script, carries the display hierarchy with personal warmth. Open Sans provides clear, readable body prose. Together they evoke the handmade and the human without feeling crafted or kitschy.

**This system is explicitly not:**
- A clinical obituary service (Legacy.com: sterile, transactional, ad-cluttered)
- A grief-heavy memorial (black or dark palettes, heavy silence, designed to mourn)
- A SaaS product (indigo gradients, uppercase-tracked CTA buttons, identical card grids, eyebrow labels on every section)

**Key Characteristics:**
- Warm amber primary on white surfaces — warmth lives in the accent, not the background
- Script display type (Gwendolyn) paired with humanist sans body (Open Sans)
- Amber-tinted shadows instead of generic gray or indigo
- Photography and content are always the hero — UI elements recede and frame
- Celebratory in tone: every design choice should feel like a story worth telling

## 2. Colors: The Hearthlight Palette

Warmth is carried by the amber accent, not the background. Surfaces stay white; the primary draws from old gold and hearthlight.

### Primary
- **Hearthlight** (`#bf8028`): The main brand amber. Used for primary buttons, active nav states, links, interactive focus rings, and any intentional brand moment. Named for the color of lamplight on a family dinner table.
- **Ember Deep** (`#8b5c18`): Darker amber for hover states, pressed buttons, and depth within the primary role. Never use on body text.

### Secondary
- **Album Rose** (`#c47070`): A dusty, muted rose. Used sparingly — for private/personal content flags, soft emotional moments, and complementary warmth when hearthlight alone isn't enough. One instance per screen maximum.

### Neutral
- **Inkwell** (`#1c1410`): Primary text. A very dark warm brown-black — not pure black, not cool. Used for all body text, headings in non-display contexts, and high-contrast UI text.
- **Faded Ink** (`#584038`): Secondary text. Dates, labels, supporting copy. The color of a felt-tip pen that's been used a hundred times.
- **Old Binding** (`#8c7468`): Muted text. Placeholder copy, supplementary metadata. Never for body content.
- **Aged Edge** (`#d4c2b4`): Borders and dividers. Warm, not cold. Used for card outlines, separators, and input strokes.
- **Divider** (`#ece8e4`): The lightest neutral. Section separators and subtle backgrounds within cards.
- **Surface** (`#ffffff`): Content surfaces — card backgrounds, modals, nav bar. True white; no warm tint. Warmth lives in the accent and shadows, not the background.

### Named Rules

**The Hearthlight Rule.** Amber is used in ≤20% of any given screen's surface area. Buttons, active states, links — not backgrounds. Its warmth is effective precisely because it's not everywhere.

**The No-Cold-Gray Rule.** The indigo/violet palette the codebase was built on has been retired. No `indigo-*`, `violet-*`, or `sky-*` Tailwind classes on UI surfaces. If something cool-gray slips in, it's a regression.

**The Warm Shadow Rule.** Shadows must always use amber or warm-brown tinting (`rgba(140, 80, 30, 0.12)` or similar), never cool gray (`rgba(0,0,0,0.1)` is permitted only for photo overlays). A cool shadow on a warm surface breaks the album aesthetic immediately.

## 3. Typography: Gwendolyn + Open Sans

**Display Font:** Gwendolyn (Google Fonts, weight 700 only — the bold is the only weight available)
**Body Font:** Open Sans (Google Fonts: 400, 400 italic, 600, 700)

**Character:** The pairing is deliberate contrast — script warmth at scale, humanist clarity at reading size. Gwendolyn at large sizes feels personal and handwritten; Open Sans at body size is invisible in the best way, letting content breathe. Don't audition any other header font; Gwendolyn's handmade quality is the identity.

### Hierarchy
- **Display** (Gwendolyn 700, clamp(3rem → 6rem), line-height 1.05): Home page hero, primary page titles. Letter-spacing −0.01em. Use `text-wrap: balance`.
- **Headline** (Gwendolyn 700, clamp(2rem → 4rem), line-height 1.1): Section headings within pages — person names in detail views, story titles, "Pictures of [First Name]". Use `text-wrap: balance`.
- **Title** (Open Sans 700, 1.5rem, line-height 1.3): Sub-section labels, card headers, modal titles. Not script — this is where Open Sans first appears in the hierarchy.
- **Body** (Open Sans 400, 1rem, line-height 1.65): All prose content — obituary text, story copy, descriptions. Minimum 16px. Max line length 65–75ch; use `max-width: 70ch` on prose containers.
- **Label** (Open Sans 600, 0.875rem, line-height 1.4): Dates, metadata, navigation links, button text. No uppercase tracking — that's the SaaS tell.

### Named Rules

**The No-Uppercase-Tracking Rule.** The current codebase uses `uppercase tracking-widest` on primary buttons. That pattern is retired. It reads as a tech product, not a family tribute. Button labels use sentence case or title case, Open Sans 600, no letter-spacing beyond `normal`.

**The Gwendolyn-Only-at-Scale Rule.** Gwendolyn is a script font — it is illegible below 24px and inappropriate in body context. Never use `font-header` on text smaller than 1.5rem.

## 4. Elevation

FamilyTribute uses ambient shadow elevation: surfaces float gently above the page without hard edges or structured layers. The aesthetic is lifted and warm — like photographs pinned to a wall with soft shadow beneath them.

Shadows are warm-amber tinted, not cool-gray. The card is the primary elevated surface; modals and nav carry the secondary elevation. No surface is flat — flatness reads as product-y.

### Shadow Vocabulary
- **Card ambient** (`box-shadow: 0 4px 24px rgba(140, 80, 30, 0.10)`): Default state for all content cards. Warm amber ambient glow.
- **Card hover** (`box-shadow: 0 8px 40px rgba(140, 80, 30, 0.18)`): Lifted hover state on interactive cards (people grid, pictures grid). The lift is the affordance.
- **Surface overlay** (`box-shadow: 0 2px 12px rgba(140, 80, 30, 0.08)`): Nav bar bottom shadow, subtle surface separation without strong contrast.
- **Modal** (`box-shadow: 0 20px 60px rgba(28, 20, 16, 0.22)`): Darker, deeper for modals and dialogs. Uses Inkwell-tinted shadow for heavier presence.

### Named Rules

**The Warm Float Rule.** Every elevated surface uses amber-tinted shadows. If the shadow reads as gray, it's wrong. The rule catches it immediately: does the page feel like it's lit with warm lamplight, or fluorescent office light? Choose lamplight.

## 5. Components

### Buttons
Warm and inviting — rounded, generous padding, unhurried.

- **Shape:** Gently curved (8px radius). Not pill, not square.
- **Primary (Hearthlight):** Amber (`#bf8028`) background, white text, Open Sans 600, sentence case, `padding: 14px 36px`. No uppercase, no tracking.
- **Hover:** `#8b5c18` (Ember Deep), `box-shadow: 0 4px 20px rgba(140, 80, 30, 0.30)`. Subtle upward lift of 1–2px via `transform: translateY(-1px)`.
- **Danger:** Deep red (`#b91c1c`), white text. Reserved strictly for delete/destroy actions. Same shape as primary.
- **Ghost:** Transparent background, `1px solid #bf8028` (Hearthlight) border, Hearthlight text. Used for secondary actions.
- **Focus ring:** `outline: 3px solid #bf8028`, `outline-offset: 2px`.

### Cards / Containers
Photograph frames, not product tiles. Cards present people and memories, not features.

- **Corner Style:** Gently rounded (12px / rounded-lg). Large enough to feel warm, not so large it reads as bubble.
- **Background:** White (`#ffffff`) with warm ambient shadow (Card ambient value above).
- **Card footer gradient:** The gradient card footer (`from-indigo-200 via-violet-200`) is retired. Replace with a warm amber-to-rose gradient: `from-amber-50 to-rose-50` in Tailwind, or `linear-gradient(to bottom-left, #fef3c7, #fce7f3)` literally.
- **Border:** None by default; shadow provides separation. An `Aged Edge` (`#d4c2b4`) `1px solid` border is used only when a card sits on a white background with no shadow (e.g., within another card — though nested cards are prohibited).
- **Internal Padding:** `16px` (spacing.md) at minimum; `24px` (spacing.lg) for content-heavy cards.
- **Hover on interactive cards:** `transform: translateY(-2px)`, shadow upgrades to Card hover. Transition: `250ms ease-out`.

### Inputs / Fields
- **Style:** White background, `1px solid #d4c2b4` (Aged Edge) border, `6px` radius.
- **Focus:** Border shifts to `#bf8028` (Hearthlight), `box-shadow: 0 0 0 3px rgba(191, 128, 40, 0.15)`. No harsh outline.
- **Error:** `1px solid #b91c1c` border, warm rose-tinted error shadow. Error message in `#b91c1c`, Open Sans 400.
- **Placeholder:** `#8c7468` (Old Binding). Must pass 4.5:1 contrast against white — verify and darken if needed.

### Navigation
- **Background:** White, `1px solid #ece8e4` (Divider) bottom border. No heavy shadow.
- **Logo type:** Gwendolyn 700 at nav height (~2.5–3rem) in Hearthlight (`#bf8028`). Brand name in amber is the identity moment.
- **Nav links:** Open Sans 600, Inkwell (`#1c1410`), `0.875rem`. Hover: Hearthlight. Active: Hearthlight with amber underline. No uppercase.
- **Mobile hamburger:** Opens as a slide-down panel. Warm white background.

### Photo Grid (Signature Component)
The home page photo collage is the most important surface — it's the first thing a visitor sees.

- **Layout:** Asymmetric CSS grid with 4–5 photos in an editorial mosaic. Not a uniform grid.
- **Photo overlays:** `bg-black/30 backdrop-blur-sm` caption bars are retained but shifted to amber-tinted: `bg-amber-900/20 backdrop-blur-sm`. White caption text.
- **Hover:** A subtle warm amber glow `box-shadow: inset 0 0 0 3px rgba(191, 128, 40, 0.6)` on photo hover — like a photo being picked up.

## 6. Do's and Don'ts

### Do:
- **Do** use Gwendolyn at display scale (≥1.5rem) for names, titles, and section heads. It is the identity.
- **Do** let photography do the emotional work. Design frames content; it doesn't compete.
- **Do** tint every shadow with amber warmth. `rgba(140, 80, 30, 0.10)` is the shadow base.
- **Do** use sentence case on button labels. The copy should feel like a person wrote it.
- **Do** ensure body text passes 4.5:1 contrast. The audience includes older family members.
- **Do** use white (`#ffffff`) surfaces. Warmth comes from the amber accent and amber-tinted shadows, not from a tinted background.
- **Do** keep interactive card hover states tactile: a slight lift (`translateY(-2px)`) plus shadow deepening signals interactivity without shouting.

### Don't:
- **Don't** use the indigo or violet palette. The current codebase (`indigo-*`, `violet-*`, `sky-*` classes) is being replaced with the Hearthlight amber system. Any remaining indigo is a regression, not a feature.
- **Don't** make it feel like a clinical obituary site (Legacy.com). No transactional layouts, no ad-heavy density, no form-over-feeling.
- **Don't** design for grief. Dark backgrounds, heavy silence, desaturated imagery treatments — FamilyTribute celebrates lives. It doesn't mourn.
- **Don't** use generic SaaS patterns: no uppercase tracked buttons, no eyebrow labels on every section, no identical card grids with icon + heading + text repeated endlessly, no gradient text.
- **Don't** use Gwendolyn below 1.5rem. It's illegible as a small font and destroys the identity when misused.
- **Don't** use nested cards. A card inside a card is always wrong. Use a tonal background or indented section instead.
- **Don't** use cool-gray shadows. `rgba(0,0,0,0.1)` shadow on an amber-warm surface breaks the album feel immediately.
- **Don't** add eyebrow labels (small all-caps "PEOPLE", "STORIES", "PICTURES" above every section heading). One deliberate kicker is voice; one per section is AI grammar.
- **Don't** use side-stripe borders (`border-left: 4px solid`) as card or callout accents. Use a background tint or leading icon instead.
