<?php
/**
 * Apps post type and helpers.
 *
 * App details (suite, tagline, status, emoji, accent override, demo link) are
 * an ACF field group (acf-json/group_drift_app_details.json). The field names
 * match the original meta keys, so values are read with get_post_meta() and
 * work with or without ACF active.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', static function () {
	register_post_type( 'drift_app', [
		'labels'        => [ 'name' => 'Apps', 'singular_name' => 'App', 'add_new_item' => 'Add app', 'edit_item' => 'Edit app' ],
		'public'        => true,
		'has_archive'   => false,
		'rewrite'       => [ 'slug' => 'apps', 'with_front' => false ],
		'menu_icon'     => 'dashicons-screenoptions',
		'menu_position' => 20,
		'supports'      => [ 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ],
		'show_in_rest'  => true,
	] );
} );

/**
 * Product suites → label + accent (Drift Brand System, "Product Accent Examples").
 *
 * @return array<string, array{label: string, accent: string}>
 */
function drift_suites(): array {
	return [
		'music'  => [ 'label' => 'Music Suite', 'accent' => '#FF4FA3' ],
		'agency' => [ 'label' => 'WP Agency Kit', 'accent' => '#1F7BFF' ],
		'seo'    => [ 'label' => 'SEO', 'accent' => '#57E35B' ],
	];
}

/**
 * Status → label. The key is also the .pill--{status} / .card--{status} modifier.
 *
 * @return array<string, string>
 */
function drift_app_statuses(): array {
	return [
		'live'        => 'Available now',
		'demo'        => 'Demo available',
		'development' => 'In development',
		'soon'        => 'Coming soon',
	];
}

/**
 * Returns an app's details with validated suite, status and accent.
 *
 * @param int $id App post ID.
 * @return array
 */
function drift_app_meta( int $id ): array {
	$status = (string) get_post_meta( $id, 'drift_status', true );
	$status = isset( drift_app_statuses()[ $status ] ) ? $status : 'soon';
	$suite  = (string) get_post_meta( $id, 'drift_suite', true );
	$suite  = isset( drift_suites()[ $suite ] ) ? $suite : 'music';
	$custom = sanitize_hex_color( (string) get_post_meta( $id, 'drift_accent', true ) );
	return [
		'suite'       => $suite,
		'suite_label' => drift_suites()[ $suite ]['label'],
		'tagline'     => (string) get_post_meta( $id, 'drift_tagline', true ),
		'status'      => $status,
		'label'       => drift_app_statuses()[ $status ],
		'emoji'       => (string) get_post_meta( $id, 'drift_emoji', true ),
		'accent'      => $custom ?: drift_suites()[ $suite ]['accent'],
		'demo'        => (string) get_post_meta( $id, 'drift_demo', true ),
	];
}

/**
 * All published apps in card order (Page Attributes → Order, then title).
 *
 * @return WP_Post[]
 */
function drift_apps(): array {
	return get_posts( [
		'post_type'      => 'drift_app',
		'posts_per_page' => -1,
		'orderby'        => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
		'no_found_rows'  => true,
	] );
}

/**
 * Whether an app's status means it can be seen today (live or demo).
 *
 * @param string $status Status key.
 * @return bool
 */
function drift_app_is_available( string $status ): bool {
	return in_array( $status, [ 'live', 'demo' ], true );
}
