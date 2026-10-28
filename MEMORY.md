# MEMORY.md — Drift Creative Systems

Running record of decisions, state and open issues. Newest first.

## Current state (2026-10-28, v1.9.0)
- Latest release v1.9.0: https://github.com/drift-creative-systems/drift-create/releases/tag/v1.9.0
- Site is moving from "Music Suite home" to a Drift-wide Home (Suite tiles → App suites → Stats → Roadmap) with a landing page per suite. Content work is in wp-admin, see README "Suites, landing pages and roadmaps".
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
| 2026-10-10 | Emoji replaced by uploaded avatars (apps `drift_icon`, cards `image`); image fills the tile; no SVG | Real app avatars. Fallback is the initial after "Drift: ". Old `drift_emoji` meta left in the DB (harmless, recoverable). SVG needs sanitising — use Safe SVG plugin if ever needed. |
| 2026-10-09 | Video embeds are click-to-play (iframe injected by jQuery on click), youtube-nocookie / Vimeo dnt | No third-party requests or cookies before the visitor asks; better LCP. Iframe src is built server-side and whitelisted again in JS. |
| 2026-10-09 | Testimonials are an admin-only CPT, grid only, no app link, no Review schema | Client choice (grid, no app link). Self-serving Review schema isn't eligible for Google stars. |
| 2026-10-09 | Team is an admin-only CPT (`public: false`), shown only via the Team module | Small team; no thin profile pages in the sitemap. Bio sits in a `<details>` toggle. |
| 2026-10-09 | Apps use Page modules; app hero stays automatic above them; editor hidden on apps | Same builder everywhere. `hide_on_screen` is set on both Page modules and App details because ACF takes it from the first group on screen and both are `menu_order` 0. |
| 2026-10-09 | Cards module can show manual cards or apps; pricing price is free text | Covers features/services and app picks with one module. Free text allows "From £…", "POA", "Free". |
| 2026-10-09 | Legal links via a WP menu location (`legal`), not Drift Settings | Editors already manage menus; the privacy policy page is the fallback. |
| 2026-10-08 | Page editor hidden via ACF `hide_on_screen`; Content module replaces it | Pages are built only from modules. Old editor content still renders on pages with no modules, but can't be edited — move it into a Content module. |
| 2026-10-26 | Mailing list = Brevo double opt-in via direct API call (no plugin); one sitewide list (#2); footer column + page module; email only | Client choice. API key is a Drift Settings field (client choice) with a `DRIFT_BREVO_API_KEY` wp-config override. It's in the DB, not the public repo. |
| 2026-10-28 | "WP Agency Kit" renamed **Agency Kit** (key `agency` and colour unchanged); Beacon (self-hosted PHP) and Redline (WP plugin) go in it; SEO stays separate and hidden until it has apps | Suites are grouped by audience, not platform. Beacon isn't WordPress. Label-only change = no data migration. |
| 2026-10-28 | Platform is an app attribute (WordPress plugin / Self-hosted / Desktop app / Web app) with a filter, not a suite | Visitors pick by who it's for; platform narrows it down. Suites survive an app gaining a second platform. |
| 2026-10-28 | Page suite field tints a page via a body class; blank module accents inherit it | Suite landing pages are duplicates of one layout; one dropdown re-themes the page. |
| 2026-10-28 | Roadmap = repeater on each app (not a CPT), shown on the app page, `/apps/{app}/roadmap/` (endpoint) and a hub module; Now / Next / Later, optional quarter, no dates on plans | One place per app; quarters, not dates, so the public roadmap doesn't make promises. Endpoint = no extra pages to create. |
| 2026-10-28 | `/roadmap/` page is noindex below 5 items, 404 with none | Avoid thin pages in search. |
| 2026-10-28 | Proposals (first: Drift: Surface) are noindex web pages built from modules + a generic **Table** module, with the PDF as a download button. No one-off "landing page" module | Send a link instead of a PDF. A generic table is reusable; a whole-proposal module would only fit one page. Surface sits under Music Suite (pink), so no new accent. |
| 2026-10-28 | Table **Prose width** for text pages (policies): reading column, text-sized heading, no section padding next to Content / prose tables. Prose always wraps values; Wide keeps nowrap unless "Wrap long values" is on | Text and tables alternating on the Privacy Policy read as two page styles. Opt-in, so pricing/proposal tables don't change. |
| 0.2.0 | Suites set the accent colour; per-app colours dropped | Brand system: one accent per product suite. |

## Open issues / to check
- [ ] **Test 1.10 on WP:** ACF Sync (Page modules, Page settings), Table module empty / 1–3 value columns / highlighted row / two in a row / phone scroll + keyboard focus, PDF button downloads, noindex toggle (view source robots tag; `/wp-sitemap-pages-1.xml`; with Yoast or Rank Math active). Rank Math's own sitemap doesn't get the exclusion: tick noindex in Rank Math too if it's used.
- [ ] **Test 1.11 on WP:** ACF Sync (Page modules), set the four tables on Privacy Policy (ID 276) to Prose width, check gaps text→table→text, light/dark, 375/768/1280px. Existing Wide tables unchanged.
- [ ] **Privacy Policy `#contact` clash:** the policy's "Contact" heading has `id="contact"`, which duplicates the contact section anchor the form handler redirects to. Rename the heading anchor (e.g. `contact-us`) in the editor.
- [ ] **Contrast:** `#7A7F87` on white ≈ 4.0:1, which fails AA for body text. Proposed `--grey-text: #6B7078`. Needs brand sign-off (DESIGN.md §2).
- [ ] **jQuery** is now loaded on the front end (about 30 KB) for a 20-line menu script. That's the Bonsai standard, but it costs a little INP/LCP. Revisit if PageSpeed suffers.
- [ ] Seeded blurbs for Greenroom, Stageside, Wristband and Wristband Fan are first drafts. Review the copy before launch.
- [ ] Add Beacon and Redline as Apps (Agency Kit; platforms Self-hosted / WordPress plugin), with avatars and excerpts. SEO suite still empty, so it stays hidden.
- [ ] **Test 1.9 on WP:** ACF Sync (4 changed / 2 new field groups), Drift Settings → Suites, Page suite tint, blank accent inherits, suite tiles, App suites platform filter (keyboard + screen reader announcement), stats, roadmap module + suite filter, app `#roadmap`, `/apps/{app}/roadmap/` (flush ran: option `drift_create_rewrites` = 1.9.0; 404 for an app with no items; noindex < 5 items; canonical points at /roadmap/). Check the page editor is still hidden on pages and apps.
- [ ] With Yoast / Rank Math active, the `/roadmap/` canonical and noindex come from the plugin, not the theme. Check it doesn't canonicalise to the app page; add a `wpseo_canonical` / `rank_math/frontend/canonical` filter if it does.
- [ ] Existing module accent fields saved as "Music" stay Music. On the Agency Kit landing page (duplicated from Home) clear them or switch to Agency Kit.
- [ ] Old front page → /music-suite/: duplicate, set Page suite = Music, set it as the Music landing page in Drift Settings → Suites. Watch Search Console for music queries moving from / to /music-suite/.
- [ ] Google Fonts load from Google's CDN. Consider self-hosting for GDPR and performance.
- [ ] CSS is desktop-first (`max-width`), against the Bonsai mobile-first standard. Refactor only as a separate task.
- [ ] Test on WP: activation seed, 0.2→0.3 upgrade, ACF Sync, contact form delivery (wp_mail / SMTP), each module empty and full.
- [ ] Set the contact recipient in Drift Settings (defaults to the admin email).
- [ ] Brevo: fill in Drift Settings → Newsletter (API key, DOI template ID, redirect page), then test sign-up → confirm email → contact lands in list #2. Also test a bad key (friendly error + `Drift newsletter:` log line).
- [ ] Privacy policy should mention Brevo as a processor for newsletter sign-ups.
- [ ] Create T&Cs / Privacy / Cookies pages and assign them to the "Footer legal links" menu.
- [ ] Seeded apps keep their editor content until it's moved into a Content module (editor is now hidden on apps).
- [ ] **Cookiebot:** check its auto-blocker doesn't block the click-to-play YouTube/Vimeo iframe. If it does, add consent markup or treat the click as the consent moment.
- [ ] Pricing ticks / badge use the suite accent; SEO green (#57E35B) on white is low contrast. Fine as decoration, but don't rely on it for meaning.

## Gotchas
- New field groups must keep `hide_on_screen: the_content` and a `menu_order` above 0, or they can bring the editor back on pages / apps (ACF uses the first group's setting).
- `drift_accent_style( '' )` returns '' on purpose (inherit the page suite). Don't reintroduce a Music fallback there.
- The platform and roadmap filters share `parts/filter.php` + `bonsai_applyFilter()`. Items need `data-filter-tags`; wrappers that should hide when empty need `data-filter-group`.
- `drift_page_layouts()` reads the raw `page_modules` post meta (ACF stores the layout names there). Used before the loop to enqueue CSS.
- `parts/contact.php` is always `id="contact"`. The form handler redirects to `#contact`.
- Newsletter forms redirect to their own anchor (`#newsletter` footer, `#signup` module default) with `?nl=…&nl_form=…`; only the matching form shows the result. Nonce is printed by hand, not `wp_nonce_field()`, to avoid duplicate ids with two forms on a page.
- `newsletter_footer` is a true_false: `drift_option()` would turn "off" (false) into the fallback, so it's read with `get_field()`.
- Encore flow illustration shows automatically on the `encore` app page (slug check in `single-drift_app.php`). In modules it's a toggle.
