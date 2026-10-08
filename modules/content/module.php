<?php
/**
 * Module: Content — heading and a rich text (WYSIWYG) block, for standard
 * pages. As the first module it takes the page's <h1> and falls back to the
 * page title when the heading is empty.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$heading = (string) get_sub_field( 'heading' );
$body    = (string) get_sub_field( 'body' );
$is_h1   = ! drift_h1_used( true );
if ( '' === $heading && $is_h1 ) {
	$heading = get_the_title();
}
if ( '' === $heading && '' === $body ) {
	return;
}
$tag = $is_h1 ? 'h1' : 'h2';
?>
<section<?php drift_section_id(); ?> class="section content">
	<div class="wrap wrap--text">
		<?php if ( $heading ) : ?>
			<<?php echo tag_escape( $tag ); ?> class="h2 content__title"><?php echo esc_html( $heading ); ?></<?php echo tag_escape( $tag ); ?>>
		<?php endif; ?>
		<?php if ( $body ) : ?>
			<div class="prose prose--flush"><?php echo wp_kses_post( $body ); ?></div>
		<?php endif; ?>
	</div>
</section>
