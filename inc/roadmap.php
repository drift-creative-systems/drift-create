<?php
/**
 * App roadmaps.
 *
 * Each app has a Roadmap repeater (acf-json/group_drift_app_roadmap.json):
 * item, stage, target, shipped date, note. Like the other app fields it's
 * read straight from post meta, so it works with or without ACF.
 *
 * Shown three ways, all through parts/roadmap.php:
 * - the #roadmap section on the app page (short version);
 * - /apps/{app}/roadmap/ — the app's full roadmap (rewrite endpoint);
 * - the Roadmap module — every app's items together, filterable by suite.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

/**
 * Stage → label, in board order. Shipped items are listed separately.
 *
 * @return array<string, string>
 */
function drift_roadmap_stages(): array {
	return [
		'now'     => 'Now',
		'next'    => 'Next',
		'later'   => 'Later',
		'shipped' => 'Shipped',
	];
}

/**
 * An app's roadmap items in editor order. Rows without text are skipped.
 *
 * @param int $id App post ID.
 * @return array<int, array{app: int, item: string, stage: string, target: string, shipped: string, note: string}>
 */
function drift_app_roadmap( int $id ): array {
	$count = (int) get_post_meta( $id, 'drift_roadmap', true );
	$items = [];
	for ( $i = 0; $i < $count; $i++ ) {
		// ACF repeater rows are stored as {repeater}_{row}_{sub field}.
		$prefix = 'drift_roadmap_' . $i . '_';
		$item   = trim( (string) get_post_meta( $id, $prefix . 'item', true ) );
		if ( '' === $item ) {
			continue;
		}
		$stage   = (string) get_post_meta( $id, $prefix . 'stage', true );
		$shipped = (string) get_post_meta( $id, $prefix . 'shipped', true );
		$items[] = [
			'app'     => $id,
			'item'    => $item,
			'stage'   => isset( drift_roadmap_stages()[ $stage ] ) ? $stage : 'later',
			'target'  => trim( (string) get_post_meta( $id, $prefix . 'target', true ) ),
			// ACF date pickers save Ymd.
			'shipped' => preg_match( '/^\d{8}$/', $shipped ) ? $shipped : '',
			'note'    => trim( (string) get_post_meta( $id, $prefix . 'note', true ) ),
		];
	}
	return $items;
}

/**
 * Roadmap items across apps, in app card order.
 *
 * @param string[] $suites Suite keys to include. Empty = every suite.
 * @return array Items as drift_app_roadmap(), plus 'suite'.
 */
function drift_roadmap_items( array $suites = [] ): array {
	$items = [];
	foreach ( drift_apps() as $app ) {
		$suite = drift_app_meta( $app->ID )['suite'];
		if ( $suites && ! in_array( $suite, $suites, true ) ) {
			continue;
		}
		foreach ( drift_app_roadmap( $app->ID ) as $item ) {
			$items[] = $item + [ 'suite' => $suite ];
		}
	}
	return $items;
}

/**
 * Splits items into board columns. Shipped items come newest first (undated
 * ones last); the others keep their order.
 *
 * @param array $items Items from drift_app_roadmap() / drift_roadmap_items().
 * @return array<string, array> Stage key → items.
 */
function drift_roadmap_by_stage( array $items ): array {
	$by_stage = array_fill_keys( array_keys( drift_roadmap_stages() ), [] );
	foreach ( $items as $item ) {
		$by_stage[ $item['stage'] ][] = $item;
	}
	usort( $by_stage['shipped'], static fn( $a, $b ) => strcmp( $b['shipped'], $a['shipped'] ) );
	return $by_stage;
}

/**
 * The app's full roadmap page.
 *
 * @param int $id App post ID.
 * @return string URL.
 */
function drift_app_roadmap_url( int $id ): string {
	return trailingslashit( (string) get_permalink( $id ) ) . user_trailingslashit( 'roadmap' );
}

/**
 * Whether this request is an app's /roadmap/ page.
 *
 * @return bool
 */
function drift_is_roadmap_view(): bool {
	global $wp_query;
	// Endpoints set their query var to '' when present, so test for the key.
	return is_singular( 'drift_app' ) && isset( $wp_query->query_vars['roadmap'] );
}

/* /apps/{app}/roadmap/. EP_PERMALINK is the default mask for the Apps post type. */
add_action( 'init', static function () {
	add_rewrite_endpoint( 'roadmap', EP_PERMALINK );
} );

/* The endpoint only means something on an app that has a roadmap; anywhere else it's a 404. */
add_action( 'template_redirect', static function () {
	global $wp_query;
	if ( ! isset( $wp_query->query_vars['roadmap'] ) || ( drift_is_roadmap_view() && drift_app_roadmap( (int) get_queried_object_id() ) ) ) {
		return;
	}
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
} );

/* The roadmap page is its own page for search engines: own canonical, "… roadmap" title. */
add_filter( 'get_canonical_url', static function ( string $url, WP_Post $post ): string {
	return drift_is_roadmap_view() && (int) get_queried_object_id() === $post->ID ? drift_app_roadmap_url( $post->ID ) : $url;
}, 10, 2 );

add_filter( 'document_title_parts', static function ( array $parts ): array {
	if ( drift_is_roadmap_view() ) {
		$parts['title'] .= ' roadmap';
	}
	return $parts;
}, 20 );

/* Keep near-empty roadmap pages out of search until they have enough to say. */
add_filter( 'wp_robots', static function ( array $robots ): array {
	if ( drift_is_roadmap_view() && count( drift_app_roadmap( (int) get_queried_object_id() ) ) < 5 ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
} );

add_filter( 'acf/load_field/key=field_drift_app_roadmap_stage', static function ( array $field ): array {
	$field['choices'] = drift_roadmap_stages();
	return $field;
} );
