<?php
/**
 * Filter buttons for a section marked data-filter-scope. assets/site.js hides
 * items whose data-filter-tags don't include the chosen key, then any
 * data-filter-group left empty. Hidden until the script shows it, so
 * there's no dead control without JS.
 *
 * $args['options'] array<string, string>  Key → label. "All" is added first.
 * $args['label']   string                 Group name for screen readers.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$options = (array) ( $args['options'] ?? [] );
if ( count( $options ) < 2 ) {
	return;
}
?>
<div class="filter" role="group" aria-label="<?php echo esc_attr( (string) ( $args['label'] ?? 'Filter' ) ); ?>" hidden>
	<button type="button" class="filter__btn" data-filter="" aria-pressed="true">All</button>
	<?php foreach ( $options as $key => $label ) : ?>
		<button type="button" class="filter__btn" data-filter="<?php echo esc_attr( (string) $key ); ?>" aria-pressed="false"><?php echo esc_html( $label ); ?></button>
	<?php endforeach; ?>
	<p class="filter__status sr" aria-live="polite"></p>
</div>
