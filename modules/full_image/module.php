<?php
/**
 * Module: Full-width image — edge to edge or inside the page width, natural
 * ratio or cropped to a set height (with a focal point), optional caption
 * and optional overlay text + buttons on a dark gradient.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$image_id = (int) get_sub_field( 'image' );
if ( ! $image_id ) {
	return;
}
$width   = 'contained' === get_sub_field( 'width' ) ? 'contained' : 'full';
$height  = in_array( (string) get_sub_field( 'height' ), [ 'short', 'medium', 'tall' ], true ) ? (string) get_sub_field( 'height' ) : 'natural';
$focus   = in_array( (string) get_sub_field( 'focal_point' ), [ 'top', 'bottom', 'left', 'right' ], true ) ? (string) get_sub_field( 'focal_point' ) : 'center';
$caption = (string) get_sub_field( 'caption' );

$overlay  = (bool) get_sub_field( 'show_overlay' );
$eyebrow  = $overlay ? (string) get_sub_field( 'eyebrow' ) : '';
$heading  = $overlay ? (string) get_sub_field( 'heading' ) : '';
$text     = $overlay ? (string) get_sub_field( 'text' ) : '';
$buttons  = $overlay ? get_sub_field( 'buttons' ) : [];
$overlay  = $overlay && ( $eyebrow || $heading || $text || $buttons );
$position = in_array( (string) get_sub_field( 'overlay_position' ), [ 'bottom-left', 'center', 'top-left' ], true ) ? (string) get_sub_field( 'overlay_position' ) : 'bottom-left';
$strength = in_array( (string) get_sub_field( 'overlay_strength' ), [ 'light', 'medium', 'strong' ], true ) ? (string) get_sub_field( 'overlay_strength' ) : 'medium';

// With overlay text the image is decorative: the text carries the meaning.
$alt = $overlay ? '' : (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );

$classes = [ 'full-image', 'full-image--' . $width, 'full-image--h-' . $height, 'full-image--focus-' . $focus ];
if ( $overlay ) {
	$classes[] = 'full-image--overlay';
	$classes[] = 'full-image--' . $position;
	$classes[] = 'full-image--shade-' . $strength;
}
?>
<section<?php drift_section_id(); ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="<?php echo drift_accent_style( (string) get_sub_field( 'accent_suite' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
	<figure class="full-image__figure<?php echo 'contained' === $width ? ' wrap' : ''; ?>">
		<div class="full-image__media">
			<?php
			echo wp_get_attachment_image( $image_id, 'full', false, [
				'class' => 'full-image__img',
				'alt'   => $alt,
				'sizes' => 'contained' === $width ? '(max-width: 1200px) 100vw, 1200px' : '100vw',
			] );
			?>
			<?php if ( $overlay ) : ?>
				<div class="full-image__overlay">
					<div class="wrap full-image__copy">
						<?php if ( $eyebrow ) : ?><p class="eyebrow eyebrow--accent"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
						<?php if ( $heading ) : ?><h2 class="h2"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
						<?php if ( $text ) : ?><p class="lead"><?php echo esc_html( $text ); ?></p><?php endif; ?>
						<?php drift_buttons( $buttons, true ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( $caption ) : ?>
			<figcaption class="full-image__caption<?php echo 'full' === $width ? ' wrap' : ''; ?>"><?php echo esc_html( $caption ); ?></figcaption>
		<?php endif; ?>
	</figure>
</section>
