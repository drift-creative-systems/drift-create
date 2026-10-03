<?php
/**
 * Module: App suites — the Apps grouped by suite as cards. Cards come from
 * the Apps post type; this module only chooses suites, their headings and
 * the featured app. Markup lives in parts/app-suites.php.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

get_template_part( 'parts/app-suites', null, [
	'section_id' => (string) get_sub_field( 'section_id' ),
	'suites'     => get_sub_field( 'suites' ) ?: [],
	'featured'   => (int) get_sub_field( 'featured_app' ),
] );
