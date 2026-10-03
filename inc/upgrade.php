<?php
/**
 * First-run seeding and version upgrades.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

/* Seed apps and a Home page on first activation. */
add_action( 'after_switch_theme', static function () {
	if ( get_option( 'drift_create_seeded' ) ) {
		return;
	}
	require_once get_theme_file_path( 'inc/seed.php' );
	drift_create_seed();
	update_option( 'drift_create_seeded', DRIFT_CREATE_VERSION );
	update_option( 'drift_create_version', DRIFT_CREATE_VERSION );
	flush_rewrite_rules();
} );

/* 0.1 → 0.2: apps get a suite; old per-app colours give way to the suite accent. */
add_action( 'init', static function () {
	if ( version_compare( (string) get_option( 'drift_create_version', '0.1.0' ), '0.2.0', '>=' ) ) {
		return;
	}
	foreach ( get_posts( [ 'post_type' => 'drift_app', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' ] ) as $id ) {
		if ( ! get_post_meta( $id, 'drift_suite', true ) ) {
			update_post_meta( $id, 'drift_suite', 'music' );
		}
		delete_post_meta( $id, 'drift_accent' );
	}
	update_option( 'drift_create_version', '0.2.0' );
}, 20 );

/*
 * 0.2 → 0.3: the home page moves to modules. Once ACF Pro is active, give the
 * front page the modules that match the old hardcoded layout — unless it
 * already has some. Runs once; safe on fresh installs too.
 */
add_action( 'init', static function () {
	if ( get_option( 'drift_create_modules_seeded' ) || ! function_exists( 'update_field' ) ) {
		return;
	}
	$front = (int) get_option( 'page_on_front' );
	if ( 'page' === get_option( 'show_on_front' ) && $front && ! get_post_meta( $front, 'page_modules', true ) ) {
		require_once get_theme_file_path( 'inc/seed.php' );
		drift_create_seed_home_modules( $front );
	}
	update_option( 'drift_create_modules_seeded', DRIFT_CREATE_VERSION );
	update_option( 'drift_create_version', DRIFT_CREATE_VERSION );
}, 100 );
