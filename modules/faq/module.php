<?php
/**
 * Module: FAQ — accordion of questions using native <details> (keyboard and
 * screen-reader friendly, no JS). Optionally outputs FAQPage JSON-LD.
 *
 * Only one FAQ per page should output schema. Turn it off if an SEO plugin
 * already adds FAQ schema for the page.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$items = get_sub_field( 'items' );
if ( empty( $items ) || ! is_array( $items ) ) {
	return;
}
$eyebrow = (string) get_sub_field( 'eyebrow' );
$heading = (string) get_sub_field( 'heading' );
$schema  = [];
?>
<section<?php drift_section_id(); ?> class="section faq">
	<div class="wrap faq__grid">
		<div class="faq__head">
			<?php if ( $eyebrow ) : ?><p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
			<?php if ( $heading ) : ?><h2 class="h2"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
		</div>
		<div class="faq__list">
			<?php
			foreach ( $items as $item ) :
				$question = (string) ( $item['question'] ?? '' );
				$answer   = (string) ( $item['answer'] ?? '' );
				if ( '' === $question || '' === $answer ) {
					continue;
				}
				$schema[] = [
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $question ),
					'acceptedAnswer' => [ '@type' => 'Answer', 'text' => wp_strip_all_tags( $answer ) ],
				];
				?>
				<details class="faq__item">
					<summary class="faq__q"><?php echo esc_html( $question ); ?></summary>
					<div class="faq__a prose prose--flush"><?php echo wp_kses_post( $answer ); ?></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php
if ( $schema && get_sub_field( 'output_schema' ) ) {
	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( [ '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $schema ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG )
	);
}
