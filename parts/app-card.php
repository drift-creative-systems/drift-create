<?php
/**
 * App card. $args['post'] (WP_Post), $args['featured'] (bool).
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$app = $args['post'] ?? null;
if ( ! $app instanceof WP_Post ) {
	return;
}
$m = drift_app_meta( $app->ID );
?>
<article class="card card--<?php echo esc_attr( $m['status'] ); ?><?php echo ! empty( $args['featured'] ) ? ' card--featured' : ''; ?>" style="--app: <?php echo esc_attr( $m['accent'] ); ?>">
	<div class="card__top">
		<?php echo drift_app_icon( $app->ID, 'card__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
		<span class="card__suite"><?php echo esc_html( $m['suite_label'] ); ?></span>
		<span class="pill pill--<?php echo esc_attr( $m['status'] ); ?>"><?php echo esc_html( $m['label'] ); ?></span>
	</div>
	<h3 class="card__title"><a href="<?php echo esc_url( get_permalink( $app ) ); ?>"><?php echo esc_html( $app->post_title ); ?></a></h3>
	<?php if ( $m['tagline'] ) : ?><p class="card__tagline"><?php echo esc_html( $m['tagline'] ); ?></p><?php endif; ?>
	<p class="card__text"><?php echo esc_html( get_the_excerpt( $app ) ); ?></p>
	<p class="card__more" aria-hidden="true"><?php echo drift_app_is_available( $m['status'] ) ? 'See it' : 'Register interest'; ?> &rarr;</p>
</article>
