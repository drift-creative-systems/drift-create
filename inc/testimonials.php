<?php
/**
 * Testimonials post type and helpers.
 *
 * Admin-only (no public URLs); shown through the Testimonials page module.
 * Title = person's name, featured image = square headshot (optional), Page Attributes →
 * Order = display order. Quote, role and company are the ACF "Testimonial
 * details" field group (acf-json/group_drift_testimonial_details.json), read
 * with get_post_meta() so they work with or without ACF active.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', static function () {
	register_post_type( 'drift_testimonial', [
		'labels'              => [
			'name'               => 'Testimonials',
			'singular_name'      => 'Testimonial',
			'add_new_item'       => 'Add testimonial',
			'edit_item'          => 'Edit testimonial',
			'featured_image'     => 'Photo',
			'set_featured_image' => 'Set photo',
		],
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'menu_icon'           => 'dashicons-format-quote',
		'menu_position'       => 22,
		'supports'            => [ 'title', 'thumbnail', 'page-attributes' ],
	] );
} );

/* "Name" instead of "Add title" on the edit screen. */
add_filter( 'enter_title_here', static fn( string $text, WP_Post $post ): string => 'drift_testimonial' === $post->post_type ? 'Name' : $text, 10, 2 );

/**
 * Returns a testimonial's details.
 *
 * @param int $id Testimonial post ID.
 * @return array{quote: string, role: string, company: string}
 */
function drift_testimonial_meta( int $id ): array {
	return [
		'quote'   => (string) get_post_meta( $id, 'drift_quote', true ),
		'role'    => (string) get_post_meta( $id, 'drift_role', true ),
		'company' => (string) get_post_meta( $id, 'drift_company', true ),
	];
}

/**
 * Published testimonials in display order (Page Attributes → Order, then newest).
 *
 * @param int[] $ids   Specific testimonials, in this order. Empty = all.
 * @param int   $limit Maximum number when showing all. 0 = no limit.
 * @return WP_Post[]
 */
function drift_testimonials( array $ids = [], int $limit = 0 ): array {
	$ids  = array_filter( array_map( 'absint', $ids ) );
	$args = [
		'post_type'      => 'drift_testimonial',
		'posts_per_page' => ( $ids || $limit < 1 ) ? -1 : $limit,
		'no_found_rows'  => true,
		'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
	];
	if ( $ids ) {
		$args['post__in'] = $ids;
		$args['orderby']  = 'post__in';
	}
	return get_posts( $args );
}
