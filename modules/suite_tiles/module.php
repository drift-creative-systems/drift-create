<?php
/**
 * Module: Suite tiles — one tile per suite in its colour, for the Drift-wide
 * home page. Tagline and link come from Drift Settings → Suites; the app
 * icons, names and counts come from the Apps post type. The editor only
 * picks which suites and in what order (no rows = every suite). Suites with
 * no apps are skipped.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$keys = array_filter( array_map( static fn( $row ) => (string) ( $row['suite'] ?? '' ), (array) get_sub_field( 'suites' ) ) );
if ( ! $keys ) {
	$keys = array_keys( drift_suites() );
}

$by_suite = [];
foreach ( drift_apps() as $app ) {
	$by_suite[ drift_app_meta( $app->ID )['suite'] ][] = $app;
}

$tiles = [];
foreach ( array_unique( $keys ) as $key ) {
	$suite = drift_suite_details( $key );
	if ( $suite && ! empty( $by_suite[ $key ] ) ) {
		$tiles[ $key ] = $suite;
	}
}
if ( ! $tiles ) {
	return;
}

$eyebrow = (string) get_sub_field( 'eyebrow' );
$heading = (string) get_sub_field( 'heading' );
$lead    = (string) get_sub_field( 'lead' );
?>
<section<?php drift_section_id( 'suites' ); ?> class="section suite-tiles">
	<div class="wrap">
		<?php if ( $eyebrow || $heading || $lead ) : ?>
			<header class="suite__head">
				<?php if ( $eyebrow ) : ?><p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
				<?php if ( $heading ) : ?><h2 class="h2"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
				<?php if ( $lead ) : ?><p class="lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
			</header>
		<?php endif; ?>
		<ul class="suite-tiles__grid suite-tiles__grid--<?php echo esc_attr( (string) min( 3, count( $tiles ) ) ); ?>">
			<?php
			foreach ( $tiles as $key => $suite ) :
				$apps      = $by_suite[ $key ];
				$available = count( array_filter( $apps, static fn( $app ) => drift_app_is_available( drift_app_meta( $app->ID )['status'] ) ) );
				$coming    = count( $apps ) - $available;
				$counts    = array_filter( [
					$available ? sprintf( '%d available', $available ) : '',
					$coming ? sprintf( '%d coming', $coming ) : '',
				] );
				?>
				<li class="suite-tile" style="<?php echo drift_accent_style( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
					<h3 class="suite-tile__title">
						<?php if ( $suite['url'] ) : ?>
							<a href="<?php echo esc_url( $suite['url'] ); ?>"><?php echo esc_html( $suite['label'] ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $suite['label'] ); ?>
						<?php endif; ?>
					</h3>
					<?php if ( $suite['tagline'] ) : ?><p class="suite-tile__tagline"><?php echo esc_html( $suite['tagline'] ); ?></p><?php endif; ?>
					<div class="suite-tile__icons" aria-hidden="true">
						<?php
						// Six icons fit on one row at the narrowest tile width.
						foreach ( array_slice( $apps, 0, 6 ) as $app ) {
							echo drift_app_icon( $app->ID, 'card__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
						}
						?>
					</div>
					<p class="suite-tile__apps"><?php echo esc_html( implode( ' · ', wp_list_pluck( $apps, 'post_title' ) ) ); ?></p>
					<p class="suite-tile__foot">
						<span class="suite-tile__count"><?php echo esc_html( implode( ' · ', $counts ) ); ?></span>
						<?php if ( $suite['url'] ) : ?><span class="suite-tile__more" aria-hidden="true">Explore &rarr;</span><?php endif; ?>
					</p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
