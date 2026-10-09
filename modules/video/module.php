<?php
/**
 * Module: Video — a YouTube / Vimeo link or an uploaded video file, with an
 * optional heading, intro and caption.
 *
 * YouTube / Vimeo are click-to-play: the poster and play button show first
 * and the player (youtube-nocookie.com / Vimeo with dnt=1) only loads when
 * the visitor clicks (assets/site.js). Nothing is requested from YouTube or
 * Vimeo before that, which helps consent and page speed.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$source    = 'file' === get_sub_field( 'source' ) ? 'file' : 'embed';
$embed     = 'embed' === $source ? drift_video_embed_url( (string) get_sub_field( 'video_url' ) ) : '';
$file_id   = 'file' === $source ? (int) get_sub_field( 'video_file' ) : 0;
$file_url  = $file_id ? (string) wp_get_attachment_url( $file_id ) : '';
$file_type = $file_id ? (string) get_post_mime_type( $file_id ) : '';
if ( '' === $embed && '' === $file_url ) {
	return;
}

$eyebrow   = (string) get_sub_field( 'eyebrow' );
$heading   = (string) get_sub_field( 'heading' );
$lead      = (string) get_sub_field( 'lead' );
$caption   = (string) get_sub_field( 'caption' );
$poster_id = (int) get_sub_field( 'poster' );
$wide      = 'wide' === get_sub_field( 'width' );
// Accessible name for the play button / iframe.
$label = (string) ( get_sub_field( 'video_title' ) ?: ( $heading ?: 'Video' ) );
?>
<section<?php drift_section_id(); ?> class="section video" style="<?php echo drift_accent_style( (string) get_sub_field( 'accent_suite' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
	<div class="wrap<?php echo $wide ? '' : ' video__wrap--narrow'; ?>">
		<?php if ( $eyebrow || $heading || $lead ) : ?>
			<header class="suite__head">
				<?php if ( $eyebrow ) : ?><p class="eyebrow eyebrow--accent"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
				<?php if ( $heading ) : ?><h2 class="h2"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
				<?php if ( $lead ) : ?><p class="lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
			</header>
		<?php endif; ?>
		<figure class="video__figure">
			<div class="video__frame">
				<?php if ( $embed ) : ?>
					<button type="button" class="video__play" data-embed="<?php echo esc_url( $embed ); ?>" data-title="<?php echo esc_attr( $label ); ?>">
						<?php
						if ( $poster_id ) {
							echo wp_get_attachment_image( $poster_id, 'large', false, [ 'class' => 'video__poster', 'alt' => '', 'sizes' => '(max-width: 1200px) 100vw, 1200px' ] );
						}
						?>
						<span class="video__icon" aria-hidden="true"></span>
						<span class="sr">Play video: <?php echo esc_html( $label ); ?></span>
					</button>
				<?php else : ?>
					<video class="video__file" controls playsinline preload="<?php echo $poster_id ? 'none' : 'metadata'; ?>"<?php echo $poster_id ? ' poster="' . esc_url( (string) wp_get_attachment_image_url( $poster_id, 'large' ) ) . '"' : ''; ?> aria-label="<?php echo esc_attr( $label ); ?>">
						<source src="<?php echo esc_url( $file_url ); ?>"<?php echo $file_type ? ' type="' . esc_attr( $file_type ) . '"' : ''; ?>>
					</video>
				<?php endif; ?>
			</div>
			<?php if ( $caption ) : ?><figcaption class="video__caption"><?php echo esc_html( $caption ); ?></figcaption><?php endif; ?>
		</figure>
	</div>
</section>
