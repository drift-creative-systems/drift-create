# Drift Creative Systems — WordPress theme

Marketing site theme for **Drift**: product suites, app cards, an app spotlight and a demo / contact form. Built by The Bonsai Digital Collective on the Drift Brand System (see [DESIGN.md](DESIGN.md)).

- **Version:** 1.0.0
- **Repo:** https://github.com/drift-creative-systems/drift-create
- **Requires:** WordPress 6.3+, PHP 8.0+, **ACF Pro**
- **Text domain:** `drift-create`

---

## Install

1. Install and activate **ACF Pro** (licence applied).
2. Download `drift-create.zip` from the [latest release](https://github.com/drift-creative-systems/drift-create/releases/latest), upload it under Appearance → Themes → Add New → Upload, and activate it. (The folder must be named `drift-create`. GitHub's auto-generated "Source code" zips won't work.)
3. On first activation the theme:
   - creates the Apps (Encore, Greenroom, Stageside, Wristband, Wristband Fan)
   - creates a **Home** page and sets it as the static front page
   - fills Home with page modules matching the original design (once ACF Pro is active)
4. Field groups load automatically from `acf-json/`. If wp-admin → ACF → Field Groups shows **Sync available**, click it.
5. Check **Drift Settings** (contact recipient, footer, brand line) and **Settings → Permalinks** (save once if `/apps/…` 404s).

---

## Editing content

| What | Where |
|---|---|
| Page layout and copy | **Pages → (page) → Page modules** |
| Apps (cards, app pages) | **Apps** — title, excerpt = card text, **App details** box (incl. **App avatar** upload and **Platforms**), **Page modules** = app page body, **Roadmap** box = the app's roadmap items, Page Attributes → Order = card order |
| Team members | **Team** — title = name, featured image = photo, **Team details** box (role, suite, bio, links), Page Attributes → Order = display order. No public pages; they show through the Team module. |
| Testimonials | **Testimonials** — title = name, featured image = headshot (optional), **Testimonial details** (quote, role, company), Page Attributes → Order. No public pages; shown through the Testimonials module. |
| Brand line, header / footer / hero logos, footer company details, footer links, social links, contact copy and recipient, newsletter (Brevo) settings and copy, suite taglines and landing pages, default meta description | **Drift Settings** |
| A page's suite colour and default suite | **Pages → (page) → Page settings → Page suite** (sidebar) |
| Keep a page out of Google (proposals sent by link) | **Pages → (page) → Page settings → Hide from search engines** (noindex + left out of the core and Yoast sitemaps) |
| A "Download PDF" button | Upload the PDF to **Media**, copy its URL into a button link. Buttons linking to a `.pdf` download it |
| Main menu | **Appearance → Menus** (location "Main menu"; falls back to Apps / Encore / About) |
| Legal links (T&Cs, Privacy, Cookies) | **Appearance → Menus** (location "Footer legal links"; falls back to the Privacy Policy page from Settings → Privacy) |

### Page modules

| Module | What it does |
|---|---|
| **Hero** | Eyebrow, big title (H1 if first), lead, buttons, optional logo tile |
| **Content** | Heading (H1 if first, page title if left empty) and a rich text block, for standard pages |
| **Suite tiles** | One tile per suite in its colour: tagline, app icons and names, available / coming count, link to the suite landing page. All automatic |
| **App suites (cards)** | Apps grouped by suite with optional headings; pick a featured app; optional platform filter. No rows = the page suite (or all) |
| **Stats** | Big numbers: counted from Apps (apps, available, suites, platforms) plus your own |
| **Cards** | Grid of 2–4 columns: your own cards (icon image, optional title, rich text, link) or chosen Apps |
| **App spotlight** | Black section for one app: steps, app page button, demo button, optional Encore flow animation |
| **Split text** | Heading left, rich text right |
| **Image + text** | Image left or right, optional grey background, buttons |
| **Testimonials** | Quote cards in 1–3 columns: all, the first N, or picked |
| **Team** | Team members as cards: photo, name, role, suite, "Read bio" toggle, links. Everyone, picked people, or one suite |
| **Full-width image** | Edge to edge or page width; natural or short/medium/tall crop with focal point; caption; optional overlay heading, text and buttons |
| **Video** | YouTube/Vimeo link (click to play, privacy-enhanced) or uploaded MP4/WebM; poster, caption, narrow or wide |
| **Pricing table** | Plans with free-text price, period, feature list and button; highlight one with a badge |
| **Table** | Data table (price lists, comparisons, worked examples): label + optional detail line, up to 3 value columns, highlighted total rows, small print. Two in a row sit together under one heading. Prose width lines it up with Content text (policies); "Wrap long values" for long-text cells |
| **Roadmap** | Every app's roadmap items as Now / Next / Later columns plus Recently shipped; optional suite filter and button. Suites = picked, else the page suite, else all |
| **FAQ** | Accordion, optional FAQPage schema |
| **CTA band** | Black band with heading, text, buttons and a suite accent |
| **Contact form** | Demo / enquiry form (always `#contact`) |
| **Newsletter sign-up** | Heading and text beside the mailing list form (Brevo). Hidden until Brevo is set up |

### Mailing list (Brevo)

Sign-ups use Brevo **double opt-in**: Brevo emails a confirmation link, and people only join the list once they click it. Set it up under **Drift Settings → Newsletter**:

1. **API key**: Brevo → SMTP & API → API Keys. To keep it out of the database instead, add `define( 'DRIFT_BREVO_API_KEY', 'xkeysib-…' );` to `wp-config.php`, which overrides the field.
2. **List ID**: Brevo → Contacts → Lists (defaults to 2).
3. **Double opt-in template ID**: Brevo → Templates. The template needs the "optin" tag and a `{{ doubleoptin }}` confirm link.
4. **After confirming, go to**: a thank-you page (blank = home page).

The footer form (`#newsletter`) and Newsletter modules stay hidden until 1–3 are filled in. Failed Brevo requests go to the PHP error log as `Drift newsletter: …`.

Every module except Contact has an **Anchor ID** field, so menus and buttons can link to `/#apps`, `/#about` and so on.

The editor is hidden on Pages and Apps, so both are built only from modules. A page or app with **no modules** still shows any older editor content. A page whose first module isn't a Hero or Content module shows the page title as its H1.

On **app pages** the app hero (from App details) always shows first and holds the H1; modules follow it. The contact form is added at the bottom automatically unless the app has a Contact module.

### Suites, landing pages and roadmaps

- **Suites** are Music Suite, Agency Kit and SEO (`drift_suites()` in `inc/apps.php`). Set each suite's tagline and landing page under **Drift Settings → Suites**. Suite tiles and the app page "←" link go to the landing page.
- **Suite landing page:** build it from modules like any page and set **Page suite** in the sidebar. The page takes the suite colour, accent fields left blank follow it, and App suites / Roadmap modules show only that suite unless you pick suites in them.
- **Roadmaps:** add items in the app's **Roadmap** box (item, stage Now / Next / Later / Shipped, optional target quarter, shipped date, note). They appear on the app page (`#roadmap`), on the app's own page at `/apps/{app}/roadmap/`, and in Roadmap modules. The `/roadmap/` page is `noindex` until it has 5 items, and returns 404 for an app with no items.

---

## Structure

```
drift-create/
├── functions.php          Constants + requires inc/*
├── inc/
│   ├── setup.php          Theme supports, menus, asset enqueue (+ per-module CSS)
│   ├── acf.php            ACF JSON paths, Drift Settings options page, drift_option(), choice filters
│   ├── apps.php           drift_app CPT, suites, page suite, statuses, platforms, drift_app_meta(), drift_apps()
│   ├── roadmap.php        App roadmaps: drift_app_roadmap(), /roadmap/ endpoint, canonical / title / robots
│   ├── team.php           drift_team CPT (admin-only), drift_team_meta(), drift_team_members()
│   ├── testimonials.php   drift_testimonial CPT (admin-only), drift_testimonial_meta(), drift_testimonials()
│   ├── brand.php          Brand name/line, logo mark SVG, favicons, document title
│   ├── meta.php           Meta description + OG (skipped if an SEO plugin is active)
│   ├── contact.php        admin-post form handler → wp_mail, drift_contact_form()
│   ├── newsletter.php     admin-post sign-up handler → Brevo double opt-in, drift_newsletter_form()
│   ├── modules.php        Flexible content renderer + module helpers
│   ├── upgrade.php        First-run seed + version upgrades
│   ├── updates.php        GitHub release updates (Plugin Update Checker)
│   └── seed.php           Seed content (loaded only when seeding)
├── modules/{layout}/      module.php (+ optional module.css, loaded only where used)
├── parts/                 app-card, app-suites, filter, roadmap, contact, newsletter, encore-flow
├── vendor/                Composer (plugin-update-checker) — committed
├── acf-json/              Field groups — commit every change
├── assets/                site.css (base + components), site.js (jQuery), brand/
├── front-page.php, page.php, single-drift_app.php, index.php, 404.php, header.php, footer.php
└── DESIGN.md, CHANGELOG.md, MEMORY.md, CLAUDE.md, llm-instructions.txt
```

### Adding a module

1. In ACF, add a layout to **Page modules** (`page_modules`). Use a snake_case name, e.g. `logo_strip`. Include an **Anchor ID** (`section_id`) field.
2. Create `modules/logo_strip/module.php` (use `get_sub_field()`, escape everything, return early if the key field is empty). Add `module.css` if it needs styles.
3. Add `logo_strip` to `drift_module_layouts()` and the `switch` in `drift_render_modules()` (`inc/modules.php`).
4. Commit the updated `acf-json/group_drift_page_modules.json`.

---

## Development notes

- **No build step.** Plain CSS and jQuery. Assets are versioned by `filemtime`. The only Composer dependency is the update checker.
- ACF JSON saves into `acf-json/`. Edit field groups on a dev site, then commit the JSON.
- App detail fields use the original meta keys (`drift_suite`, `drift_tagline`, `drift_status`, `drift_platform`, `drift_icon` (avatar image ID), `drift_accent`, `drift_demo`, and the `drift_roadmap` repeater). Templates read them with `get_post_meta()`, so they work even if ACF is off.
- Filters (`parts/filter.php` + `assets/site.js`): put `data-filter-scope` on the section, `data-filter-tags="a b"` on items and `data-filter-group` on wrappers that should hide when empty.
- Without ACF Pro: an admin notice shows, the front page falls back to hero + apps + contact, and other pages show their editor content.

## Updates

The theme updates itself from GitHub releases via [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) (`inc/updates.php`). WordPress checks every 6 hours. New versions appear under **Dashboard → Updates** and install the `drift-create.zip` release asset.

`vendor/` is committed, so the theme works straight from a release zip. After a fresh clone it's already there. Only run `composer install` if you change `composer.json`.

## Releasing a new version

Work on `develop`, then:

1. Bump the version in **both** `style.css` (`Version:`) and `functions.php` (`DRIFT_CREATE_VERSION`).
2. Add a dated section to `CHANGELOG.md`.
3. Merge `develop` → `main` and push.
4. Tag and build the zip from the tag (`.gitattributes` keeps dev files out of it):
   ```bash
   git tag v1.0.1 && git push origin v1.0.1
   git archive --format=zip --prefix=drift-create/ -o drift-create.zip v1.0.1
   ```
5. Create the release with the zip attached, named exactly `drift-create.zip`:
   ```bash
   gh release create v1.0.1 drift-create.zip --title "Drift Creative Systems 1.0.1" --notes "<changelog section>"
   ```

Sites pick it up on their next update check (or Dashboard → Updates → Check again).

## Upgrading from 0.2.x

Activate ACF Pro first. On the next page load the theme gives the front page the modules that recreate the old hardcoded layout (only if it has none). The Customiser "Brand" setting is still read as a fallback until **Drift Settings → Brand** is saved.

## Copyright

Copyright © 2026 Drift Creative Systems Ltd

Drift Creative Systems Ltd. Registered in England and Wales, company number 17548220. Registered office: 167-169 Great Portland Street, 5th Floor, London W1W 5PF
