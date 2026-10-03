<?php
/**
 * Module: App spotlight — black section featuring one app, with numbered
 * steps, a link to the app page, its demo link, and (optionally) the Encore
 * flow illustration. Heading and lead default to the app's title and tagline.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$app = get_post( (int) get_sub_field( 'app' ) );
if ( ! $app || 'drift_app' !== $app->post_type || 'publish' !== $app->post_status ) {
	return;
}
$m          = drift_app_meta( $app->ID );
$eyebrow    = (string) get_sub_field( 'eyebrow' );
$heading    = (string) ( get_sub_field( 'heading' ) ?: $app->post_title );
$lead       = (string) ( get_sub_field( 'lead' ) ?: $m['tagline'] );
$link_label = (string) ( get_sub_field( 'link_label' ) ?: 'Find out more' );
?>
<section<?php drift_section_id(); ?> class="section spotlight" style="--app: <?php echo esc_attr( $m['accent'] ); ?>">
	<div class="wrap<?php echo get_sub_field( 'show_flow' ) ? ' spotlight__grid' : ''; ?>">
		<div class="spotlight__copy">
			<p class="eyebrow eyebrow--accent"><?php echo esc_html( ( $eyebrow ? $eyebrow . ' · ' : '' ) . $m['label'] ); ?></p>
			<h2 class="h2"><?php echo esc_html( $heading ); ?></h2>
			<?php if ( $lead ) : ?>
				<p class="lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>

			<?php if ( have_rows( 'steps' ) ) : ?>
				<ol class="steps">
					<?php
					while ( have_rows( 'steps' ) ) :
						the_row();
						?>
						<li><b><?php echo esc_html( (string) get_sub_field( 'title' ) ); ?></b> <?php echo esc_html( (string) get_sub_field( 'text' ) ); ?></li>
					<?php endwhile; ?>
				</ol>
			<?php endif; ?>

			<p class="btns">
				<a class="btn btn--accent" href="<?php echo esc_url( get_permalink( $app ) ); ?>"><?php echo esc_html( $link_label ); ?></a>
				<?php if ( get_sub_field( 'show_demo' ) && $m['demo'] ) : ?>
					<a class="btn btn--line-light" href="<?php echo esc_url( $m['demo'] ); ?>" target="_blank" rel="noopener">View demo site</a>
				<?php endif; ?>
			</p>
		</div>
		<?php
		if ( get_sub_field( 'show_flow' ) ) {
			get_template_part( 'parts/encore-flow' );
		}
		?>
	</div>
</section>
