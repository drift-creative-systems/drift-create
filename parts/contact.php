<?php
/**
 * Contact / demo form section. Always id="contact" — the form handler
 * redirects back to #contact.
 *
 * $args['preselect'] string App name to preselect.
 * $args['eyebrow'], $args['heading'], $args['lead'] Optional copy overrides;
 * empty values fall back to Drift Settings → Contact.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$pre     = (string) ( $args['preselect'] ?? '' );
$eyebrow = (string) ( ( $args['eyebrow'] ?? '' ) ?: drift_option( 'contact_eyebrow', 'Get in touch' ) );
$heading = (string) ( ( $args['heading'] ?? '' ) ?: drift_option( 'contact_heading', 'Book a demo, or ask us anything.' ) );
$lead    = (string) ( ( $args['lead'] ?? '' ) ?: drift_option( 'contact_lead', 'We\'ll walk you through what\'s live, show you Encore on a real band\'s content, and talk about what you\'d want from the apps still in development.' ) );
$success = (string) drift_option( 'contact_success', 'We\'ll get back to you within a working day.' );

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only status flags set by our own redirect.
$sent = isset( $_GET['sent'] );
$err  = isset( $_GET['err'] ) ? sanitize_key( wp_unslash( $_GET['err'] ) ) : '';
// phpcs:enable
?>
<section id="contact" class="section contact">
	<div class="wrap contact__grid">
		<div>
			<?php if ( $eyebrow ) : ?><p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
			<h2 class="h2"><?php echo esc_html( $heading ); ?></h2>
			<?php if ( $lead ) : ?><p class="lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
		</div>
		<?php if ( $sent ) : ?>
			<div class="notice notice--ok" role="status" tabindex="-1"><strong>Thanks, message sent.</strong> <?php echo esc_html( $success ); ?></div>
		<?php else : ?>
			<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php if ( 'mail' === $err ) : ?>
					<p class="notice notice--err" role="alert">Sorry, we couldn't send your message just now. Please try again in a moment.</p>
				<?php elseif ( $err ) : ?>
					<p class="notice notice--err" role="alert">Please fill in your name, a valid email and a message.</p>
				<?php endif; ?>
				<input type="hidden" name="action" value="drift_contact">
				<?php wp_nonce_field( 'drift_contact', 'drift_nonce' ); ?>
				<div class="hp" aria-hidden="true"><label>Leave empty <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label></div>
				<div class="form__row">
					<label>Name<input type="text" name="name" autocomplete="name" required></label>
					<label>Email<input type="email" name="email" autocomplete="email" required></label>
				</div>
				<label>Interested in
					<select name="app">
						<option value="">General enquiry</option>
						<?php foreach ( drift_apps() as $app ) : ?>
							<option <?php selected( $pre, $app->post_title ); ?>><?php echo esc_html( $app->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>Message<textarea name="message" rows="5" required></textarea></label>
				<button type="submit" class="btn btn--dark btn--big">Send</button>
			</form>
		<?php endif; ?>
	</div>
</section>
