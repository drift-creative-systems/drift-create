<?php
/**
 * ACF Flexible Content page builder ("Page modules").
 *
 * Each layout has a folder: modules/{layout_name}/module.php (markup) and an
 * optional module.css that is enqueued only on pages using that layout.
 * Adding a module: create the layout in ACF, add its folder, add its name to
 * drift_module_layouts() and the switch in drift_render_modules().
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registered module layout names (also their folder names).
 *
 * @return string[]
 */
function drift_module_layouts(): array {
	return [ 'hero', 'content', 'app_suites', 'spotlight', 'split_text', 'image_text', 'faq', 'cta', 'contact' ];
}

/**
 * Layout names used on a post, read straight from the flexible content meta
 * (ACF stores the ordered layout names there), so it's cheap to call in
 * wp_enqueue_scripts before the loop.
 *
 * @param int $post_id Post ID.
 * @return string[]
 */
function drift_page_layouts( int $post_id ): array {
	$layouts = $post_id ? get_post_meta( $post_id, 'page_modules', true ) : [];
	// Keeps the page's order, drops anything that isn't a registered module.
	return is_array( $layouts ) ? array_values( array_intersect( $layouts, drift_module_layouts() ) ) : [];
}

/**
 * Layouts whose CSS the current request needs.
 *
 * @return string[]
 */
function drift_layouts_for_request(): array {
	$layouts = is_singular() ? drift_page_layouts( (int) get_queried_object_id() ) : [];
	// 404, app pages (.hero__lead) and the module-less front page fallback use hero styles.
	if ( is_404() || is_singular( 'drift_app' ) || ( is_front_page() && ! $layouts ) ) {
		$layouts[] = 'hero';
	}
	return array_unique( $layouts );
}

/**
 * Enqueues modules/{layout}/module.css if the module has one.
 *
 * @param string $layout Layout name.
 * @return void
 */
function drift_enqueue_module_style( string $layout ): void {
	$file = 'modules/' . $layout . '/module.css';
	if ( ! in_array( $layout, drift_module_layouts(), true ) || ! file_exists( get_theme_file_path( $file ) ) ) {
		return;
	}
	wp_enqueue_style( 'drift-module-' . $layout, get_theme_file_uri( $file ), [ 'drift-site' ], drift_asset_version( $file ) );
}

/**
 * Renders a post's page modules.
 *
 * @param int $post_id Post ID.
 * @return bool False when ACF is inactive or the post has no modules, so the
 *              template can show its fallback.
 */
function drift_render_modules( int $post_id ): bool {
	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'page_modules', $post_id ) ) {
		return false;
	}
	while ( have_rows( 'page_modules', $post_id ) ) {
		the_row();
		$layout = get_row_layout();
		switch ( $layout ) {
			case 'hero':
			case 'content':
			case 'app_suites':
			case 'spotlight':
			case 'split_text':
			case 'image_text':
			case 'faq':
			case 'cta':
			case 'contact':
				get_template_part( 'modules/' . $layout . '/module' );
				break;
			default:
				// Unknown layout — log it, don't break the page.
				error_log( 'Drift: unknown page module layout "' . $layout . '" on post ' . $post_id ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}
	return true;
}

/**
 * Whether a layout prints the page's <h1> when it's the first module, so
 * templates know not to add their own title.
 *
 * @param string $layout Layout name.
 * @return bool
 */
function drift_layout_has_h1( string $layout ): bool {
	return in_array( $layout, [ 'hero', 'content' ], true );
}

/**
 * Tracks whether the page already has its <h1>. The first hero module gets
 * the h1; templates that print their own title call drift_h1_used( true ).
 *
 * @param bool $set Mark the h1 as used.
 * @return bool Whether it was already used before this call.
 */
function drift_h1_used( bool $set = false ): bool {
	static $used = false;
	$was = $used;
	if ( $set ) {
		$used = true;
	}
	return $was;
}

/**
 * Echoes an id attribute from the module's "Anchor ID" field (for #links).
 *
 * @param string $fallback ID to use when the field is empty.
 * @return void
 */
function drift_section_id( string $fallback = '' ): void {
	$id = sanitize_title( (string) ( get_sub_field( 'section_id' ) ?: $fallback ) );
	if ( $id ) {
		echo ' id="' . esc_attr( $id ) . '"';
	}
}

/**
 * Inline style setting the --app accent from a suite key.
 *
 * @param string $suite Suite key (music, agency, seo).
 * @return string Escaped style attribute value.
 */
function drift_accent_style( string $suite ): string {
	$suites = drift_suites();
	$accent = isset( $suites[ $suite ] ) ? $suites[ $suite ]['accent'] : $suites['music']['accent'];
	return esc_attr( '--app: ' . $accent );
}

/**
 * Outputs a "buttons" repeater (link + style) as a .btns row.
 *
 * @param mixed $buttons Repeater value: [ [ 'link' => array, 'style' => string ], ... ].
 * @param bool  $big     Use the large button size.
 * @return void
 */
function drift_buttons( $buttons, bool $big = false ): void {
	if ( empty( $buttons ) || ! is_array( $buttons ) ) {
		return;
	}
	$styles = [ 'dark', 'line', 'accent', 'line-light' ];
	$out    = '';
	foreach ( $buttons as $button ) {
		$link = $button['link'] ?? null;
		if ( empty( $link['url'] ) || empty( $link['title'] ) ) {
			continue;
		}
		$style = in_array( $button['style'] ?? '', $styles, true ) ? $button['style'] : 'dark';
		$out  .= sprintf(
			'<a class="btn btn--%1$s%2$s" href="%3$s"%4$s>%5$s</a>',
			esc_attr( $style ),
			$big ? ' btn--big' : '',
			esc_url( $link['url'] ),
			empty( $link['target'] ) ? '' : ' target="_blank" rel="noopener"',
			esc_html( $link['title'] )
		);
	}
	if ( $out ) {
		echo '<p class="btns">' . $out . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
	}
}
