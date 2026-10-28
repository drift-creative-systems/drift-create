<?php
/**
 * Module: Table — a simple data table (price lists, comparisons, worked
 * examples) under an optional heading. The first column is the row label,
 * with an optional muted detail line under it; up to three value columns follow.
 * Columns with no heading and no values are left out. A highlighted row
 * (totals, a bundle price) is bold on the soft background with a
 * suite-accent edge.
 *
 * Two Table modules in a row sit closer together, so one heading can cover
 * a pair of tables.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$rows = array_values( array_filter( (array) get_sub_field( 'rows' ), static fn( $row ) => is_array( $row ) && '' !== trim( (string) ( $row['label'] ?? '' ) ) ) );
if ( ! $rows ) {
	return;
}

// Value columns 1–3: keep a column if it has a heading or any value.
$heads   = [];
$columns = [];
foreach ( [ 1, 2, 3 ] as $col ) {
	$head = (string) get_sub_field( 'head_' . $col );
	$used = '' !== $head;
	foreach ( $rows as $row ) {
		$used = $used || '' !== trim( (string) ( $row[ 'value_' . $col ] ?? '' ) );
	}
	if ( $used ) {
		$columns[]     = $col;
		$heads[ $col ] = $head;
	}
}
$label_head = (string) get_sub_field( 'head_label' );
$has_head   = '' !== $label_head || array_filter( $heads );

$eyebrow    = (string) get_sub_field( 'eyebrow' );
$heading    = (string) get_sub_field( 'heading' );
$lead       = (string) get_sub_field( 'lead' );
$note       = (string) get_sub_field( 'note' );
$heading_id = $heading ? wp_unique_id( 'table-heading-' ) : '';
// Scroll region name for keyboard and screen reader users on narrow screens.
$region = $heading ?: ( $label_head ?: __( 'Table', 'drift-create' ) );
?>
<section<?php drift_section_id(); ?> class="section data-block" style="<?php echo drift_accent_style( (string) get_sub_field( 'accent_suite' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
	<div class="wrap">
		<?php if ( $eyebrow || $heading || $lead ) : ?>
			<header class="suite__head">
				<?php if ( $eyebrow ) : ?><p class="eyebrow eyebrow--accent"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
				<?php if ( $heading ) : ?><h2 class="h2" id="<?php echo esc_attr( $heading_id ); ?>"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
				<?php if ( $lead ) : ?><p class="lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
			</header>
		<?php endif; ?>
		<div class="data-table__scroll" role="region" tabindex="0" aria-label="<?php echo esc_attr( $region ); ?>">
			<table class="data-table data-table--cols-<?php echo esc_attr( (string) count( $columns ) ); ?>"<?php echo $heading_id ? ' aria-labelledby="' . esc_attr( $heading_id ) . '"' : ''; ?>>
				<?php if ( $has_head ) : ?>
					<thead>
						<tr>
							<th scope="col"><?php echo esc_html( $label_head ); ?></th>
							<?php foreach ( $columns as $col ) : ?>
								<th scope="col"><?php echo esc_html( $heads[ $col ] ); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
				<?php endif; ?>
				<tbody>
					<?php
					foreach ( $rows as $row ) :
						$detail = trim( (string) ( $row['detail'] ?? '' ) );
						?>
						<tr<?php echo empty( $row['highlight'] ) ? '' : ' class="data-table__total"'; ?>>
							<th scope="row">
								<?php echo esc_html( $row['label'] ); ?>
								<?php if ( $detail ) : ?><span class="data-table__detail"><?php echo esc_html( $detail ); ?></span><?php endif; ?>
							</th>
							<?php foreach ( $columns as $col ) : ?>
								<td><?php echo esc_html( (string) ( $row[ 'value_' . $col ] ?? '' ) ); ?></td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php if ( $note ) : ?><div class="data-table__note"><?php echo wp_kses_post( wpautop( $note ) ); ?></div><?php endif; ?>
	</div>
</section>
