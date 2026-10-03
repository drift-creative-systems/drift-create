<?php
/**
 * Theme updates from GitHub releases (Plugin Update Checker, via Composer).
 *
 * WordPress checks https://github.com/drift-creative-systems/drift-create
 * for new releases and installs the drift-create.zip release asset through
 * Dashboard → Updates, like any other theme. Release process: README.md.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$drift_autoload = get_template_directory() . '/vendor/autoload.php';

if ( ! file_exists( $drift_autoload ) ) {
	// Missing vendor/ (e.g. a raw git clone without composer install) — the theme still works, it just won't update.
	error_log( 'Drift Creative Systems: vendor/autoload.php missing, GitHub updates disabled. Run composer install.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	return;
}

require_once $drift_autoload;

if ( class_exists( \YahnisElsts\PluginUpdateChecker\v5\PucFactory::class ) ) {
	$drift_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		'https://github.com/drift-creative-systems/drift-create',
		get_template_directory() . '/functions.php',
		'drift-create',
		6
	);
	$drift_update_checker->setBranch( 'main' );
	$drift_update_checker->getVcsApi()->enableReleaseAssets();
}
