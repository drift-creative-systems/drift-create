<?php
/**
 * Page: page modules if the page has any, otherwise the title and editor
 * content. When the first module doesn't print an <h1> (see
 * drift_layout_has_h1()), the page title is shown above the modules so the
 * page still has its <h1>.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$layouts = function_exists( 'have_rows' ) ? drift_page_layouts( get_the_ID() ) : [];

	if ( $layouts ) :
		if ( ! drift_layout_has_h1( $layouts[0] ) ) :
			drift_h1_used( true );
			?>
			<header class="page-head">
				<div class="wrap"><h1 class="h2"><?php the_title(); ?></h1></div>
			</header>
			<?php
		endif;
		drift_render_modules( get_the_ID() );
	else :
		?>
		<article class="section">
			<div class="wrap wrap--text">
				<h1 class="h2"><?php the_title(); ?></h1>
				<div class="prose"><?php the_content(); ?></div>
			</div>
		</article>
		<?php
	endif;
endwhile;

get_footer();
