<?php
/**
 * Brand name, logo mark, favicons and document title.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

/**
 * Brand name second line, as on the primary logo (Drift Settings → Brand).
 * Falls back to the pre-0.3 Customiser value so existing sites keep theirs.
 *
 * @return string
 */
function drift_brand_line(): string {
	return (string) drift_option( 'brand_line', get_theme_mod( 'drift_brand_line', 'Creative Systems' ) );
}

/**
 * Full brand name, e.g. "Drift Creative Systems".
 *
 * @return string
 */
function drift_brand_name(): string {
	return 'Drift ' . drift_brand_line();
}

/**
 * The logo mark from assets/brand/primary-logo.svg, inline so it takes the
 * surrounding text colour (black on white, white on black).
 *
 * @param string $class CSS class for the svg.
 * @return string Static SVG markup.
 */
function drift_mark( string $class = 'mark' ): string {
	return '<svg class="' . esc_attr( $class ) . '" viewBox="80 40 180 160" aria-hidden="true" focusable="false"><path fill="currentColor" d="M80 40H180C220 40 260 80 260 120C260 160 220 200 180 200H80L130 150H180C196 150 210 136 210 120C210 104 196 90 180 90H80V40Z"/><path fill="currentColor" d="M90 170L150 110H210L150 170H90Z"/></svg>';
}

/**
 * An uploaded logo from Drift Settings → Brand (header_logo, header_logo_dark, footer_logo,
 * hero_logo). Empty alt: every spot it's used is already labelled (link
 * aria-label) or aria-hidden.
 *
 * @param string $name  Option field name.
 * @param string $class CSS class for the img.
 * @param bool   $eager Above the fold — skip lazy loading.
 * @return string Image markup, or '' when none is set (use the built-in logo).
 */
function drift_logo( string $name, string $class, bool $eager = false ): string {
	$image = (int) drift_option( $name, 0 );
	if ( ! $image ) {
		return '';
	}
	return (string) wp_get_attachment_image( $image, 'full', false, [
		'class'    => $class,
		'alt'      => '',
		'loading'  => $eager ? false : 'lazy',
		'decoding' => 'async',
	] );
}

/* Favicons: the brand favicon.svg, PNG fallbacks for Safari / iOS. */
add_action( 'wp_head', static function () {
	if ( has_site_icon() ) {
		return; // Someone set one in the Customiser — theirs wins.
	}
	$b = get_theme_file_uri( 'assets/brand/' );
	printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( $b . 'favicon.svg' ) );
	printf( '<link rel="icon" href="%s" sizes="32x32" type="image/png">' . "\n", esc_url( $b . 'favicon-32.png' ) );
	printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( $b . 'apple-touch-icon.png' ) );
}, 1 );

add_filter( 'document_title_parts', static function ( array $parts ): array {
	if ( is_front_page() ) {
		$parts['title'] = drift_brand_name();
		unset( $parts['tagline'] );
	} else {
		$parts['site'] = drift_brand_name();
	}
	return $parts;
} );
