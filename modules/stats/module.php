<?php
/**
 * Module: Stats — a row of big numbers. "Counted" stats come from the Apps
 * post type so they never go stale; extra stats are typed in. Prints
 * nothing when there are no stats.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$apps  = drift_apps();
$metas = array_map( static fn( $app ) => drift_app_meta( $app->ID ), $apps );

// Counted stats: key → [ value, singular label, plural label ].
$counted = [
	'apps'      => [ count( $apps ), 'app', 'apps' ],
	'available' => [ count( array_filter( $metas, static fn( $m ) => drift_app_is_available( $m['status'] ) ) ), 'available now', 'available now' ],
	'suites'    => [ count( array_unique( wp_list_pluck( $metas, 'suite' ) ) ), 'suite', 'suites' ],
	'platforms' => [ count( array_unique( array_merge( [], ...wp_list_pluck( $metas, 'platforms' ) ) ) ), 'platform', 'platforms' ],
];

$stats = [];
foreach ( (array) get_sub_field( 'counted' ) as $key ) {
	if ( isset( $counted[ $key ] ) && $counted[ $key ][0] ) {
		[ $value, $one, $many ] = $counted[ $key ];
		$stats[] = [ 'value' => (string) $value, 'label' => 1 === $value ? $one : $many ];
	}
}
foreach ( (array) get_sub_field( 'stats' ) as $row ) {
	if ( ! empty( $row['value'] ) ) {
		$stats[] = [ 'value' => (string) $row['value'], 'label' => (string) ( $row['label'] ?? '' ) ];
	}
}
if ( ! $stats ) {
	return;
}

$heading = (string) get_sub_field( 'heading' );
?>
<section<?php drift_section_id(); ?> class="section stats-block" style="<?php echo drift_accent_style( (string) get_sub_field( 'accent_suite' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
	<div class="wrap">
		<?php if ( $heading ) : ?><h2 class="h3 stats-block__heading"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
		<ul class="stats">
			<?php foreach ( $stats as $stat ) : ?>
				<li class="stat">
					<span class="stat__value"><?php echo esc_html( $stat['value'] ); ?></span>
					<?php if ( $stat['label'] ) : ?><span class="stat__label"><?php echo esc_html( $stat['label'] ); ?></span><?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
