<?php
/**
 * Module: Testimonials — quote cards from wp-admin → Testimonials. Shows all
 * (optionally the first N in order) or picked testimonials, in 1–3 columns.
 * Testimonials without a quote are skipped.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$items = [];
foreach ( drift_testimonials( (array) get_sub_field( 'testimonials' ), (int) get_sub_field( 'limit' ) ) as $post_item ) {
	$meta = drift_testimonial_meta( $post_item->ID );
	if ( '' !== trim( $meta['quote'] ) ) {
		$items[] = [ 'post' => $post_item, 'meta' => $meta ];
	}
}
if ( ! $items ) {
	return;
}
$columns = in_array( (string) get_sub_field( 'columns' ), [ '1', '2', '3' ], true ) ? (string) get_sub_field( 'columns' ) : '3';
$eyebrow = (string) get_sub_field( 'eyebrow' );
$heading = (string) get_sub_field( 'heading' );
$lead    = (string) get_sub_field( 'lead' );
?>
<section<?php drift_section_id(); ?> class="section testimonials" style="<?php echo drift_accent_style( (string) get_sub_field( 'accent_suite' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
	<div class="wrap">
		<?php if ( $eyebrow || $heading || $lead ) : ?>
			<header class="suite__head">
				<?php if ( $eyebrow ) : ?><p class="eyebrow eyebrow--accent"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
				<?php if ( $heading ) : ?><h2 class="h2"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
				<?php if ( $lead ) : ?><p class="lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
			</header>
		<?php endif; ?>
		<div class="testimonials__grid testimonials__grid--<?php echo esc_attr( $columns ); ?>">
			<?php
			foreach ( $items as $item ) :
				$m        = $item['meta'];
				$name     = $item['post']->post_title;
				$photo_id = (int) get_post_thumbnail_id( $item['post'] );
				$detail   = implode( ', ', array_filter( [ $m['role'], $m['company'] ] ) );
				?>
				<figure class="quote">
					<blockquote class="quote__text"><?php echo wp_kses_post( wpautop( $m['quote'] ) ); ?></blockquote>
					<figcaption class="quote__by">
						<?php
						if ( $photo_id ) {
							// Name is printed next to it, so the photo is decorative.
							echo wp_get_attachment_image( $photo_id, 'thumbnail', false, [ 'class' => 'quote__photo', 'alt' => '', 'loading' => 'lazy' ] );
						}
						?>
						<span>
							<?php if ( $name ) : ?><cite class="quote__name"><?php echo esc_html( $name ); ?></cite><?php endif; ?>
							<?php if ( $detail ) : ?><span class="quote__role"><?php echo esc_html( $detail ); ?></span><?php endif; ?>
						</span>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
