<?php
/**
 * Drift Creative Systems — marketing theme.
 *
 * Brand: Drift Brand System (assets/brand/, DESIGN.md) — Poppins + Inter,
 * black / white / #7A7F87, one accent per product suite (Music #FF4FA3,
 * WP Agency Kit #1F7BFF, SEO #57E35B).
 *
 * Pages are built from ACF Flexible Content modules (modules/{layout}/).
 * Apps are a post type with an ACF "App details" field group. Sitewide
 * settings live under Drift Settings (ACF options page).
 *
 * On first activation the theme seeds the apps and a Home page built from
 * modules.
 *
 * Updates come from GitHub releases (inc/updates.php).
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

define( 'DRIFT_CREATE_VERSION', '1.1.0' );

require_once get_theme_file_path( 'inc/setup.php' );
require_once get_theme_file_path( 'inc/acf.php' );
require_once get_theme_file_path( 'inc/apps.php' );
require_once get_theme_file_path( 'inc/brand.php' );
require_once get_theme_file_path( 'inc/meta.php' );
require_once get_theme_file_path( 'inc/contact.php' );
require_once get_theme_file_path( 'inc/modules.php' );
require_once get_theme_file_path( 'inc/upgrade.php' );
require_once get_theme_file_path( 'inc/updates.php' );
