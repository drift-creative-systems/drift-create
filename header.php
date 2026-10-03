<?php defined( 'ABSPATH' ) || exit; ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main">Skip to content</a>
<header class="top">
	<div class="wrap top__inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( drift_brand_name() ); ?> — home">
			<?php echo drift_mark( 'brand__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?>
			<span class="brand__word">DRIFT<span class="brand__line"><?php echo esc_html( drift_brand_line() ); ?></span></span>
		</a>
		<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="menu"><span></span><span class="sr">Menu</span></button>
		<nav id="menu" class="menu" aria-label="Main">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( [ 'theme_location' => 'primary', 'container' => false, 'depth' => 1 ] );
			} else {
				$home = home_url( '/' );
				printf(
					'<ul><li><a href="%1$s#apps">Apps</a></li><li><a href="%1$s#encore">Encore</a></li><li><a href="%1$s#about">About</a></li></ul>',
					esc_url( $home )
				);
			}
			?>
			<a class="btn btn--dark" href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">Book a demo</a>
		</nav>
	</div>
</header>
<main id="main" tabindex="-1">
