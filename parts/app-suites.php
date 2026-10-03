<?php
/**
 * Apps grouped by suite. Used by the app_suites module and the front page
 * fallback. Suites with no apps are skipped.
 *
 * $args['section_id'] string  Anchor ID (default "apps").
 * $args['suites']     array   Rows of [ suite, heading, lead ] in display order.
 * $args['featured']   int     App ID shown as the large black card.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$suites   = drift_suites();
$rows     = ! empty( $args['suites'] ) && is_array( $args['suites'] ) ? $args['suites'] : array_map( static fn( $key ) => [ 'suite' => $key ], array_keys( $suites ) );
$featured = (int) ( $args['featured'] ?? 0 );
$anchor   = sanitize_title( (string) ( $args['section_id'] ?? 'apps' ) );

$by_suite = [];
foreach ( drift_apps() as $app ) {
	$by_suite[ drift_app_meta( $app->ID )['suite'] ][] = $app;
}
?>
<section<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?> class="section">
	<div class="wrap">
		<?php
		foreach ( $rows as $row ) :
			$key = (string) ( $row['suite'] ?? '' );
			if ( ! isset( $suites[ $key ] ) || empty( $by_suite[ $key ] ) ) {
				continue;
			}
			?>
			<div class="suite" style="<?php echo drift_accent_style( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
				<header class="suite__head">
					<p class="eyebrow eyebrow--accent"><?php echo esc_html( $suites[ $key ]['label'] ); ?></p>
					<?php if ( ! empty( $row['heading'] ) ) : ?>
						<h2 class="h2"><?php echo esc_html( $row['heading'] ); ?></h2>
					<?php endif; ?>
					<?php if ( ! empty( $row['lead'] ) ) : ?>
						<p class="lead"><?php echo esc_html( $row['lead'] ); ?></p>
					<?php endif; ?>
				</header>
				<div class="cards">
					<?php
					foreach ( $by_suite[ $key ] as $app ) {
						get_template_part( 'parts/app-card', null, [ 'post' => $app, 'featured' => $featured === $app->ID ] );
					}
					?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
