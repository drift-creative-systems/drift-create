<?php
/**
 * Contact / demo form handler (admin-post → wp_mail).
 *
 * The form itself is parts/contact.php. Recipient and copy defaults are in
 * Drift Settings → Contact.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

/**
 * Validates the contact form, emails it and redirects back with ?sent or ?err.
 *
 * @return void
 */
function drift_contact_handler(): void {
	$back = wp_get_referer() ?: home_url( '/' );
	$back = remove_query_arg( [ 'sent', 'err' ], $back );

	if ( ! isset( $_POST['drift_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['drift_nonce'] ) ), 'drift_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'err', '1', $back ) . '#contact' );
		exit;
	}
	if ( ! empty( $_POST['website_url'] ) ) { // Honeypot: pretend it worked.
		wp_safe_redirect( add_query_arg( 'sent', '1', $back ) . '#contact' );
		exit;
	}

	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$app     = sanitize_text_field( wp_unslash( $_POST['app'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		wp_safe_redirect( add_query_arg( 'err', '1', $back ) . '#contact' );
		exit;
	}

	$to = sanitize_email( (string) drift_option( 'contact_recipient', '' ) );
	$to = is_email( $to ) ? $to : (string) get_option( 'admin_email' );

	$sent = wp_mail(
		$to,
		'Drift enquiry' . ( $app ? ' — ' . $app : '' ) . ' from ' . $name,
		"Name: {$name}\nEmail: {$email}\nInterested in: " . ( $app ?: 'General' ) . "\n\n{$message}",
		[ 'Reply-To: ' . $name . ' <' . $email . '>' ]
	);

	if ( ! $sent ) {
		error_log( 'Drift contact form: wp_mail failed for enquiry from ' . $email ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		wp_safe_redirect( add_query_arg( 'err', 'mail', $back ) . '#contact' );
		exit;
	}

	wp_safe_redirect( add_query_arg( 'sent', '1', $back ) . '#contact' );
	exit;
}
add_action( 'admin_post_drift_contact', 'drift_contact_handler' );
add_action( 'admin_post_nopriv_drift_contact', 'drift_contact_handler' );

/**
 * Outputs the contact section. Empty args fall back to Drift Settings.
 *
 * @param string $preselect App name to preselect in "Interested in".
 * @param array  $copy      Optional eyebrow / heading / lead overrides.
 * @return void
 */
function drift_contact_form( string $preselect = '', array $copy = [] ): void {
	get_template_part( 'parts/contact', null, array_merge( $copy, [ 'preselect' => $preselect ] ) );
}
