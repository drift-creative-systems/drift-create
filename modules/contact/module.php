<?php
/**
 * Module: Contact — the demo / enquiry form. Empty copy fields fall back to
 * Drift Settings → Contact. Use once per page (it's always #contact).
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

// On an app page, preselect that app in the "Interested in" list.
drift_contact_form( is_singular( 'drift_app' ) ? get_the_title() : '', [
	'eyebrow' => (string) get_sub_field( 'eyebrow' ),
	'heading' => (string) get_sub_field( 'heading' ),
	'lead'    => (string) get_sub_field( 'lead' ),
] );
