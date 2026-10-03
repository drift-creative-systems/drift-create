<?php
/**
 * Module: CTA band — black band with heading, short text and buttons.
 * The accent (suite colour) tints the rule and any "Accent" buttons.
 * Use the "Accent" or "Outline (light)" button styles on black.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$heading = (string) get_sub_field( 'heading' );
if ( '' === $heading ) {
	return;
}
$eyebrow = (string) get_sub_field( 'eyebrow' );
$text    = (string) get_sub_field( 'text' );
?>
<section<?php drift_section_id(); ?> class="section cta" style="<?php echo drift_accent_style( (string) get_sub_field( 'accent_suite' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
	<div class="wrap cta__inner">
		<div>
			<?php if ( $eyebrow ) : ?><p class="eyebrow eyebrow--accent"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
			<h2 class="h2"><?php echo esc_html( $heading ); ?></h2>
			<?php if ( $text ) : ?><p class="lead"><?php echo esc_html( $text ); ?></p><?php endif; ?>
		</div>
		<?php drift_buttons( get_sub_field( 'buttons' ), true ); ?>
	</div>
</section>
