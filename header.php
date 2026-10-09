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
			<?php
			$drift_header_logo = drift_logo( 'header_logo', 'brand__logo', true );
			if ( $drift_header_logo ) :
				// Uploaded logos are made for the light bar. In dark mode show the
				// dark-mode upload, or the built-in lockup (it follows the text colour).
				$drift_header_logo_dark = drift_logo( 'header_logo_dark', 'brand__logo', true );
				?>
				<span class="brand__set light-only"><?php echo $drift_header_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image(). ?></span>
				<span class="brand__set dark-only">
					<?php
					if ( $drift_header_logo_dark ) :
						echo $drift_header_logo_dark; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image().
					else :
						echo drift_mark( 'brand__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG.
						?>
						<span class="brand__word">DRIFT<span class="brand__line"><?php echo esc_html( drift_brand_line() ); ?></span></span>
					<?php endif; ?>
				</span>
			<?php else : ?>
				<?php echo drift_mark( 'brand__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?>
				<span class="brand__word">DRIFT<span class="brand__line"><?php echo esc_html( drift_brand_line() ); ?></span></span>
			<?php endif; ?>
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
		<?php // Hidden until assets/site.js wires it up; aria-pressed = dark mode on. ?>
		<button class="theme-toggle" type="button" aria-pressed="false" title="Dark mode" hidden>
			<svg class="light-only" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>
			<svg class="dark-only" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
			<span class="sr">Dark mode</span>
		</button>
	</div>
</header>
<main id="main" tabindex="-1">
