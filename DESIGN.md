# DESIGN.md — Drift Creative Systems

Source of truth: **Drift Brand System** (`assets/brand/drift-brand-guidelines.pdf`) — *"Modern. Minimal. Purposeful."*
Implementation: CSS custom properties in `assets/site.css` `:root`. Use the tokens; don't hardcode new values.

---

## 1. Principles

1. **Black does the work.** Black, white and grey carry the layout, type and primary buttons.
2. **One accent, used sparingly.** Each product suite has one accent colour. It appears only on product moments: eyebrow rules, status dots, the card "more" rule, spotlight step rings, accent buttons. Never as a large background behind text.
3. **Big, tight display type; calm reading type.** Poppins for display, Inter for reading.
4. **Content first.** No decorative imagery. The logo mark and the Encore flow illustration are the only "art".

---

## 2. Colour

| Token | Value | Use |
|---|---|---|
| `--black` | `#000000` | Text, primary buttons, spotlight / CTA / footer backgrounds |
| `--white` | `#FFFFFF` | Page background, text on black |
| `--grey` | `#7A7F87` | Secondary text, eyebrows, labels (brand core grey) |
| `--line` | `#E3E5E8` | Borders, dividers |
| `--soft` | `#F5F6F7` | Contact background, emoji tiles, pills, "grey background" option |
| `#B6BAC0` | (inline) | Secondary text **on black** (spotlight, CTA, featured card) |
| `#2A2B2E` | (inline) | Dark-button hover; dividers on black |

### Suite accents (`--app`)

| Suite | Key | Accent |
|---|---|---|
| Music Suite | `music` | `#FF4FA3` |
| WP Agency Kit | `agency` | `#1F7BFF` |
| SEO | `seo` | `#57E35B` |

- Defined in PHP in `drift_suites()` (`inc/apps.php`). The CSS tokens `--music`, `--agency` and `--seo` mirror them. **Change both together.**
- `--app` is set inline per element (`style="--app: #…"`) by suites, cards, the spotlight, the CTA and app pages. Components read `var(--app)`.
- An app can override its suite accent (App details → Accent override). Use this rarely.
- Accent buttons have **black text** on the accent (all three accents pass contrast with black).

### ⚠ Contrast — open issue

`#7A7F87` on white is about **4.0:1**, which fails WCAG AA (4.5:1) for normal-size text. It is used for `.lead`, `.card__text`, `.eyebrow` and `.hero__lead`. On black it passes (about 5.2:1).
**Proposed fix (needs brand sign-off):** a text-only token `--grey-text: #6B7078` (about 5.0:1 on white, 4.6:1 on `--soft`) for grey body text on light backgrounds. Keep `#7A7F87` for non-text uses and on black.

---

## 3. Typography

| Token | Stack |
|---|---|
| `--font-primary` | Poppins → system-ui… (display, headings, buttons, labels) |
| `--font-secondary` | Inter → system-ui… (body, form fields) |

Loaded from Google Fonts: Inter 400/500/600, Poppins 400/500/600/700, `display=swap`.

| Style | Class | Size | Notes |
|---|---|---|---|
| Hero title | `.hero__title` / `.app__title` | `clamp(2.9rem, 1.5rem + 6vw, 6.75rem)` | 600, `-0.04em`, line-height 0.98, max 13ch |
| H2 | `.h2` | `clamp(2rem, 1.3rem + 3vw, 3.75rem)` | 600, `-0.025em`, line-height 1.05 |
| H3 | `.h3` | `clamp(1.5rem, 1.2rem + 1.2vw, 2.1rem)` | |
| Lead | `.lead` / `.hero__lead` | `clamp(1.1rem, 1rem + 0.4vw, 1.3rem)` | grey, max 52ch |
| Body | `body` | `1.0625rem / 1.65` | Inter 400 |
| Prose | `.prose` | `1.125rem` | for editor / WYSIWYG content |
| Eyebrow | `.eyebrow` | `0.78rem` | Poppins 500, **0.28em tracking**, uppercase, grey — echoes the logo's "CREATIVE SYSTEMS" |
| Accent eyebrow | `.eyebrow--accent` | | black text with a 1.75rem × 3px `--app` rule before it |

Copy is **UK English**. Headings are short statements ending with a full stop ("Systems for creative work.").

---

## 4. Layout & spacing

