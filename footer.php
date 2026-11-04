<?php
/**
 * Site footer. Tagline, company details, links and social icons: Drift Settings.
 * The Suites column lists suites with a landing page. Ads landing pages
 * (Page settings) keep only the brand and the legal base. Mailing
 * list sign-up (#newsletter): Drift Settings → Newsletter. Legal links:
 * Appearance → Menus → "Footer legal links" (falls back to the privacy
 * policy page set in Settings → Privacy), followed by a CookieScript
 * "Cookie settings" link.
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
			<?php echo drift_is_landing() ? '' : drift_social_links( 'foot__social' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in drift_social_links(). ?>
		</div>
		<?php if ( ! drift_is_landing() ) : ?>
		<div class="foot__cols">
			<div>
				<h2>Suites</h2>
				<ul>
					<?php
					// Suites with a landing page (Drift Settings → Suites), WP Suite first. A full app list gets too long.
					$drift_suite_keys = array_unique( array_merge( [ 'agency', 'music' ], array_keys( drift_suites() ) ) );
					foreach ( $drift_suite_keys as $drift_suite_key ) :
						$drift_suite = drift_suite_details( $drift_suite_key );
						if ( empty( $drift_suite['url'] ) ) {
							continue;
						}
						?>
						<li><a href="<?php echo esc_url( $drift_suite['url'] ); ?>"><?php echo esc_html( $drift_suite['label'] ); ?></a></li>
						<?php if ( 'agency' === $drift_suite_key ) : ?>
							<li><a href="<?php echo esc_url( $drift_suite['url'] . '#free' ); ?>">Free WordPress plugins</a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
			<div>
				<h2>Get in touch</h2>
				<ul>
					<li><a href="<?php echo esc_url( drift_demo_url() ); ?>">Book a demo</a></li>
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
		<?php endif; ?>
		<div class="foot__base">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) . ' ' . drift_brand_name() ); ?> · Theme by <a href="https://bonsaidigitalcollective.co.uk/">The Bonsai Digital Collective</a></p>
			<p class="foot__company"><?php echo esc_html( (string) drift_option( 'footer_company', 'Drift Creative Systems Ltd. Registered in England and Wales, company number 17548220. Registered office: 167-169 Great Portland Street, 5th Floor, London W1W 5PF' ) ); ?></p>
			<?php
			$drift_cookie_link = '<li><a href="#" onclick="CookieScript.instance.show();return false;">Cookie settings</a></li>';
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu( [
					'theme_location'       => 'legal',
					'container'            => 'nav',
					'container_aria_label' => 'Legal',
					'menu_class'           => 'foot__legal',
					'depth'                => 1,
					'fallback_cb'          => false,
					'items_wrap'           => '<ul id="%1$s" class="%2$s">%3$s' . $drift_cookie_link . '</ul>',
				] );
			} elseif ( get_privacy_policy_url() ) {
				echo '<nav aria-label="Legal"><ul class="foot__legal"><li>' . get_the_privacy_policy_link() . '</li>' . $drift_cookie_link . '</ul></nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core function, escaped internally.
			}
			?>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
