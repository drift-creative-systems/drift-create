<?php
defined( 'ABSPATH' ) || exit;
get_header();
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
get_footer();