| Token | Value |
|---|---|
| `--wrap` | `1200px` max content width (`.wrap`) |
| `.wrap--text` | `46rem` reading width |
| `--gut` | `clamp(1.125rem, 4vw, 2.5rem)` side gutter |
| `--sec` | `clamp(4.5rem, 10vw, 8rem)` section padding (`.section`) |
| `--radius` | `14px` cards and images |
| `--ease` | `cubic-bezier(0.2, 0.7, 0.2, 1)` |

Two-column modules use `minmax(0, Xfr)` grids with a `clamp(2rem, 6vw, 5rem)` gap and collapse to one column at about 860–900px.

### Breakpoints (max-width, desktop-first, as built)

| px | What changes |
|---|---|
| 960 | Cards 3 → 2 columns |
| 900 | Spotlight to one column |
| 860 | Hero, split text, image + text, FAQ, contact to one column |
| 820 | Header menu becomes the toggle / drop-down |
| 620 | Cards → 1 column; featured card stops spanning |

> Bonsai standard is mobile-first. This theme was built desktop-first; keep new modules consistent with the existing `max-width` queries unless the whole sheet gets refactored.

---

## 5. Components (assets/site.css)

- **Buttons** `.btn` — pill (`999px`), Poppins 500, minimum height 2.75rem.
  `--dark` (black, primary) · `--line` (outline) · `--accent` (suite colour, product moments only) · `--line-light` (outline on black) · `--big` (hero / CTA size).
  Rows sit in `.btns`. In modules the editor chooses the style per button.
- **Pills** `.pill--{status}` — `live` / `demo` are black with an accent dot; `development` / `soon` are soft grey.
- **App card** `.card` (`parts/app-card.php`) — white, 1px line, lifts 3px on hover, whole card clickable (title link `::after`). `--featured` is black and spans 2×2.
- **Encore flow** `.flow` (`parts/encore-flow.php`) — decorative Airtable → Publish → site animation. `aria-hidden`; the steps beside it carry the meaning.
- **Contact form** `.contact` / `.form` (`parts/contact.php`) — soft grey section, 10px-radius fields, honeypot `.hp`.
- **Header** `.top` — sticky, 90% white with blur. **Footer** `.foot` — black, large spaced wordmark.

---

## 6. Page modules

| Module | Layout name | Background | Notes |
|---|---|---|---|
| Hero | `hero` | white | First hero = page H1. Optional logo-art tile. |
| App suites | `app_suites` | white | Cards from the Apps CPT, grouped by suite; one featured. |
| App spotlight | `spotlight` | **black** | "The only place the accent leads." Steps, flow illustration. |
| Split text | `split_text` | white | Heading left, WYSIWYG right (the About section). |
| Image + text | `image_text` | white / soft | Image left or right. |
| FAQ | `faq` | white | `<details>` accordion, optional FAQPage schema. |
| CTA band | `cta` | **black** | Pick an accent suite; dark/line buttons are inverted on black. |
| Contact form | `contact` | soft | Always `#contact`, once per page. |

**Rhythm:** avoid two black modules back to back. Spotlight → CTA reads as one block, so put a white module between them.

---

## 7. Motion

- Hover transitions 0.2–0.3s; cards lift `translateY(-3px)`.
- Flow illustration loops on a 3.2s cycle (`glow`, `pulse`, `arrive`).
- `prefers-reduced-motion: reduce` turns off all animation and transitions and smooth scrolling. (The `!important` here is intentional, the standard reduced-motion override.)

---

## 8. Accessibility rules

- Visible focus: 2px outline, black (white on black sections).
- Skip link, `main` focus target, `aria-expanded` menu toggle, Esc closes the menu.
- Decorative SVG and emoji are `aria-hidden`.
- One H1 per page (hero, page title, or a visually hidden H1 on the front page).
- Images: alt text is set in the Media Library. Flag missing alt before launch.
- Known issue: grey text contrast (see §2).

---

## 9. Brand assets (`assets/brand/`)

| File | Use |
|---|---|
| `primary-logo.svg` | Websites and decks (inlined as `drift_mark()` so it inherits `currentColor`) |
| `submark.svg` | App icons, favicons, social avatars |
| `favicon.svg`, `favicon-32.png`, `apple-touch-icon.png` | Output in `<head>` unless a Site Icon is set |
| `og-image.png` | 1200×630 Open Graph image |
| `social-avatar.svg` | Social profiles |
