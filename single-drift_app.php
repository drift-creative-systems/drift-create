<?php
/**
 * One app.
 *
 * @package Drift_Create
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$m = drift_app_meta( get_the_ID() );
	?>
	<article class="app" style="--app: <?php echo esc_attr( $m['accent'] ); ?>">
		<header class="app__hero">
			<div class="wrap">
				<p class="app__crumb"><a href="<?php echo esc_url( home_url( '/#apps' ) ); ?>">&larr; All apps</a></p>
				<p class="eyebrow"><?php echo esc_html( $m['suite_label'] ); ?></p>
				<span class="app__emoji" aria-hidden="true"><?php echo esc_html( $m['emoji'] ?: '✦' ); ?></span>
				<h1 class="app__title"><?php the_title(); ?></h1>
				<?php if ( $m['tagline'] ) : ?><p class="hero__lead"><?php echo esc_html( $m['tagline'] ); ?></p><?php endif; ?>
				<p class="btns">
					<span class="pill pill--<?php echo esc_attr( $m['status'] ); ?>"><?php echo esc_html( $m['label'] ); ?></span>
					<?php if ( $m['demo'] ) : ?><a class="btn btn--accent" href="<?php echo esc_url( $m['demo'] ); ?>" target="_blank" rel="noopener">View demo</a><?php endif; ?>
					<a class="btn btn--line" href="#contact"><?php echo drift_app_is_available( $m['status'] ) ? 'Book a demo' : 'Register interest'; ?></a>
				</p>
			</div>
		</header>

		<?php if ( 'encore' === get_post_field( 'post_name' ) ) : ?>
			<div class="wrap app__flow"><?php get_template_part( 'parts/encore-flow' ); ?></div>
		<?php endif; ?>

		<div class="wrap wrap--text prose"><?php the_content(); ?></div>
	</article>

	<?php
	$others = array_filter( drift_apps(), static fn( $a ) => $a->ID !== get_the_ID() );
	if ( $others ) :
		?>
		<section class="section">
			<div class="wrap">
				<h2 class="h3">More from Drift</h2>
				<div class="cards cards--small">
					<?php
					foreach ( $others as $other ) {
						get_template_part( 'parts/app-card', null, [ 'post' => $other ] );
					}
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	drift_contact_form( get_the_title() );
endwhile;

get_footer();
