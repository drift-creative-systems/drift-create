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
 * The app a form value points at. The "Interested in" list posts the app's
 * slug; older cached pages posted the title, so that still matches.
 *
 * @param string $value Posted value.
 * @return WP_Post|null
 */
function drift_contact_app( string $value ): ?WP_Post {
	if ( '' === $value ) {
		return null;
	}
	foreach ( drift_apps() as $app ) {
		if ( $app->post_name === $value || $app->post_title === $value ) {
			return $app;
		}
	}
	return null;
}

/**
 * Drift Settings → Contact → Thank-you page, or 0 when none is set.
 *
 * @return int
 */
function drift_thanks_page_id(): int {
	$id = (int) drift_option( 'contact_thanks_page', 0 );
	return ( $id && 'publish' === get_post_status( $id ) ) ? $id : 0;
}

/**
 * Where a sent form goes: {thank-you page}/demo/{app}/ for an app, the
 * thank-you page itself for a general enquiry. Empty string when no
 * thank-you page is set (the form then shows its message in place).
 *
 * @param WP_Post|null $app App the enquiry is about.
 * @return string
 */
function drift_thanks_url( ?WP_Post $app ): string {
	$page = drift_thanks_page_id();
	if ( ! $page ) {
		return '';
	}
	$url = (string) get_permalink( $page );
	return $app ? trailingslashit( $url ) . 'demo/' . $app->post_name . '/' : $url;
}

/**
 * Validates the contact form, emails it, then redirects to the thank-you
 * page (if set) or back to the form with ?sent / ?err.
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

	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$app     = drift_contact_app( sanitize_text_field( wp_unslash( $_POST['app'] ?? '' ) ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

	// Honeypot: pretend it worked. Spam goes to the general thank-you page, never an app's, so it can't count as a demo request.
	if ( ! empty( $_POST['website_url'] ) ) {
		$thanks = drift_thanks_url( null );
		wp_safe_redirect( $thanks ?: add_query_arg( 'sent', '1', $back ) . '#contact' );
		exit;
	}

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		wp_safe_redirect( add_query_arg( 'err', '1', $back ) . '#contact' );
		exit;
	}

	$to    = sanitize_email( (string) drift_option( 'contact_recipient', '' ) );
	$to    = is_email( $to ) ? $to : (string) get_option( 'admin_email' );
	$about = $app ? $app->post_title : '';
	$from  = wp_parse_url( $back, PHP_URL_PATH ) ?: '/';

	$sent = wp_mail(
		$to,
		'Drift enquiry' . ( $about ? ' — ' . $about : '' ) . ' from ' . $name,
		"Name: {$name}\nEmail: {$email}\nInterested in: " . ( $about ?: 'General' ) . "\nSent from: {$from}\n\n{$message}",
		[ 'Reply-To: ' . $name . ' <' . $email . '>' ]
	);

	if ( ! $sent ) {
		error_log( 'Drift contact form: wp_mail failed for enquiry from ' . $email ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		wp_safe_redirect( add_query_arg( 'err', 'mail', $back ) . '#contact' );
		exit;
	}

	$thanks = drift_thanks_url( $app );
	wp_safe_redirect( $thanks ?: add_query_arg( 'sent', '1', $back ) . '#contact' );
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

/**
 * /{thank-you page}/demo/{app}/ → the thank-you page with drift_thanks_app
 * set. Rules are re-flushed once whenever the page's path changes.
 */
add_action( 'init', static function () {
	add_rewrite_tag( '%drift_thanks_app%', '([^/]+)' );

	$page = drift_thanks_page_id();
	$path = $page ? get_page_uri( $page ) : '';
	if ( $path ) {
		add_rewrite_rule( '^' . preg_quote( $path, '#' ) . '/demo/([^/]+)/?$', 'index.php?page_id=' . $page . '&drift_thanks_app=$matches[1]', 'top' );
	}
	if ( get_option( 'drift_thanks_rule' ) !== $path ) {
		update_option( 'drift_thanks_rule', $path, true );
		flush_rewrite_rules( false );
	}
}, 20 );

/**
 * The app on a /{thank-you page}/demo/{app}/ URL, or null.
 *
 * @return WP_Post|null
 */
function drift_thanks_app(): ?WP_Post {
	$slug = (string) get_query_var( 'drift_thanks_app' );
	return '' === $slug ? null : drift_contact_app( sanitize_title( $slug ) );
}

/* A demo URL for an app that doesn't exist is a 404, not a second copy of the page. */
add_action( 'template_redirect', static function () {
	if ( '' !== (string) get_query_var( 'drift_thanks_app' ) && ! drift_thanks_app() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
} );

/* Thank-you URLs are never indexed (also tick "Hide from search engines" on the page so it leaves the sitemap). */
add_filter( 'wp_robots', static function ( array $robots ): array {
	if ( drift_thanks_page_id() && is_page( drift_thanks_page_id() ) ) {
		unset( $robots['max-image-preview'] );
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
} );
add_filter( 'wpseo_robots', static function ( $robots ) {
	return ( drift_thanks_page_id() && is_page( drift_thanks_page_id() ) ) ? 'noindex, follow' : $robots;
} );

/*
 * GTM / GA4: a drift_enquiry event on the thank-you page. Google Ads can
 * count conversions on the URL alone (/thank-you/demo/surface/); this is
 * for GA4 reports and GTM triggers.
 */
add_action( 'wp_head', static function () {
	if ( ! drift_thanks_page_id() || ! is_page( drift_thanks_page_id() ) ) {
		return;
	}
	$app  = drift_thanks_app();
	$data = [
		'event'        => 'drift_enquiry',
		'enquiry_type' => $app ? 'demo' : 'general',
		'app'          => $app ? $app->post_name : '',
	];
	printf( "<script>window.dataLayer=window.dataLayer||[];window.dataLayer.push(%s);</script>\n", wp_json_encode( $data ) );
}, 1 );

/* "Thanks — Drift: Surface demo" in the tab title on an app's thank-you URL. */
add_filter( 'document_title_parts', static function ( array $parts ): array {
	$app = drift_thanks_app();
	if ( $app && drift_thanks_page_id() && is_page( drift_thanks_page_id() ) ) {
		$parts['title'] = $parts['title'] . ': ' . $app->post_title . ' demo';
	}
	return $parts;
} );

/**
 * Whether this page prints the #contact section, so "Book a demo" links can
 * stay on the page instead of jumping to the home page's form.
 *
 * @return bool
 */
function drift_page_has_contact(): bool {
	if ( is_singular( 'drift_app' ) ) {
		return true;
	}
	if ( is_front_page() && ! drift_page_layouts( (int) get_queried_object_id() ) ) {
		return true; // front-page.php fallback prints the form.
	}
	return is_singular() && in_array( 'contact', drift_page_layouts( (int) get_queried_object_id() ), true );
}

/**
 * Link for "Book a demo": #contact on pages with the form, else the home page's.
 *
 * @return string
 */
function drift_demo_url(): string {
	return drift_page_has_contact() ? '#contact' : home_url( '/#contact' );
}
