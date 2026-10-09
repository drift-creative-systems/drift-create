<?php
/**
 * First-run content: the Drift apps, a Home page, and the Home page's
 * modules. Loaded only by inc/upgrade.php. Everything here is editable
 * afterwards (Apps, Pages → Home).
 *
 * Blurbs for the apps other than Encore are first drafts written from the
 * app names — check them before sharing the site.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

/**
 * Creates the apps (skipping any that exist) and a static Home page.
 *
 * @return void
 */
function drift_create_seed(): void {
	$apps = [
		[
			'title'   => 'Drift: Encore',
			'slug'    => 'encore',
			'suite'   => 'music',
			'status'  => 'demo',
			'tagline' => 'Websites for bands, artists and labels — run from one Airtable base.',
			'excerpt' => 'A proper band website the band can update themselves. Gigs, releases, videos, press and merch live in a simple Airtable workspace; one click publishes the lot.',
			'content' => drift_create_encore_content(),
		],
		[
			'title'   => 'Greenroom',
			'slug'    => 'greenroom',
			'suite'   => 'music',
			'status'  => 'development',
			'tagline' => 'The artist\'s gig manager.',
			'excerpt' => 'Every show in one place for artists and their teams: dates, venues, set times, riders and travel, shared with the people who need them.',
			'content' => '<p>Greenroom keeps the working side of gigging in one place, so nothing lives in a dozen message threads.</p><p>It is in development. Register your interest below and we\'ll be in touch when it\'s ready to try.</p>',
		],
		[
			'title'   => 'Stageside',
			'slug'    => 'stageside',
			'suite'   => 'music',
			'status'  => 'development',
			'tagline' => 'For venues and promoters.',
			'excerpt' => 'Plan the programme, hold dates, advance artists and keep everyone briefed, from first offer to curfew.',
			'content' => '<p>Stageside is the venue and promoter side of the Drift suite, built to talk to Greenroom and Encore.</p><p>It is in development. Register your interest below.</p>',
		],
		[
			'title'   => 'Wristband',
			'slug'    => 'wristband',
			'suite'   => 'music',
			'status'  => 'development',
			'tagline' => 'The festival app.',
			'excerpt' => 'Line-ups, stage times, maps and live updates for festival-goers, managed by the festival team without a developer on call.',
			'content' => '<p>Wristband gives a festival its own app, with the line-up and timings managed by the people running it.</p><p>It is in development. Register your interest below.</p>',
		],
		[
			'title'   => 'Wristband Fan',
			'slug'    => 'wristband-fan',
			'suite'   => 'music',
			'status'  => 'soon',
			'tagline' => 'Your gigs, tracked.',
			'excerpt' => 'Keep track of every gig you\'ve been to and every show you\'ve got tickets for, all in one place.',
			'content' => '<p>Wristband Fan is a gig diary for music fans: the shows coming up, and the ones you\'ll be talking about for years.</p><p>Coming soon. Register your interest below.</p>',
		],
	];

	foreach ( $apps as $i => $app ) {
		if ( get_page_by_path( $app['slug'], OBJECT, 'drift_app' ) ) {
			continue;
		}
		$id = wp_insert_post( [
			'post_type'    => 'drift_app',
			'post_status'  => 'publish',
			'post_title'   => $app['title'],
			'post_name'    => $app['slug'],
			'post_excerpt' => $app['excerpt'],
			'post_content' => $app['content'],
			'menu_order'   => $i,
		] );
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, 'drift_tagline', $app['tagline'] );
			update_post_meta( $id, 'drift_status', $app['status'] );
			update_post_meta( $id, 'drift_suite', $app['suite'] );
		}
	}

	if ( 'page' !== get_option( 'show_on_front' ) ) {
		$home = get_page_by_path( 'home' ) ?: get_post( (int) wp_insert_post( [ 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Home', 'post_name' => 'home' ] ) );
		if ( $home ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home->ID );
		}
	}
}

/**
 * Gives a page the modules that recreate the original hardcoded home page:
 * hero, app suites, Encore spotlight, about, contact. Needs ACF Pro.
 *
 * @param int $page_id Page ID.
 * @return void
 */
function drift_create_seed_home_modules( int $page_id ): void {
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$encore    = get_page_by_path( 'encore', OBJECT, 'drift_app' );
	$encore_id = $encore ? $encore->ID : 0;

	$rows = [
		[
			'acf_fc_layout' => 'hero',
			'section_id'    => '',
			'eyebrow'       => '',
			'title'         => 'Systems for creative work.',
			'lead'          => 'Apps, websites and tools for musicians, venues, festivals and the agencies that build for them. Each one designed to be run by the people who use it.',
			'buttons'       => [
				[ 'link' => [ 'title' => 'See the apps', 'url' => '#apps', 'target' => '' ], 'style' => 'dark' ],
				[ 'link' => [ 'title' => 'Book a demo', 'url' => '#contact', 'target' => '' ], 'style' => 'line' ],
			],
			'show_mark'     => 1,
		],
		[
			'acf_fc_layout' => 'app_suites',
			'section_id'    => 'apps',
			'featured_app'  => $encore_id,
			'suites'        => [
				[ 'suite' => 'music', 'heading' => 'From the stage to the crowd.', 'lead' => 'One family of apps for bands, venues, festivals and fans, built to work together.' ],
				[ 'suite' => 'agency', 'heading' => '', 'lead' => '' ],
				[ 'suite' => 'seo', 'heading' => '', 'lead' => '' ],
			],
		],
	];

	if ( $encore_id ) {
		$rows[] = [
			'acf_fc_layout' => 'spotlight',
			'section_id'    => 'encore',
			'app'           => $encore_id,
			'eyebrow'       => 'Spotlight',
			'heading'       => '',
			'lead'          => '',
			'steps'         => [
				[ 'title' => 'Edit in Airtable.', 'text' => 'Gigs, releases, photos and bio, in a simple workspace the band already understands.' ],
				[ 'title' => 'Press Publish.', 'text' => 'One button. No WordPress login, no developer.' ],
				[ 'title' => 'The site updates.', 'text' => 'In seconds, with proper event and album data for Google.' ],
			],
			'link_label'    => 'How Encore works',
			'show_demo'     => 1,
			'show_flow'     => 1,
		];
	}

	$rows[] = [
		'acf_fc_layout' => 'split_text',
		'section_id'    => 'about',
		'heading'       => 'Built in Devon by people who make websites for a living.',
		'body'          => '<p>Drift is made by <strong>The Bonsai Digital Collective</strong>, a WordPress studio looking after 50+ client websites. We kept meeting the same problem: great content stuck behind a website nobody could easily update.</p><p>So every Drift product starts from one rule: the people closest to the work should be able to run it themselves.</p>',
	];
	$rows[] = [
		'acf_fc_layout' => 'contact',
		'eyebrow'       => '',
		'heading'       => '',
		'lead'          => '',
	];

	update_field( 'field_drift_page_modules', $rows, $page_id );
}

/**
 * Body copy for the Encore app page.
 *
 * @return string HTML.
 */
function drift_create_encore_content(): string {
	return <<<'HTML'
<h2>A website the band actually runs</h2>
<p>Most band websites go stale because every change means emailing someone. Encore turns that around. The band keeps their gigs, releases and news in a simple Airtable workspace they already know how to use. When they're happy, they press <strong>Publish website</strong> and the site updates within seconds.</p>

<h2>How it works</h2>
<ol>
<li><strong>Edit in Airtable.</strong> Add a gig, drop in new artwork, update the bio, from a phone in the van if needs be.</li>
<li><strong>Press Publish.</strong> One button. No logins to WordPress, no developer.</li>
<li><strong>The site updates.</strong> Pages, images and listings refresh, and search engines get proper event and album data.</li>
</ol>

<h2>What's in it</h2>
<ul>
<li><strong>Tour dates</strong> with ticket links, sold-out and cancelled states, and past shows archived automatically.</li>
<li><strong>Discography</strong> with artwork, streaming links, tracklists, lyrics and pre-save for upcoming releases.</li>
<li><strong>Band members, press quotes, photo gallery and videos</strong>, with videos that load nothing from YouTube until someone presses play.</li>
<li><strong>Merch</strong> linking out to the band's own store.</li>
<li><strong>Booking enquiries and mailing list sign-ups</strong>, saved straight into Airtable and emailed to the band.</li>
<li><strong>An EPK page</strong> for promoters and press, with a downloadable press kit.</li>
<li><strong>Built-in search data</strong> (schema.org events and albums) so gigs and records show up properly in Google.</li>
<li><strong>The band's own look</strong>: their colours, fonts and logo, on their own domain.</li>
</ul>

<h2>Who it's for</h2>
<p>Independent bands and solo artists who want a site they're proud of without the upkeep, and labels and management companies looking after a roster.</p>
HTML;
}
