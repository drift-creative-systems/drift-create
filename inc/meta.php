<?php
/**
 * Meta description and Open Graph tags.
 *
 * Skipped when an SEO plugin is active, so the page never gets two sets.
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
