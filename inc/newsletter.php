<?php
/**
 * Mailing list sign-up: Brevo double opt-in (admin-post → Brevo API).
 *
 * The form is parts/newsletter.php, shown in the footer and by the Newsletter
 * module. Settings are in Drift Settings → Newsletter. DRIFT_BREVO_API_KEY in
 * wp-config.php, if defined, wins over the API key field.
 *
 * Brevo emails the visitor the double opt-in template. They only join the
 * list once they click its confirm link.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

/**
 * Brevo API key: the wp-config constant, else the Drift Settings field.
 *
 * @return string
 */
function drift_brevo_api_key(): string {
	if ( defined( 'DRIFT_BREVO_API_KEY' ) && DRIFT_BREVO_API_KEY ) {
		return trim( (string) DRIFT_BREVO_API_KEY );
	}
	return trim( (string) drift_option( 'brevo_api_key', '' ) );
}

/**
 * Whether Brevo is set up enough to take sign-ups (key, list and DOI template).
 * Forms stay hidden until it is.
 *
 * @return bool
 */
function drift_newsletter_ready(): bool {
	return '' !== drift_brevo_api_key()
		&& absint( drift_option( 'brevo_list_id', 2 ) ) > 0
		&& absint( drift_option( 'brevo_doi_template_id', 0 ) ) > 0;
}

/**
 * Whether the footer shows the sign-up. On until "Show in footer" is unticked.
 *
 * @return bool
 */
function drift_newsletter_in_footer(): bool {
	// Read directly: drift_option() treats an unticked (false) field as empty.
	$show = function_exists( 'get_field' ) ? get_field( 'newsletter_footer', 'option' ) : null;
	return ( null === $show || (bool) $show ) && drift_newsletter_ready();
}

/**
 * Sends Brevo a double opt-in request for an email address.
 *
 * @param string $email Sanitised, valid email address.
 * @return bool True when Brevo accepted it (new or existing contact).
 */
function drift_brevo_subscribe( string $email ): bool {
	$response = wp_remote_post(
		'https://api.brevo.com/v3/contacts/doubleOptinConfirmation',
		[
			'timeout' => 10,
			'headers' => [
				'api-key'      => drift_brevo_api_key(),
				'accept'       => 'application/json',
				'content-type' => 'application/json',
			],
			'body'    => wp_json_encode( [
				'email'          => $email,
				'includeListIds' => [ absint( drift_option( 'brevo_list_id', 2 ) ) ],
				'templateId'     => absint( drift_option( 'brevo_doi_template_id', 0 ) ),
				'redirectionUrl' => esc_url_raw( (string) drift_option( 'brevo_doi_redirect', home_url( '/' ) ) ),
			] ),
		]
	);

	if ( is_wp_error( $response ) ) {
		error_log( 'Drift newsletter: Brevo request failed: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return false;
	}

	// 201 = confirmation email sent, 204 = existing contact updated.
	$code = (int) wp_remote_retrieve_response_code( $response );
	if ( $code >= 200 && $code < 300 ) {
		return true;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	$why  = is_array( $data ) && ! empty( $data['message'] ) ? (string) $data['message'] : 'no message';
	error_log( 'Drift newsletter: Brevo returned HTTP ' . $code . ': ' . $why ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	return false;
}

/**
 * Validates the sign-up form, sends it to Brevo and redirects back to the
 * form's anchor with ?nl=ok|err|fail (err = bad input, fail = Brevo error).
 *
 * @return void
 */
function drift_newsletter_handler(): void {
	$form = sanitize_title( wp_unslash( $_POST['nl_form'] ?? '' ) ) ?: 'newsletter'; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Only picks the redirect anchor; nonce checked below.
	$back = remove_query_arg( [ 'nl', 'nl_form' ], wp_get_referer() ?: home_url( '/' ) );

	$done = static function ( string $status ) use ( $back, $form ): void {
		wp_safe_redirect( add_query_arg( [ 'nl' => $status, 'nl_form' => $form ], $back ) . '#' . $form );
		exit;
	};

	if ( ! isset( $_POST['drift_nl_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['drift_nl_nonce'] ) ), 'drift_newsletter' ) ) {
		$done( 'err' );
	}
	if ( ! empty( $_POST['website_url'] ) ) { // Honeypot: pretend it worked.
		$done( 'ok' );
	}

	$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	if ( ! is_email( $email ) ) {
		$done( 'err' );
	}
	if ( ! drift_newsletter_ready() ) {
		$done( 'fail' );
	}

	$done( drift_brevo_subscribe( $email ) ? 'ok' : 'fail' );
}
add_action( 'admin_post_drift_newsletter', 'drift_newsletter_handler' );
add_action( 'admin_post_nopriv_drift_newsletter', 'drift_newsletter_handler' );

/**
 * Outputs the sign-up form.
 *
 * @param string $form Anchor ID of the element wrapping the form. The handler
 *                     redirects back to it, and only this form shows the result.
 * @param array  $args Optional 'button_class' (default 'btn--dark').
 * @return void
 */
function drift_newsletter_form( string $form, array $args = [] ): void {
	get_template_part( 'parts/newsletter', null, array_merge( $args, [ 'form' => $form ] ) );
}

/* Say on the settings screen when the wp-config constant overrides the field. */
add_filter( 'acf/load_field/name=brevo_api_key', static function ( array $field ): array {
	if ( defined( 'DRIFT_BREVO_API_KEY' ) && DRIFT_BREVO_API_KEY ) {
		$field['instructions'] .= ' DRIFT_BREVO_API_KEY is set in wp-config.php, so that key is used and this field is ignored.';
	}
	return $field;
} );
