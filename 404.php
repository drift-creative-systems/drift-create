<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="hero hero--small"><div class="wrap">
	<p class="eyebrow">404</p>
	<h1 class="hero__title">Nothing here.</h1>
	<p class="hero__lead">That page doesn't exist.</p>
	<p class="btns"><a class="btn btn--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></p>
</div></section>
<?php
get_footer();
