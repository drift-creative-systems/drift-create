<?php
/**
 * Module: Hero — eyebrow, big title, lead, buttons, optional logo art.
 * The first hero on a page uses the <h1>.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$title = (string) get_sub_field( 'title' );
if ( '' === $title ) {
	return;
}
$eyebrow   = (string) ( get_sub_field( 'eyebrow' ) ?: drift_brand_name() );
$lead      = (string) get_sub_field( 'lead' );
$show_mark = (bool) get_sub_field( 'show_mark' );
$tag       = drift_h1_used( true ) ? 'h2' : 'h1';
?>
<section<?php drift_section_id(); ?> class="hero">
	<div class="wrap<?php echo $show_mark ? ' hero__grid' : ''; ?>">
		<div>
			<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<<?php echo tag_escape( $tag ); ?> class="hero__title"><?php echo esc_html( $title ); ?></<?php echo tag_escape( $tag ); ?>>
			<?php if ( $lead ) : ?>
				<p class="hero__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
			<?php drift_buttons( get_sub_field( 'buttons' ), true ); ?>
		</div>
		<?php if ( $show_mark ) : ?>
			<div class="hero__art" aria-hidden="true"><?php echo drift_logo( 'hero_logo', 'hero__logo', true ) ?: drift_mark( 'hero__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() or static SVG. ?></div>
		<?php endif; ?>
	</div>
</section>
