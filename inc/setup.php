<?php
/**
 * Theme supports, menus and front-end assets.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', static function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'style', 'script', 'search-form' ] );
	register_nav_menus( [
		'primary' => 'Main menu',
		'legal'   => 'Footer legal links',
	] );
} );

// Classic editor everywhere: pages are built with ACF modules, not blocks.
add_filter( 'use_block_editor_for_post_type', '__return_false' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Returns a theme file's modified time as an asset version string.
 *
 * @param string $file Path relative to the theme root.
 * @return string
 */
function drift_asset_version( string $file ): string {
	$path = get_theme_file_path( $file );
	return file_exists( $path ) ? (string) filemtime( $path ) : DRIFT_CREATE_VERSION;
}

/*
 * Apply the visitor's saved light / dark choice before first paint. Inline on
 * purpose: the deferred site.js runs too late and the page would flash the
 * other theme. The toggle itself lives in assets/site.js.
 */
add_action( 'wp_head', static function () {
	wp_print_inline_script_tag( "(function(){try{var t=localStorage.getItem('drift-theme');if(t==='dark'||t==='light'){document.documentElement.setAttribute('data-theme',t);}}catch(e){}})();" );
}, 0 );

add_action( 'wp_enqueue_scripts', static function () {
	wp_enqueue_style( 'drift-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@400;500;600;700&display=swap', [], null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'drift-site', get_theme_file_uri( 'assets/site.css' ), [ 'drift-fonts' ], drift_asset_version( 'assets/site.css' ) );
	wp_enqueue_script( 'drift-site', get_theme_file_uri( 'assets/site.js' ), [ 'jquery' ], drift_asset_version( 'assets/site.js' ), [ 'in_footer' => true, 'strategy' => 'defer' ] );
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );

	// Module styles load only on pages that use the module (inc/modules.php).
	foreach ( drift_layouts_for_request() as $layout ) {
		drift_enqueue_module_style( $layout );
	}
}, 20 );
