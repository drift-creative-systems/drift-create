<?php
/**
 * Apps post type and helpers.
 *
 * App details (suite, tagline, status, avatar, accent override, demo link) are
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
 * Product suites → label, accent (Drift Brand System, "Product Accent
 * Examples") and a default tagline. Drift Settings → Suites can override the
 * tagline and set a landing page; read those with drift_suite_details().
 *
 * @return array<string, array{label: string, accent: string, tagline: string}>
 */
function drift_suites(): array {
	return [
		'music'  => [ 'label' => 'Music Suite', 'accent' => '#FF4FA3', 'tagline' => 'Apps for artists, venues and live events.' ],
		'agency' => [ 'label' => 'Agency Kit', 'accent' => '#1F7BFF', 'tagline' => 'Tools for agencies that build and look after client sites.' ],
		'seo'    => [ 'label' => 'SEO', 'accent' => '#57E35B', 'tagline' => '' ],
	];
}

/**
 * A suite with its Drift Settings → Suites overrides applied: label, accent,
 * tagline and the landing page URL ('' when none is set or it isn't published).
 *
 * @param string $key Suite key.
 * @return array{label: string, accent: string, tagline: string, url: string} Empty array for an unknown suite.
 */
function drift_suite_details( string $key ): array {
	static $settings = null;
	$suites = drift_suites();
	if ( ! isset( $suites[ $key ] ) ) {
		return [];
	}
	if ( null === $settings ) {
		$settings = [];
		foreach ( (array) drift_option( 'suite_details', [] ) as $row ) {
			if ( ! empty( $row['suite'] ) ) {
				$settings[ (string) $row['suite'] ] = $row;
			}
		}
	}
	$row     = $settings[ $key ] ?? [];
	$landing = (int) ( $row['landing_page'] ?? 0 );
	return [
		'label'   => $suites[ $key ]['label'],
		'accent'  => $suites[ $key ]['accent'],
		'tagline' => (string) ( ( $row['tagline'] ?? '' ) ?: $suites[ $key ]['tagline'] ),
		'url'     => $landing && 'publish' === get_post_status( $landing ) ? (string) get_permalink( $landing ) : '',
	];
}

/**
 * The current page's suite: Page settings → Page suite on pages, the app's
 * own suite on app pages, '' everywhere else. Tints the page (body class
 * page-suite-{key}) and is the default suite for suite-aware modules.
 *
 * @return string Suite key or ''.
 */
function drift_page_suite(): string {
	if ( is_singular( 'drift_app' ) ) {
		return drift_app_meta( (int) get_queried_object_id() )['suite'];
	}
	if ( ! is_singular( 'page' ) ) {
		return '';
	}
	$suite = (string) get_post_meta( (int) get_queried_object_id(), 'page_suite', true );
	return isset( drift_suites()[ $suite ] ) ? $suite : '';
}

add_filter( 'body_class', static function ( array $classes ): array {
	$suite = drift_page_suite();
	if ( $suite ) {
		$classes[] = 'page-suite-' . $suite;
	}
	return $classes;
} );

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
 * Platform → label. Shown as tags on cards and the app page, and as the App
 * suites platform filter. Keys are also the data-filter-tags values.
 *
 * @return array<string, string>
 */
function drift_platforms(): array {
	return [
		'wordpress'   => 'WordPress plugin',
		'self-hosted' => 'Self-hosted',
		'desktop'     => 'Desktop app',
		'web'         => 'Web app',
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
		'icon'        => (int) get_post_meta( $id, 'drift_icon', true ),
		'accent'      => $custom ?: drift_suites()[ $suite ]['accent'],
		'demo'        => (string) get_post_meta( $id, 'drift_demo', true ),
		// ACF stores the checkbox as an array; keep known platforms in drift_platforms() order.
		'platforms'   => array_values( array_intersect( array_keys( drift_platforms() ), (array) get_post_meta( $id, 'drift_platform', true ) ) ),
	];
}

/**
 * The app's platforms as a row of tags, or '' when none are set.
 *
 * @param string[] $platforms Platform keys from drift_app_meta().
 * @param string   $class     Wrapper class, e.g. "card__platforms".
 * @return string Tags HTML.
 */
function drift_platform_tags( array $platforms, string $class ): string {
	$labels = array_intersect_key( drift_platforms(), array_flip( $platforms ) );
	if ( ! $labels ) {
		return '';
	}
	$tags = array_map( static fn( $label ) => '<li class="tag">' . esc_html( $label ) . '</li>', $labels );
	return '<ul class="tags ' . esc_attr( $class ) . '" aria-label="Platforms">' . implode( '', $tags ) . '</ul>';
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

/**
 * The app's avatar tile: the uploaded avatar filling the tile, or the app's
 * initial when there's no image. Decorative (the app name is always next to
 * it), so it's hidden from screen readers.
 *
 * @param int    $id    App post ID.
 * @param string $class Tile class, e.g. "card__icon" or "app__icon".
 * @param bool   $eager Load the image straight away (above the fold).
 * @return string Tile HTML.
 */
function drift_app_icon( int $id, string $class, bool $eager = false ): string {
	$image = (int) get_post_meta( $id, 'drift_icon', true );
	if ( $image && wp_attachment_is_image( $image ) ) {
		return sprintf(
			'<span class="%1$s %1$s--image" aria-hidden="true">%2$s</span>',
			esc_attr( $class ),
			wp_get_attachment_image( $image, 'thumbnail', false, [ 'alt' => '', 'loading' => $eager ? false : 'lazy' ] )
		);
	}
	// "Drift: Encore" → "E".
	$name    = drift_app_short_name( $id );
	$initial = $name ? mb_strtoupper( mb_substr( $name, 0, 1 ) ) : '';
	return '<span class="' . esc_attr( $class ) . '" aria-hidden="true">' . esc_html( $initial ) . '</span>';
}

/**
 * The app's name without the brand prefix: "Drift: Encore" → "Encore". For
 * places that already say Drift (suite tiles, roadmap labels, icon initials).
 *
 * @param int $id App post ID.
 * @return string
 */
function drift_app_short_name( int $id ): string {
	return trim( (string) preg_replace( '/^.*:\s*/', '', get_the_title( $id ) ) );
}
