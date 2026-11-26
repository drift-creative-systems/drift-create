<?php
/**
 * Module: Logos — "who we work with" row of labels, artists, agencies.
 * Up to 5 logos sit in one static row; 6 or more become a slider (5 per view
 * on desktop, fewer on smaller screens) with prev / next buttons from
 * assets/site.js. Without JS the track still scrolls sideways.
 * Logos without an image are skipped.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$logos = [];
foreach ( (array) get_sub_field( 'logos' ) as $row ) {
	$image_id = (int) ( $row['image'] ?? 0 );
	if ( $image_id ) {
		$logos[] = [
			'image' => $image_id,
			'dark'  => (int) ( $row['image_dark'] ?? 0 ),
			'name'  => trim( (string) ( $row['name'] ?? '' ) ),
			'url'   => trim( (string) ( $row['url'] ?? '' ) ),
		];
	}
}
if ( ! $logos ) {
	return;
}
$eyebrow   = (string) get_sub_field( 'eyebrow' );
$heading   = (string) get_sub_field( 'heading' );
$lead      = (string) get_sub_field( 'lead' );
$is_slider = count( $logos ) > 5;
// Default on: the field only exists once ACF Sync has run.
$mono      = null === get_sub_field( 'mono' ) ? true : (bool) get_sub_field( 'mono' );
$label     = $heading ? $heading : ( $eyebrow ? $eyebrow : __( 'Logos', 'drift-create' ) );
$classes   = 'logos__track' . ( $is_slider ? ' logos__track--slider' : ' logos__track--' . count( $logos ) );
?>
<section<?php drift_section_id(); ?> class="section logos<?php echo $mono ? ' logos--mono' : ''; ?>" style="<?php echo drift_accent_style( (string) get_sub_field( 'accent_suite' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
	<div class="wrap">
		<?php if ( $eyebrow || $heading || $lead || $is_slider ) : ?>
			<div class="logos__top">
				<?php if ( $eyebrow || $heading || $lead ) : ?>
					<header class="suite__head">
						<?php if ( $eyebrow ) : ?><p class="eyebrow eyebrow--accent"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
						<?php if ( $heading ) : ?><h2 class="h2"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
						<?php if ( $lead ) : ?><p class="lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
					</header>
				<?php endif; ?>
				<?php if ( $is_slider ) : ?>
					<div class="logos__nav" hidden>
						<button type="button" class="logos__btn" data-dir="-1" aria-label="<?php esc_attr_e( 'Previous logos', 'drift-create' ); ?>">
							<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</button>
						<button type="button" class="logos__btn" data-dir="1" aria-label="<?php esc_attr_e( 'Next logos', 'drift-create' ); ?>">
							<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</button>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( $is_slider ) : ?>
			<?php /* Focusable scroll region so keyboard users can scroll it with the arrow keys. */ ?>
			<div class="logos__viewport" tabindex="0" role="region" aria-label="<?php echo esc_attr( $label ); ?>">
		<?php endif; ?>
		<ul class="<?php echo esc_attr( $classes ); ?>">
			<?php
			foreach ( $logos as $logo ) :
				// The name is the alt text; fall back to the image's own alt.
				$alt  = $logo['name'] ? $logo['name'] : trim( (string) get_post_meta( $logo['image'], '_wp_attachment_image_alt', true ) );
				$img  = wp_get_attachment_image( $logo['image'], 'medium', false, [ 'class' => 'logos__img' . ( $logo['dark'] ? ' light-only' : '' ), 'alt' => $alt, 'loading' => 'lazy' ] );
				$img .= $logo['dark'] ? wp_get_attachment_image( $logo['dark'], 'medium', false, [ 'class' => 'logos__img dark-only', 'alt' => $alt, 'loading' => 'lazy' ] ) : '';
				if ( ! $img ) {
					continue;
				}
				?>
				<li class="logos__item">
					<?php if ( $logo['url'] ) : ?>
						<a class="logos__tile" href="<?php echo esc_url( $logo['url'] ); ?>" target="_blank" rel="noopener">
							<?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core image markup. ?>
							<span class="sr"><?php esc_html_e( '(opens in a new tab)', 'drift-create' ); ?></span>
						</a>
					<?php else : ?>
						<span class="logos__tile"><?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core image markup. ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( $is_slider ) : ?>
			</div>
		<?php endif; ?>
	</div>
</section>
