<?php
/**
 * Module: Roadmap — every app's roadmap items on one board (Now / Next /
 * Later + Recently shipped), each linking to that app's /roadmap/ page.
 * Items come from Apps → Roadmap. Suites: the ones picked here, else the
 * page suite, else all. Optional suite filter buttons. Markup lives in
 * parts/roadmap.php.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$suites = array_values( array_intersect( (array) get_sub_field( 'roadmap_suites' ), array_keys( drift_suites() ) ) );
if ( ! $suites && drift_page_suite() ) {
	$suites = [ drift_page_suite() ];
}
$items = drift_roadmap_items( $suites );
if ( ! $items ) {
	return;
}

$stages = (array) get_sub_field( 'stages' ) ?: [ 'now', 'next', 'later' ];
$count  = get_sub_field( 'shipped_count' );
$count  = null === $count || '' === $count ? 5 : (int) $count;

// Suite filter: only for suites that have items here.
$options = [];
if ( get_sub_field( 'suite_filter' ) ) {
	$present = array_unique( wp_list_pluck( $items, 'suite' ) );
	foreach ( drift_suites() as $key => $suite ) {
		if ( in_array( $key, $present, true ) ) {
			$options[ $key ] = $suite['label'];
		}
	}
}

$eyebrow = (string) get_sub_field( 'eyebrow' );
$heading = (string) get_sub_field( 'heading' );
$lead    = (string) get_sub_field( 'lead' );
$button  = get_sub_field( 'button' );
?>
<section<?php drift_section_id( 'roadmap' ); ?> class="section roadmap-block"<?php echo count( $options ) > 1 ? ' data-filter-scope' : ''; ?>>
	<div class="wrap">
		<?php if ( $eyebrow || $heading || $lead ) : ?>
			<header class="suite__head">
				<?php if ( $eyebrow ) : ?><p class="eyebrow eyebrow--accent"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
				<?php if ( $heading ) : ?><h2 class="h2"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
				<?php if ( $lead ) : ?><p class="lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
			</header>
		<?php endif; ?>
		<?php get_template_part( 'parts/filter', null, [ 'options' => $options, 'label' => 'Filter roadmap by suite' ] ); ?>
		<?php
		get_template_part( 'parts/roadmap', null, [
			'items'    => $items,
			'stages'   => $stages,
			'shipped'  => $count,
			'show_app' => true,
		] );
		if ( is_array( $button ) ) {
			drift_buttons( [ [ 'link' => $button, 'style' => 'line' ] ] );
		}
		?>
	</div>
</section>
