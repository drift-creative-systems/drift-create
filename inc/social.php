<?php
/**
 * Social links: network list, icons and the footer icon row.
 * Links are set on Drift Settings → Footer → Social links.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

/**
 * Supported networks. Single source for the ACF select choices and the icons.
 * Icons are 24×24 line icons (stroke = currentColor), so they follow the
 * footer text colour in both themes.
 *
 * @return array<string, array{label: string, icon: string}> Keyed by network slug.
 */
function drift_social_networks(): array {
	return [
		'instagram'  => [
			'label' => 'Instagram',
			'icon'  => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
		],
		'facebook'   => [
			'label' => 'Facebook',
			'icon'  => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
		],
		'x'          => [
			'label' => 'X',
			'icon'  => '<path d="M4 4l11.7 16H20L8.3 4H4z"/><path d="M4 20l6.8-6.8M13.2 10.8L20 4"/>',
		],
		'linkedin'   => [
			'label' => 'LinkedIn',
			'icon'  => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
		],
		'youtube'    => [
			'label' => 'YouTube',
			'icon'  => '<path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><path d="M9.75 15.02l5.75-3.27-5.75-3.27v6.54z"/>',
		],
		'tiktok'     => [
			'label' => 'TikTok',
			'icon'  => '<path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/>',
		],
		'bluesky'    => [
			'label' => 'Bluesky',
			'icon'  => '<path d="M6.335 5.144C4.681 3.945 2 3.017 2 5.97c0 .59.35 4.953.555 5.661.713 2.463 3.13 2.75 5.445 2.369-4.045.665-4.889 3.208-2.667 5.41C6.363 20.428 7.246 21 8 21c2 0 3.134-2.769 3.5-3.5.333-.667.5-1.167.5-1.5 0 .333.167.833.5 1.5.366.731 1.5 3.5 3.5 3.5.754 0 1.637-.571 2.667-1.59 2.222-2.203 1.378-4.746-2.667-5.41 2.314.38 4.732.094 5.445-2.369.206-.708.555-5.07.555-5.661 0-2.953-2.68-2.025-4.335-.826C15.372 6.806 12.905 10.192 12 12c-.905-1.808-3.372-5.194-5.665-6.856z"/>',
		],
		'spotify'    => [
			'label' => 'Spotify',
			'icon'  => '<circle cx="12" cy="12" r="9"/><path d="M7 9c2-1 6-2 10 .5M8 11.973c2.5-1.473 5.5-.973 7.5.527M9 15c1.5-1 4-1 5 .5"/>',
		],
		'soundcloud' => [
			'label' => 'SoundCloud',
			'icon'  => '<path d="M17 11h1c1.38 0 3 1.274 3 3 0 1.657-1.5 3-3 3h-6V7c3 0 4.5 1.5 5 4z"/><path d="M9 8v9M6 17v-7M3 16v-2"/>',
		],
		'github'     => [
			'label' => 'GitHub',
			'icon'  => '<path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>',
		],
		'email'      => [
			'label' => 'Email',
			'icon'  => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 6L2 7"/>',
		],
	];
}

/* Fill the Social links network select from drift_social_networks(). */
add_filter( 'acf/load_field/key=field_drift_settings_social_links_network', static function ( array $field ): array {
	$field['choices'] = wp_list_pluck( drift_social_networks(), 'label' );
	return $field;
} );

/**
 * The footer social icon row from Drift Settings → Footer → Social links.
 * Rows with an unknown network or no URL are skipped. Email rows accept a
 * plain address or a mailto: link.
 *
 * @param string $class CSS class for the list.
 * @return string List markup, or '' when there are no valid links.
 */
function drift_social_links( string $class = 'social' ): string {
	$networks = drift_social_networks();
	$items    = '';

	foreach ( (array) drift_option( 'social_links', [] ) as $row ) {
		$network = (string) ( $row['network'] ?? '' );
		$url     = trim( (string) ( $row['url'] ?? '' ) );
		if ( ! isset( $networks[ $network ] ) || '' === $url ) {
			continue;
		}

		$is_email = 'email' === $network;
		if ( $is_email && is_email( $url ) ) {
			$url = 'mailto:' . $url;
		}
		$url = esc_url( $url, $is_email ? [ 'mailto' ] : [ 'http', 'https' ] );
		if ( '' === $url ) {
			continue; // Wrong protocol for the network.
		}

		$items .= sprintf(
			'<li><a href="%1$s"%2$s aria-label="%3$s"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%4$s</svg></a></li>',
			$url,
			$is_email ? '' : ' target="_blank" rel="noopener me"',
			esc_attr( $is_email ? 'Email us' : $networks[ $network ]['label'] . ' (opens in a new tab)' ),
			$networks[ $network ]['icon'] // Static markup from drift_social_networks().
		);
	}

	return $items ? '<ul class="' . esc_attr( $class ) . '">' . $items . '</ul>' : '';
}
