<?php
/**
 * One app. The hero comes from App details and always shows; the page
 * modules (if any) follow it, otherwise the old editor content does. The
 * hero prints the <h1>, so modules below it use h2s. A #roadmap section
 * follows when the app has roadmap items. The contact form is added at the
 * end unless a Contact module is already on the page.
 *
 * /apps/{app}/roadmap/ (inc/roadmap.php) uses the same hero, then the full
 * roadmap instead of the modules.
 *
 * @package Drift_Create
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$m            = drift_app_meta( get_the_ID() );
	$suite        = drift_suite_details( $m['suite'] );
	$roadmap      = drift_app_roadmap( get_the_ID() );
	$roadmap_view = drift_is_roadmap_view();
	$layouts      = function_exists( 'have_rows' ) && ! $roadmap_view ? drift_page_layouts( get_the_ID() ) : [];
	?>
	<article class="app" style="--app: <?php echo esc_attr( $m['accent'] ); ?>">
		<header class="app__hero">
			<div class="wrap">
				<p class="app__crumb">
					<?php if ( $roadmap_view ) : ?>
						<a href="<?php the_permalink(); ?>">&larr; <?php the_title(); ?></a>
					<?php elseif ( $suite['url'] ) : ?>
						<?php // Back to the suite's landing page when one is set (Drift Settings → Suites). ?>
						<a href="<?php echo esc_url( $suite['url'] ); ?>">&larr; <?php echo esc_html( $suite['label'] ); ?></a>
					<?php else : ?>
						<a href="<?php echo esc_url( home_url( '/#apps' ) ); ?>">&larr; All apps</a>
					<?php endif; ?>
				</p>
				<p class="eyebrow"><?php echo esc_html( $roadmap_view ? 'Roadmap · ' . $m['suite_label'] : $m['suite_label'] ); ?></p>
				<?php echo drift_app_icon( get_the_ID(), 'app__icon', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
				<h1 class="app__title"><?php the_title(); ?><?php echo $roadmap_view ? '<span class="sr"> roadmap</span>' : ''; ?></h1>
				<?php drift_h1_used( true ); ?>
				<?php if ( $roadmap_view ) : ?>
					<p class="hero__lead">What we're working on now, what's next and what's further out.</p>
				<?php else : ?>
					<?php if ( $m['tagline'] ) : ?><p class="hero__lead"><?php echo esc_html( $m['tagline'] ); ?></p><?php endif; ?>
					<?php echo drift_platform_tags( $m['platforms'], 'app__platforms' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
				<?php endif; ?>
				<p class="btns">
					<span class="pill pill--<?php echo esc_attr( $m['status'] ); ?>"><?php echo esc_html( $m['label'] ); ?></span>
					<?php if ( $m['demo'] ) : ?><a class="btn btn--accent" href="<?php echo esc_url( $m['demo'] ); ?>" target="_blank" rel="noopener">View demo</a><?php endif; ?>
					<?php if ( $roadmap_view ) : ?>
						<a class="btn btn--line" href="#contact">Suggest a feature</a>
					<?php else : ?>
						<a class="btn btn--line" href="#contact"><?php echo drift_app_is_available( $m['status'] ) ? 'Book a demo' : 'Register interest'; ?></a>
					<?php endif; ?>
				</p>
			</div>
		</header>

		<?php if ( $roadmap_view ) : ?>
			<section class="section">
				<div class="wrap">
					<?php // Stage headings are h3s; keep the outline h1 → h2 → h3. ?>
					<h2 class="sr">Roadmap</h2>
					<?php get_template_part( 'parts/roadmap', null, [ 'items' => $roadmap, 'shipped' => count( $roadmap ) ] ); ?>
				</div>
			</section>
		<?php else : ?>
			<?php if ( 'encore' === get_post_field( 'post_name' ) ) : ?>
				<div class="wrap app__flow"><?php get_template_part( 'parts/encore-flow' ); ?></div>
			<?php endif; ?>

			<?php if ( ! $layouts || ! drift_render_modules( get_the_ID() ) ) : ?>
				<div class="wrap wrap--text prose"><?php the_content(); ?></div>
			<?php endif; ?>

			<?php if ( $roadmap ) : ?>
				<section id="roadmap" class="section app-roadmap">
					<div class="wrap">
						<header class="suite__head">
							<p class="eyebrow eyebrow--accent">Roadmap</p>
							<h2 class="h3">What's coming to <?php the_title(); ?></h2>
						</header>
						<?php get_template_part( 'parts/roadmap', null, [ 'items' => $roadmap, 'shipped' => 3, 'limit' => 3 ] ); ?>
						<p class="app-roadmap__more"><a href="<?php echo esc_url( drift_app_roadmap_url( get_the_ID() ) ); ?>">Full roadmap<span class="sr"> for <?php the_title(); ?></span> &rarr;</a></p>
					</div>
				</section>
			<?php endif; ?>
		<?php endif; ?>
	</article>

	<?php
	$others = $roadmap_view ? [] : array_filter( drift_apps(), static fn( $a ) => $a->ID !== get_the_ID() );
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
	if ( ! in_array( 'contact', $layouts, true ) ) {
		drift_contact_form( get_the_title() );
	}
endwhile;

get_footer();
