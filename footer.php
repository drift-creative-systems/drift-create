<?php
/**
 * Site footer. Tagline, company details, links and social icons: Drift Settings. Mailing
 * list sign-up (#newsletter): Drift Settings → Newsletter. Legal links:
 * Appearance → Menus → "Footer legal links" (falls back to the privacy
 * policy page set in Settings → Privacy).
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$drift_footer_links = drift_option( 'footer_links', [ [ 'link' => [ 'title' => 'GitHub', 'url' => 'https://github.com/drift-creative-systems', 'target' => '' ] ] ] );
?>
</main>
<footer class="foot">
	<div class="wrap foot__inner">
		<div class="foot__brand">
			<?php
			$drift_footer_logo = drift_logo( 'footer_logo', 'foot__logo' );
			if ( $drift_footer_logo ) :
				echo $drift_footer_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image().
			else :
				echo drift_mark( 'foot__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG.
				?>
				<p class="foot__word" aria-hidden="true">DRIFT<span><?php echo esc_html( drift_brand_line() ); ?></span></p>
			<?php endif; ?>
			<p class="foot__tag"><?php echo esc_html( (string) drift_option( 'footer_tagline', 'Modern. Minimal. Purposeful.' ) ); ?></p>
			<?php echo drift_social_links( 'foot__social' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in drift_social_links(). ?>
		</div>
		<div class="foot__cols">
			<div>
				<h2>Apps</h2>
				<ul>
					<?php foreach ( drift_apps() as $app ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $app ) ); ?>"><?php echo esc_html( $app->post_title ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div>
				<h2>Get in touch</h2>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">Book a demo</a></li>
					<?php
					foreach ( (array) $drift_footer_links as $row ) :
						$link = $row['link'] ?? null;
						if ( empty( $link['url'] ) || empty( $link['title'] ) ) {
							continue;
						}
						?>
						<li><a href="<?php echo esc_url( $link['url'] ); ?>"<?php echo empty( $link['target'] ) ? '' : ' target="_blank"'; ?> rel="noopener"><?php echo esc_html( $link['title'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php if ( drift_newsletter_in_footer() ) : ?>
				<div id="newsletter" class="foot__signup">
					<h2><?php echo esc_html( (string) drift_option( 'newsletter_heading', 'Newsletter' ) ); ?></h2>
					<p class="foot__lead"><?php echo esc_html( (string) drift_option( 'newsletter_lead', 'New apps, releases and the odd look behind the scenes. No spam.' ) ); ?></p>
					<?php drift_newsletter_form( 'newsletter', [ 'button_class' => 'btn--line-light' ] ); ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="foot__base">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) . ' ' . drift_brand_name() ); ?> · Built in Devon by The Bonsai Digital Collective</p>
			<p class="foot__company"><?php echo esc_html( (string) drift_option( 'footer_company', 'Drift Creative Systems Ltd. Registered in England and Wales, company number 17548220. Registered office: 167-169 Great Portland Street, 5th Floor, London W1W 5PF' ) ); ?></p>
			<?php
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu( [
					'theme_location'       => 'legal',
					'container'            => 'nav',
					'container_aria_label' => 'Legal',
					'menu_class'           => 'foot__legal',
					'depth'                => 1,
					'fallback_cb'          => false,
				] );
			} elseif ( get_privacy_policy_url() ) {
				echo '<nav aria-label="Legal"><ul class="foot__legal"><li>' . get_the_privacy_policy_link() . '</li></ul></nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core function, escaped internally.
			}
			?>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
