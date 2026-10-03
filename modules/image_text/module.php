<?php
/**
 * Module: Image + text — two columns, image left or right.
 * Alt text comes from the Media Library.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$image_id = (int) get_sub_field( 'image' );
$eyebrow  = (string) get_sub_field( 'eyebrow' );
$heading  = (string) get_sub_field( 'heading' );
$body     = (string) get_sub_field( 'body' );
$flip     = 'right' === get_sub_field( 'image_position' );
$soft     = (bool) get_sub_field( 'soft_background' );
if ( ! $image_id && '' === $heading && '' === $body ) {
	return;
}
$classes = 'section image-text' . ( $flip ? ' image-text--flip' : '' ) . ( $soft ? ' image-text--soft' : '' );
?>
<section<?php drift_section_id(); ?> class="<?php echo esc_attr( $classes ); ?>">
	<div class="wrap image-text__grid">
		<?php if ( $image_id ) : ?>
			<figure class="image-text__media">
				<?php echo wp_get_attachment_image( $image_id, 'large', false, [ 'class' => 'image-text__img', 'sizes' => '(max-width: 860px) 100vw, 600px' ] ); ?>
			</figure>
		<?php endif; ?>
		<div class="image-text__copy">
			<?php if ( $eyebrow ) : ?><p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
			<?php if ( $heading ) : ?><h2 class="h2"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
			<?php if ( $body ) : ?><div class="prose prose--flush"><?php echo wp_kses_post( $body ); ?></div><?php endif; ?>
			<?php drift_buttons( get_sub_field( 'buttons' ) ); ?>
		</div>
	</div>
</section>
