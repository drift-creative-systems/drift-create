# Changelog

All notable changes to this theme are documented here.
Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). Versioning: [SemVer](https://semver.org/).

## [1.6.0] - 2026-10-09

### Added
- **Dark mode.** The site follows the visitor's OS light / dark setting. A sun / moon toggle in the header (right of "Book a demo"; left of the burger on mobile) overrides it, and the choice is remembered (`localStorage` key `drift-theme`). A small inline script in `<head>` (`inc/setup.php`) applies a saved choice before first paint, so there's no flash.
- **Header logo (dark mode)** upload on Drift Settings → Brand (`header_logo_dark`). Used in dark mode when a Header logo is set; blank = the built-in SVG logo in dark mode.
- `theme-color` meta for each colour scheme.

### Changed
- All CSS now reads theme tokens (`--bg`, `--fg`, `--muted`, `--surface`, `--band`, `--band-fg`…) instead of `--black` / `--white`. Light mode looks the same as before. Dark values are listed in DESIGN.md §2.
- In dark mode the black bands (spotlight, CTA, footer, featured card / plan, hero tile, app icon) lift to `#17181A` so they still stand off the page.
- The flow illustration, full-width image overlays and video frames keep fixed colours in both themes.

## [1.5.0] - 2026-10-09

### Added
- **Header logo**, **Footer logo** and **Hero logo** uploads on Drift Settings → Brand (`header_logo`, `footer_logo`, `hero_logo`). Header and footer logos replace the whole lockup (mark and "DRIFT" text); the hero logo replaces the mark inside the black tile. Blank = the built-in SVG logo, as before. `drift_logo()` (`inc/brand.php`) renders them.

## [1.4.0] - 2026-10-10

### Added
- **App avatar** upload (`drift_icon`, App details): a square PNG, WebP or JPG that fills the rounded tile on app cards and the app page hero. `drift_app_icon()` renders the tile.
- **Cards** module: icon image upload per card, filling the small tile.

### Changed
- Apps without an avatar show the app's initial (the name after "Drift: ") in the tile instead of an emoji.
- Fresh installs no longer seed app emoji.

### Removed
- Emoji fields: **Emoji** on App details (`drift_emoji`) and **Icon** (emoji text) on Cards module cards. Existing emoji values stay in the database but are no longer shown.
- `.card__emoji` / `.app__emoji` classes, renamed `.card__icon` / `.app__icon`.

## [1.3.0] - 2026-10-09

### Added
- **Team** post type (`drift_team`, `inc/team.php`): admin-only (no public URLs). Title = name, featured image = photo, Page Attributes → Order = display order.
- **Team details** ACF field group: role, optional suite (sets the accent), bio, and up to five links (LinkedIn, website, email). Field names are the meta keys, read with `drift_team_meta()` so they work without ACF.
- **Testimonials** post type (`drift_testimonial`, `inc/testimonials.php`): admin-only. Title = name, featured image = headshot, plus quote, role and company (**Testimonial details** ACF group, read with `drift_testimonial_meta()`).
- **Testimonials** page module (`modules/testimonials/`): quote cards in 1, 2 or 3 columns. Shows all, the first N in order, or picked testimonials.
- **Video** page module (`modules/video/`): a YouTube/Vimeo link or an uploaded MP4/WebM, with heading, intro, poster, caption and narrow/wide width. YouTube/Vimeo are click-to-play: nothing loads from them until the play button is pressed, then `youtube-nocookie.com` / Vimeo `dnt=1` is used (`drift_video_embed_url()`, `assets/site.js`).
- **Full-width image** page module (`modules/full_image/`): edge to edge or page width; natural ratio or short/medium/tall crop with a focal point; optional caption; optional overlay eyebrow, heading, text and buttons with position and darkening options.
- **Team** page module (`modules/team/`): eyebrow, heading, intro and member cards (photo or initials, name, role, suite, "Read bio" toggle, links). Shows everyone in order, picked members, or one suite only; 3 or 4 columns.

### Changed
- **Cards** module: card text is now a rich text (WYSIWYG) field and the title is optional. A linked card with no title uses its link text as the clickable line. Links inside card text stay clickable. Existing plain-text card text carries over.

## [1.2.0] - 2026-10-09

### Added
- **Cards** page module (`modules/cards/`): eyebrow, heading, intro and a 2/3/4-column grid of either your own cards (icon, title, text, optional link) or chosen Apps (empty = all apps; the current app is left out on app pages). Optional buttons underneath.
- **Pricing table** page module (`modules/pricing/`): plans with a free-text price ("£29", "From £499", "POA"), period, short description, feature list (one per line) and button. A highlighted plan is shown in black with an optional badge. Optional small-print line.
- **Footer legal links** menu location (Appearance → Menus), shown beside the copyright line. Falls back to the Privacy Policy page set in Settings → Privacy.

### Changed
- Apps can be built with **Page modules**. The app hero (App details) still shows first and holds the H1; modules replace the editor body. Apps with no modules still show their old editor content.
- The editor is hidden on Apps, like Pages. The excerpt (card text) stays.
- On app pages, the automatic contact form is skipped when the app has a Contact module, and the Contact module preselects the current app.
- Footer base line is now a flex row (`div.foot__base`) holding the copyright and the legal menu.

## [1.1.0] - 2026-10-08

### Added
- **Content** page module (`modules/content/`): heading and a full-toolbar WYSIWYG block in the reading-width column. As the first module it prints the page's H1, using the page title when the heading is empty; further down it's an H2.
- `drift_layout_has_h1()`, used by `page.php` and `front-page.php` so a page that starts with a Content module doesn't get a second H1.

### Changed
- The WordPress content editor is hidden on Pages (Page modules field group, `hide_on_screen: the_content`). Pages are built only from modules. Pages with no modules still show any existing editor content on the front end.

## [1.0.0] - 2026-10-03

First public release.

### Added
- Automatic theme updates from GitHub releases (`inc/updates.php`, Plugin Update Checker 5.7 via Composer, `vendor/` committed). New versions appear under Dashboard → Updates.
- ACF Flexible Content page builder (`page_modules`) on Pages, with modules in `modules/{layout}/`: Hero, App suites, App spotlight, Split text, Image + text, FAQ (with optional FAQPage JSON-LD), CTA band, Contact form.
- Per-module CSS (`modules/{layout}/module.css`), enqueued only on pages that use the module.
- **Drift Settings** ACF options page: brand line, footer tagline, footer links, contact recipient and copy, default meta description.
- ACF JSON sync (`acf-json/`) for the Page modules, App details and Drift Settings field groups.
- Front page seeding: Home gets modules that recreate the original layout (fresh installs and 0.2 upgrades, once, only if it has none).
- Admin notice when ACF Pro is inactive; front page falls back to hero + apps + contact.
- Anchor ID field on modules for in-page links.
- Contact form shows an error when `wp_mail()` fails, and logs it.
- `parts/app-suites.php`, shared by the module and the fallback.
- Project docs: README, DESIGN.md, CHANGELOG, MEMORY.md, CLAUDE.md, llm-instructions.txt.

### Changed
- `functions.php` split into `inc/` files (setup, acf, apps, brand, meta, contact, modules, upgrade).
- App details meta box replaced by an ACF field group using the **same meta keys**, so no data migration is needed.
- Brand line moved from Customiser → Brand to Drift Settings (Customiser value still used as fallback).
- `front-page.php` and `page.php` render page modules; pages without modules show their editor content.
- Hero, spotlight and about styles moved out of `assets/site.css` into module CSS. `.about__grid` renamed `.split__grid`.
- `assets/site.js` rewritten in jQuery (now depends on `jquery`).
- Meta description / OG tags are skipped when Yoast, Rank Math, AIOSEO or SEOPress is active.

### Removed
- Custom "App details" meta box and its save handler (replaced by ACF).
- Customiser "Brand" section.

### Fixed
- Footer GitHub link pointed to a non-existent account (`Drift-Apps`); it now goes to https://github.com/drift-creative-systems.

## [0.2.0]

### Added
- Product suites (Music, WP Agency Kit, SEO) with per-suite accents; cards grouped by suite.

### Changed
- Per-app colours replaced by the suite accent (optional override).

## [0.1.0]

### Added
- Initial theme: Apps CPT, hardcoded home page, app pages, contact form, brand assets.
