<?php
/**
 * Meta description and Open Graph tags.
 *
 * Skipped when an SEO plugin is active, so the page never gets two sets.
 *
 * Also the Page settings → "Hide from search engines" toggle (page_noindex),
 * for pages sent by link, like proposals.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_head', static function () {
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return;
	}

	$default = (string) drift_option( 'default_meta_description', drift_brand_name() . ' — apps, websites and tools for musicians, venues, festivals and the agencies that build for them.' );
	$desc    = is_singular( 'drift_app' ) ? (string) get_post_meta( get_the_ID(), 'drift_tagline', true ) : '';
	$desc    = $desc ?: $default;

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( wp_get_document_title() ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:image" content="%s">' . "\n", esc_url( get_theme_file_uri( 'assets/brand/og-image.png' ) ) );
	echo '<meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	// Browser chrome: black on the light theme, the page colour on the dark one.
	echo '<meta name="theme-color" content="#000000" media="(prefers-color-scheme: light)">' . "\n";
	echo '<meta name="theme-color" content="#0b0c0d" media="(prefers-color-scheme: dark)">' . "\n";
}, 2 );

/**
 * Whether the current page has Page settings → "Hide from search engines" on.
 * Read from post meta so it still works without ACF.
 *
 * @return bool
 */
function drift_page_noindex(): bool {
	return is_singular( 'page' ) && (bool) get_post_meta( (int) get_queried_object_id(), 'page_noindex', true );
}

/**
 * IDs of published pages with "Hide from search engines" on, for sitemaps.
 *
 * @return int[]
 */
function drift_noindex_page_ids(): array {
	return array_map( 'absint', get_posts( [
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'posts_per_page' => 100,
		'no_found_rows'  => true,
		'meta_key'       => 'page_noindex', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Sitemap requests only.
		'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Sitemap requests only.
	] ) );
}

/* Core robots tag. Yoast and Rank Math replace it with their own, so they get their own filters below. */
add_filter( 'wp_robots', static function ( array $robots ): array {
	if ( drift_page_noindex() ) {
		unset( $robots['max-image-preview'] );
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
} );

add_filter( 'wpseo_robots', static function ( $robots ) {
	return drift_page_noindex() ? 'noindex, follow' : $robots;
} );

add_filter( 'rank_math/frontend/robots', static function ( $robots ) {
	if ( drift_page_noindex() && is_array( $robots ) ) {
		$robots['index'] = 'noindex';
	}
	return $robots;
} );

/* Keep hidden pages out of the core and Yoast sitemaps. */
add_filter( 'wp_sitemaps_posts_query_args', static function ( array $args, string $post_type ): array {
	if ( 'page' === $post_type ) {
		$args['post__not_in'] = array_merge( (array) ( $args['post__not_in'] ?? [] ), drift_noindex_page_ids() );
	}
	return $args;
}, 10, 2 );

add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', static function ( $ids ): array {
	return array_merge( (array) $ids, drift_noindex_page_ids() );
} );
