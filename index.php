<?php
/**
 * Fallback template. Handles three cases:
 *
 * - The posts page (Settings → Reading → Posts page), category / tag / date
 *   archives and search: a card grid of posts with All / category filter
 *   links (drift_blog_categories()).
 * - A single blog post: category · date, title, content, and a link back to
 *   the posts page.
 * - Anything else that reaches index.php: title and content.
 *
 * Kept in index.php rather than home.php / archive.php / single.php so the
 * whole blog stays in one file.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;
get_header();

$drift_blog_url = drift_blog_url();

if ( is_home() || is_archive() || is_search() ) :
	if ( is_home() ) {
		$drift_blog_title = get_option( 'page_for_posts' ) ? get_the_title( (int) get_option( 'page_for_posts' ) ) : 'Blog';
		$drift_blog_lead  = 'Releases, guides and the odd look behind the scenes from Drift Creative Systems.';
	} elseif ( is_search() ) {
		/* translators: %s: search terms. */
		$drift_blog_title = sprintf( 'Search: %s', get_search_query() );
		$drift_blog_lead  = '';
	} else {
		$drift_blog_title = single_term_title( '', false ) ? single_term_title( '', false ) : get_the_archive_title();
		$drift_blog_lead  = wp_strip_all_tags( (string) get_the_archive_description() );
	}
	$drift_current_cat = is_category() ? get_queried_object_id() : 0;
	$drift_blog_cats   = drift_blog_categories();
	?>
	<header class="page-head blog-head">
		<div class="wrap">
			<?php if ( ! is_home() ) : ?><p class="eyebrow">Blog</p><?php endif; ?>
			<h1 class="h2"><?php echo esc_html( $drift_blog_title ); ?></h1>
			<?php if ( $drift_blog_lead ) : ?>
				<p class="blog-head__lead"><?php echo esc_html( $drift_blog_lead ); ?></p>
			<?php endif; ?>
			<?php if ( count( $drift_blog_cats ) > 1 && ! is_search() ) : ?>
				<nav class="blog-filter" aria-label="Blog categories">
					<a class="pill<?php echo ( is_home() ) ? ' is-current' : ''; ?>" href="<?php echo esc_url( $drift_blog_url ); ?>"<?php echo is_home() ? ' aria-current="page"' : ''; ?>>All</a>
					<?php foreach ( $drift_blog_cats as $drift_cat ) : ?>
						<a class="pill<?php echo ( $drift_current_cat === $drift_cat->term_id ) ? ' is-current' : ''; ?>" href="<?php echo esc_url( get_category_link( $drift_cat ) ); ?>"<?php echo ( $drift_current_cat === $drift_cat->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $drift_cat->name ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>
	</header>
	<section class="section blog-list">
		<div class="wrap">
			<?php if ( have_posts() ) : ?>
				<div class="cards">
					<?php
					while ( have_posts() ) :
						the_post();
						$drift_post_cat = drift_post_category( get_the_ID() );
						?>
						<article class="card post-card">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="post-card__media"><?php the_post_thumbnail( 'medium_large', [ 'alt' => '', 'loading' => 'lazy' ] ); ?></div>
							<?php endif; ?>
							<p class="post-meta">
								<?php if ( $drift_post_cat ) : ?><span><?php echo esc_html( $drift_post_cat->name ); ?></span> · <?php endif; ?>
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></time>
							</p>
							<h2 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p class="card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
							<p class="card__more" aria-hidden="true">Read &rarr;</p>
						</article>
					<?php endwhile; ?>
				</div>
				<?php
				the_posts_pagination(
					[
						'mid_size'  => 1,
						'prev_text' => '&larr; Newer',
						'next_text' => 'Older &rarr;',
						'class'     => 'blog-pages',
					]
				);
				?>
			<?php else : ?>
				<p class="blog-empty">Nothing here yet. <a href="<?php echo esc_url( $drift_blog_url ); ?>">See all posts</a>.</p>
			<?php endif; ?>
		</div>
	</section>
	<?php
elseif ( is_singular( 'post' ) ) :
	while ( have_posts() ) :
		the_post();
		$drift_post_cat = drift_post_category( get_the_ID() );
		?>
		<article class="section post-single">
			<div class="wrap wrap--text">
				<p class="post-meta">
					<a href="<?php echo esc_url( $drift_blog_url ); ?>">Blog</a>
					<?php if ( $drift_post_cat ) : ?> · <a href="<?php echo esc_url( get_category_link( $drift_post_cat ) ); ?>"><?php echo esc_html( $drift_post_cat->name ); ?></a><?php endif; ?>
					· <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
				</p>
				<h1 class="h2"><?php the_title(); ?></h1>
				<div class="prose"><?php the_content(); ?></div>
				<p class="post-single__back"><a class="btn btn--line" href="<?php echo esc_url( $drift_blog_url ); ?>">&larr; All posts</a></p>
			</div>
		</article>
		<?php
	endwhile;
else :
	while ( have_posts() ) :
		the_post();
		?>
		<article class="section">
			<div class="wrap wrap--text">
				<h1 class="h2"><?php the_title(); ?></h1>
				<div class="prose"><?php the_content(); ?></div>
			</div>
		</article>
		<?php
	endwhile;
endif;

get_footer();
