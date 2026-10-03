<?php
/**
 * Module: Split text — big heading on the left, rich text on the right
 * (the original "About" section).
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$heading = (string) get_sub_field( 'heading' );
$body    = (string) get_sub_field( 'body' );
if ( '' === $heading && '' === $body ) {
	return;
}
?>
<section<?php drift_section_id(); ?> class="section split">
	<div class="wrap split__grid">
		<?php if ( $heading ) : ?>
			<h2 class="h2"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( $body ) : ?>
			<div class="lead"><?php echo wp_kses_post( $body ); ?></div>
		<?php endif; ?>
	</div>
</section>
