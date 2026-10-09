<?php
/**
 * Module: Cards — a grid of cards under an optional heading. Either manual
 * cards (icon image, optional title, rich text, optional link) or chosen Apps,
 * which use the same app card as the App suites module (parts/app-card.php).
 *
 * A linked card is clickable all over: the stretched link sits on the title,
 * or on the "more" line when the card has no title. Links inside the text
 * stay clickable above it.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$source  = 'apps' === get_sub_field( 'source' ) ? 'apps' : 'manual';
$columns = in_array( (string) get_sub_field( 'columns' ), [ '2', '3', '4' ], true ) ? (string) get_sub_field( 'columns' ) : '3';

if ( 'apps' === $source ) {
	// Chosen apps in their chosen order; none chosen = every published app.
	$ids   = array_filter( array_map( 'absint', (array) get_sub_field( 'apps' ) ) );
	$items = $ids ? array_values( array_filter( array_map( 'get_post', $ids ), static fn( $p ) => $p instanceof WP_Post && 'publish' === $p->post_status ) ) : drift_apps();
	if ( is_singular( 'drift_app' ) ) {
		// Don't list the app you're already on.
		$items = array_values( array_filter( $items, static fn( $p ) => $p->ID !== get_queried_object_id() ) );
	}
} else {
	// A card needs a title or some text; empty rows are skipped.
	$items = array_values( array_filter( (array) get_sub_field( 'cards' ), static fn( $card ) => is_array( $card ) && ( '' !== trim( (string) ( $card['title'] ?? '' ) ) || '' !== trim( wp_strip_all_tags( (string) ( $card['text'] ?? '' ) ) ) ) ) );
}
if ( ! $items ) {
	return;
}

$eyebrow = (string) get_sub_field( 'eyebrow' );
$heading = (string) get_sub_field( 'heading' );
$lead    = (string) get_sub_field( 'lead' );
?>
<section<?php drift_section_id(); ?> class="section cards-block" style="<?php echo drift_accent_style( (string) get_sub_field( 'accent_suite' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
	<div class="wrap">
		<?php if ( $eyebrow || $heading || $lead ) : ?>
			<header class="suite__head">
				<?php if ( $eyebrow ) : ?><p class="eyebrow eyebrow--accent"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
				<?php if ( $heading ) : ?><h2 class="h2"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
				<?php if ( $lead ) : ?><p class="lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
			</header>
		<?php endif; ?>
		<div class="cards cards--<?php echo esc_attr( $columns ); ?>">
			<?php
			foreach ( $items as $item ) :
				if ( 'apps' === $source ) {
					get_template_part( 'parts/app-card', null, [ 'post' => $item ] );
					continue;
				}
				$link   = is_array( $item['link'] ?? null ) && ! empty( $item['link']['url'] ) ? $item['link'] : null;
				$icon   = (int) ( $item['image'] ?? 0 );
				$title  = trim( (string) ( $item['title'] ?? '' ) );
				$text   = (string) ( $item['text'] ?? '' );
				$more   = $link ? (string) ( $link['title'] ?: 'Find out more' ) : '';
				$target = $link && ! empty( $link['target'] ) ? ' target="_blank" rel="noopener"' : '';
				?>
				<article class="card<?php echo $link ? '' : ' card--static'; ?>">
					<?php if ( $icon ) : ?>
						<div class="card__top"><span class="card__icon card__icon--image" aria-hidden="true"><?php echo wp_get_attachment_image( $icon, 'thumbnail', false, [ 'alt' => '', 'loading' => 'lazy' ] ); ?></span></div>
					<?php endif; ?>
					<?php if ( $title ) : ?>
						<h3 class="card__title">
							<?php if ( $link ) : ?>
								<a href="<?php echo esc_url( $link['url'] ); ?>"<?php echo $target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static string. ?>><?php echo esc_html( $title ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $title ); ?>
							<?php endif; ?>
						</h3>
					<?php endif; ?>
					<?php if ( $text ) : ?><div class="card__text prose--flush"><?php echo wp_kses_post( $text ); ?></div><?php endif; ?>
					<?php if ( $link && $title ) : ?>
						<p class="card__more" aria-hidden="true"><?php echo esc_html( $more ); ?> &rarr;</p>
					<?php elseif ( $link ) : ?>
						<?php // No title: the "more" line is the real (stretched) link. ?>
						<p class="card__more"><a class="card__link" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo $target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static string. ?>><?php echo esc_html( $more ); ?> &rarr;</a></p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
		<?php drift_buttons( get_sub_field( 'buttons' ) ); ?>
	</div>
</section>
