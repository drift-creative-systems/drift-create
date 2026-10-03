<?php
/**
 * Home: built from the front page's modules (Pages → Home → Page modules).
 *
 * Fallback when ACF Pro is off or the page has no modules: a simple hero,
 * the page content, the apps by suite and the contact form, so the home page
 * is never empty.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;
get_header();

$front_id = is_page() ? (int) get_queried_object_id() : 0;
$layouts  = function_exists( 'have_rows' ) ? drift_page_layouts( $front_id ) : [];

// No hero first? Keep a (visually hidden) <h1> for the page.
if ( $layouts && 'hero' !== $layouts[0] ) {
	drift_h1_used( true );
	echo '<h1 class="sr">' . esc_html( drift_brand_name() ) . '</h1>';
}

if ( ! drift_render_modules( $front_id ) ) :
	drift_h1_used( true );
	?>
	<section class="hero">
		<div class="wrap hero__grid">
			<div>
				<p class="eyebrow"><?php echo esc_html( drift_brand_name() ); ?></p>
				<h1 class="hero__title">Systems for creative work.</h1>
				<p class="btns">
					<a class="btn btn--dark btn--big" href="#apps">See the apps</a>
					<a class="btn btn--line btn--big" href="#contact">Book a demo</a>
				</p>
			</div>
			<div class="hero__art" aria-hidden="true"><?php echo drift_mark( 'hero__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?></div>
		</div>
	</section>
	<?php
	if ( $front_id && '' !== trim( (string) get_post_field( 'post_content', $front_id ) ) ) :
		?>
		<div class="wrap wrap--text prose"><?php echo apply_filters( 'the_content', get_post_field( 'post_content', $front_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core content filter. ?></div>
		<?php
	endif;

	get_template_part( 'parts/app-suites', null, [ 'section_id' => 'apps' ] );
	drift_contact_form();
endif;

get_footer();
