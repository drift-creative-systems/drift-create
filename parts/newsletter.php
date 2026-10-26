<?php
/**
 * Mailing list sign-up form (Brevo double opt-in). Output it with
 * drift_newsletter_form(), inside an element whose id matches $args['form'].
 *
 * $args['form']         string Anchor ID the handler redirects back to.
 * $args['button_class'] string Button style class (default 'btn--dark').
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$form      = sanitize_title( (string) ( $args['form'] ?? '' ) ) ?: 'newsletter';
$btn_class = sanitize_html_class( (string) ( $args['button_class'] ?? '' ) ) ?: 'btn--dark';
$button    = (string) drift_option( 'newsletter_button', 'Subscribe' );
$success   = (string) drift_option( 'newsletter_success', 'Check your inbox and click the link to confirm your email.' );
$privacy   = get_privacy_policy_url();

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only status flags set by our own redirect.
$status = ( isset( $_GET['nl'], $_GET['nl_form'] ) && sanitize_title( wp_unslash( $_GET['nl_form'] ) ) === $form )
	? sanitize_key( wp_unslash( $_GET['nl'] ) )
	: '';
// phpcs:enable

if ( 'ok' === $status ) : ?>
	<div class="notice notice--ok" role="status"><strong>Nearly there.</strong> <?php echo esc_html( $success ); ?></div>
<?php else : ?>
	<form class="signup" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php if ( 'fail' === $status ) : ?>
			<p class="notice notice--err" role="alert">Sorry, we couldn't sign you up just now. Please try again in a moment.</p>
		<?php elseif ( $status ) : ?>
			<p class="notice notice--err" role="alert">Please enter a valid email address.</p>
		<?php endif; ?>
		<input type="hidden" name="action" value="drift_newsletter">
		<input type="hidden" name="nl_form" value="<?php echo esc_attr( $form ); ?>">
		<?php // Not wp_nonce_field(): its fixed id would repeat when the footer and a module are on one page. ?>
		<input type="hidden" name="drift_nl_nonce" value="<?php echo esc_attr( wp_create_nonce( 'drift_newsletter' ) ); ?>">
		<?php wp_referer_field(); ?>
		<div class="hp" aria-hidden="true"><label>Leave empty <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label></div>
		<label class="signup__label" for="<?php echo esc_attr( $form ); ?>-email">Email address</label>
		<div class="signup__row">
			<input id="<?php echo esc_attr( $form ); ?>-email" type="email" name="email" autocomplete="email" required>
			<button type="submit" class="btn <?php echo esc_attr( $btn_class ); ?>"><?php echo esc_html( $button ); ?></button>
		</div>
		<p class="signup__note">We'll email you a link to confirm. Unsubscribe any time.<?php if ( $privacy ) : ?> <a href="<?php echo esc_url( $privacy ); ?>">Privacy policy</a><?php endif; ?></p>
	</form>
<?php endif; ?>
