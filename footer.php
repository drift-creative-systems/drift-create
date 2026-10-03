<?php
/**
 * Site footer. Tagline and links: Drift Settings → Footer.
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
			<?php echo drift_mark( 'foot__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?>
			<p class="foot__word" aria-hidden="true">DRIFT<span><?php echo esc_html( drift_brand_line() ); ?></span></p>
			<p class="foot__tag"><?php echo esc_html( (string) drift_option( 'footer_tagline', 'Modern. Minimal. Purposeful.' ) ); ?></p>
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
		</div>
		<p class="foot__base">&copy; <?php echo esc_html( wp_date( 'Y' ) . ' ' . drift_brand_name() ); ?> · Built in Devon by The Bonsai Digital Collective</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
