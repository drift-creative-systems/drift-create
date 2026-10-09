<?php
/**
 * Team post type and helpers.
 *
 * Team members are admin-only (no public URLs); they show through the Team
 * page module. Title = name, featured image = photo, Page Attributes →
 * Order = display order. Details are the ACF "Team details" field group
 * (acf-json/group_drift_team_details.json), read with get_post_meta() so
 * they work with or without ACF active.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', static function () {
	register_post_type( 'drift_team', [
		'labels'              => [
			'name'               => 'Team',
			'singular_name'      => 'Team member',
			'add_new_item'       => 'Add team member',
			'edit_item'          => 'Edit team member',
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
		'menu_icon'           => 'dashicons-groups',
		'menu_position'       => 21,
		'supports'            => [ 'title', 'thumbnail', 'page-attributes' ],
	] );
} );

/* "Name" instead of "Add title" on the edit screen. */
add_filter( 'enter_title_here', static fn( string $text, WP_Post $post ): string => 'drift_team' === $post->post_type ? 'Name' : $text, 10, 2 );

/**
 * Returns a team member's details with a validated suite and clean links.
 *
 * @param int $id Team member post ID.
 * @return array{role: string, bio: string, suite: string, suite_label: string, accent: string, links: array<int, array{url: string, title: string, target: string}>}
 */
function drift_team_meta( int $id ): array {
	$suites = drift_suites();
	$suite  = (string) get_post_meta( $id, 'drift_suite', true );
	$suite  = isset( $suites[ $suite ] ) ? $suite : '';

	// ACF stores repeater rows as drift_links (count) + drift_links_{i}_link.
	$links = [];
	$count = (int) get_post_meta( $id, 'drift_links', true );
	for ( $i = 0; $i < $count; $i++ ) {
		$link = get_post_meta( $id, 'drift_links_' . $i . '_link', true );
		if ( is_array( $link ) && ! empty( $link['url'] ) && ! empty( $link['title'] ) ) {
			$links[] = [
				'url'    => (string) $link['url'],
				'title'  => (string) $link['title'],
				'target' => (string) ( $link['target'] ?? '' ),
			];
		}
	}

	return [
		'role'        => (string) get_post_meta( $id, 'drift_role', true ),
		'bio'         => (string) get_post_meta( $id, 'drift_bio', true ),
		'suite'       => $suite,
		'suite_label' => $suite ? $suites[ $suite ]['label'] : '',
		// No suite = black, the brand's neutral.
		'accent'      => $suite ? $suites[ $suite ]['accent'] : 'var(--black)',
		'links'       => $links,
	];
}

/**
 * Published team members in display order (Page Attributes → Order, then name).
 *
 * @param int[]  $ids   Specific members to return, in this order. Empty = all.
 * @param string $suite Only members in this suite. Empty = any.
 * @return WP_Post[]
 */
function drift_team_members( array $ids = [], string $suite = '' ): array {
	$args = [
		'post_type'      => 'drift_team',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
		'orderby'        => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
	];
	$ids = array_filter( array_map( 'absint', $ids ) );
	if ( $ids ) {
		$args['post__in'] = $ids;
		$args['orderby']  = 'post__in';
	}
	if ( $suite && isset( drift_suites()[ $suite ] ) ) {
		$args['meta_query'] = [ [ 'key' => 'drift_suite', 'value' => $suite ] ]; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Small post type.
	}
	return get_posts( $args );
}
