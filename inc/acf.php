<?php
/**
 * ACF Pro: JSON sync, Drift Settings options page and field helpers.
 *
 * Field groups live in acf-json/ and are committed. Edit them in wp-admin
 * (Custom Fields) on a dev site and commit the updated JSON.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

/* Save and load field groups from the theme's acf-json/ folder. */
add_filter( 'acf/settings/save_json', static fn() => get_theme_file_path( 'acf-json' ) );
add_filter( 'acf/settings/load_json', static function ( array $paths ): array {
	$paths[] = get_theme_file_path( 'acf-json' );
	return $paths;
} );

add_action( 'acf/init', static function () {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( [
			'page_title' => 'Drift Settings',
			'menu_title' => 'Drift Settings',
			'menu_slug'  => 'drift-settings',
			'capability' => 'edit_theme_options',
			'icon_url'   => 'dashicons-admin-generic',
			'position'   => 59,
			'redirect'   => false,
		] );
	}
} );

/* Without ACF Pro the modules, app details and settings all fall back to defaults. */
add_action( 'admin_notices', static function () {
	if ( function_exists( 'acf_add_options_page' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-error"><p><strong>Drift Creative Systems</strong> needs <strong>ACF Pro</strong> for page modules, app details and Drift Settings. Pages show their fallback content until it is active.</p></div>';
} );

/**
 * Returns a Drift Settings value, or the fallback when ACF is off or the
 * field is empty.
 *
 * @param string $name     Field name on the options page.
 * @param mixed  $fallback Value to use when the field is empty.
 * @return mixed
 */
function drift_option( string $name, $fallback = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}
	$value = get_field( $name, 'option' );
	return ( null === $value || false === $value || '' === $value || [] === $value ) ? $fallback : $value;
}

/*
 * Suite and status choices come from drift_suites() / drift_app_statuses()
 * so the PHP lists stay the single source of truth. The JSON copies are only
 * a fallback.
 */
foreach ( [ 'drift_suite', 'suite', 'accent_suite', 'only_suite' ] as $drift_suite_field ) {
	add_filter( 'acf/load_field/name=' . $drift_suite_field, static function ( array $field ): array {
		$field['choices'] = wp_list_pluck( drift_suites(), 'label' );
		return $field;
	} );
}
add_filter( 'acf/load_field/name=drift_status', static function ( array $field ): array {
	$field['choices'] = drift_app_statuses();
	return $field;
} );
