<?php
/**
 * Module: Pricing table — plans side by side, each with a free-text price
 * ("£29", "From £499", "POA"), an optional period, a feature list (one per
 * line) and a button. A highlighted plan is shown as the black card with
 * an optional badge. The accent (suite colour) marks the feature ticks.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$plans = array_values( array_filter( (array) get_sub_field( 'plans' ), static fn( $plan ) => is_array( $plan ) && '' !== (string) ( $plan['name'] ?? '' ) ) );
if ( ! $plans ) {
	return;
}
$eyebrow = (string) get_sub_field( 'eyebrow' );
$heading = (string) get_sub_field( 'heading' );
$lead    = (string) get_sub_field( 'lead' );
$note    = (string) get_sub_field( 'note' );
?>
<section<?php drift_section_id(); ?> class="section pricing" style="<?php echo drift_accent_style( (string) get_sub_field( 'accent_suite' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
	<div class="wrap">
		<?php if ( $eyebrow || $heading || $lead ) : ?>
			<header class="suite__head">
				<?php if ( $eyebrow ) : ?><p class="eyebrow eyebrow--accent"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
				<?php if ( $heading ) : ?><h2 class="h2"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
				<?php if ( $lead ) : ?><p class="lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
			</header>
		<?php endif; ?>
		<div class="pricing__grid">
			<?php
			foreach ( $plans as $plan ) :
				$featured = ! empty( $plan['featured'] );
				$badge    = $featured ? (string) ( $plan['badge'] ?? '' ) : '';
				$price    = (string) ( $plan['price'] ?? '' );
				$period   = (string) ( $plan['period'] ?? '' );
				$desc     = (string) ( $plan['description'] ?? '' );
				// One feature per line; blank lines dropped.
				$features = array_filter( array_map( 'trim', preg_split( '/\R/', (string) ( $plan['features'] ?? '' ) ) ) );
				$link     = is_array( $plan['link'] ?? null ) && ! empty( $plan['link']['url'] ) && ! empty( $plan['link']['title'] ) ? $plan['link'] : null;
				?>
				<article class="plan<?php echo $featured ? ' plan--featured' : ''; ?>">
					<?php if ( $badge ) : ?><p class="plan__badge"><?php echo esc_html( $badge ); ?></p><?php endif; ?>
					<h3 class="plan__name"><?php echo esc_html( $plan['name'] ); ?></h3>
					<?php if ( $price ) : ?>
						<p class="plan__price">
							<span class="plan__amount"><?php echo esc_html( $price ); ?></span>
							<?php if ( $period ) : ?><span class="plan__period"><?php echo esc_html( $period ); ?></span><?php endif; ?>
						</p>
					<?php endif; ?>
					<?php if ( $desc ) : ?><p class="plan__desc"><?php echo esc_html( $desc ); ?></p><?php endif; ?>
					<?php if ( $features ) : ?>
						<ul class="plan__features">
							<?php foreach ( $features as $feature ) : ?>
								<li><?php echo esc_html( $feature ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php if ( $link ) : ?>
						<p class="plan__cta">
							<a class="btn <?php echo $featured ? 'btn--accent' : 'btn--dark'; ?>" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo empty( $link['target'] ) ? '' : ' target="_blank" rel="noopener"'; ?>><?php echo esc_html( $link['title'] ); ?></a>
						</p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
		<?php if ( $note ) : ?><p class="pricing__note"><?php echo esc_html( $note ); ?></p><?php endif; ?>
	</div>
</section>
