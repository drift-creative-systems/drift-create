# MEMORY.md — Drift Creative Systems

Running record of decisions, state and open issues. Newest first.

## Current state (2026-10-09, v1.3.0)
- Latest release v1.2.0: https://github.com/drift-creative-systems/drift-create/releases/tag/v1.2.0
- Theme converted to ACF Flexible Content page modules. Not yet tested on a WordPress install.
- Updates via GitHub releases (Plugin Update Checker 5.7). No GitHub Actions deploy yet. Releases are built by hand (README).
- No staging / live URLs recorded yet.

## Decisions
| Date | Decision | Why |
|---|---|---|
| 2026-10-03 | Public repo, updates via PUC + `drift-create.zip` release asset, `vendor/` committed | Same pattern as the Bonsai plugins (bonsai-page-transitions). Public = no token needed on sites. |
| 2026-10-03 | Unreleased 0.3.0 work shipped as 1.0.0 | First public release. `inc/upgrade.php` keeps its 0.2/0.3 milestones as data-migration markers. |
| 2026-10-03 | Plain CSS per module (`modules/{layout}/module.css`), no SCSS build | Theme had no build step. Per-module files load only where used. |
| 2026-10-03 | App details moved to ACF using the **original meta keys** | Zero data migration. Templates keep `get_post_meta()`, so they work without ACF. |
| 2026-10-03 | Sitewide settings on an ACF options page (Drift Settings) | Bonsai standard. Customiser brand line kept as a read-only fallback. |
| 2026-10-03 | Module layout names = folder names (snake_case) | One mapping, no translation layer. |
| 2026-10-03 | Shared components (cards, flow, contact, buttons) stay in `site.css` | Used by both modules and `single-drift_app.php`. |
| 2026-10-03 | Hero CSS also loads on 404, app pages and the no-module front page | Those templates reuse `.hero` / `.hero__lead`. |
| 2026-10-03 | Front page modules seeded once via `drift_create_modules_seeded` option | Upgrading sites keep the same look with no manual rebuild. |
| 2026-10-03 | Theme meta/OG tags skipped when an SEO plugin is active | Avoid duplicate meta descriptions. |
| 2026-10-09 | Video embeds are click-to-play (iframe injected by jQuery on click), youtube-nocookie / Vimeo dnt | No third-party requests or cookies before the visitor asks; better LCP. Iframe src is built server-side and whitelisted again in JS. |
| 2026-10-09 | Testimonials are an admin-only CPT, grid only, no app link, no Review schema | Client choice (grid, no app link). Self-serving Review schema isn't eligible for Google stars. |
| 2026-10-09 | Team is an admin-only CPT (`public: false`), shown only via the Team module | Small team; no thin profile pages in the sitemap. Bio sits in a `<details>` toggle. |
| 2026-10-09 | Apps use Page modules; app hero stays automatic above them; editor hidden on apps | Same builder everywhere. `hide_on_screen` is set on both Page modules and App details because ACF takes it from the first group on screen and both are `menu_order` 0. |
| 2026-10-09 | Cards module can show manual cards or apps; pricing price is free text | Covers features/services and app picks with one module. Free text allows "From £…", "POA", "Free". |
| 2026-10-09 | Legal links via a WP menu location (`legal`), not Drift Settings | Editors already manage menus; the privacy policy page is the fallback. |
| 2026-10-08 | Page editor hidden via ACF `hide_on_screen`; Content module replaces it | Pages are built only from modules. Old editor content still renders on pages with no modules, but can't be edited — move it into a Content module. |
| 0.2.0 | Suites set the accent colour; per-app colours dropped | Brand system: one accent per product suite. |

## Open issues / to check
- [ ] **Contrast:** `#7A7F87` on white ≈ 4.0:1, which fails AA for body text. Proposed `--grey-text: #6B7078`. Needs brand sign-off (DESIGN.md §2).
- [ ] **jQuery** is now loaded on the front end (about 30 KB) for a 20-line menu script. That's the Bonsai standard, but it costs a little INP/LCP. Revisit if PageSpeed suffers.
- [ ] Seeded blurbs for Greenroom, Stageside, Wristband and Wristband Fan are first drafts. Review the copy before launch.
- [ ] No apps yet in the WP Agency Kit or SEO suites. Those suites stay hidden until apps are added.
- [ ] Google Fonts load from Google's CDN. Consider self-hosting for GDPR and performance.
- [ ] CSS is desktop-first (`max-width`), against the Bonsai mobile-first standard. Refactor only as a separate task.
- [ ] Test on WP: activation seed, 0.2→0.3 upgrade, ACF Sync, contact form delivery (wp_mail / SMTP), each module empty and full.
- [ ] Set the contact recipient in Drift Settings (defaults to the admin email).
- [ ] Create T&Cs / Privacy / Cookies pages and assign them to the "Footer legal links" menu.
- [ ] Seeded apps keep their editor content until it's moved into a Content module (editor is now hidden on apps).
- [ ] **Cookiebot:** check its auto-blocker doesn't block the click-to-play YouTube/Vimeo iframe. If it does, add consent markup or treat the click as the consent moment.
- [ ] Pricing ticks / badge use the suite accent; SEO green (#57E35B) on white is low contrast. Fine as decoration, but don't rely on it for meaning.

## Gotchas
- `drift_page_layouts()` reads the raw `page_modules` post meta (ACF stores the layout names there). Used before the loop to enqueue CSS.
- `parts/contact.php` is always `id="contact"`. The form handler redirects to `#contact`.
- Encore flow illustration shows automatically on the `encore` app page (slug check in `single-drift_app.php`). In modules it's a toggle.
