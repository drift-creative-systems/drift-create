# Changelog

All notable changes to this theme are documented here.
Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). Versioning: [SemVer](https://semver.org/).

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
