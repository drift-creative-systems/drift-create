<?php
/**
 * Module: Contact — the demo / enquiry form. Empty copy fields fall back to
 * Drift Settings → Contact. Use once per page (it's always #contact).
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

drift_contact_form( '', [
	'eyebrow' => (string) get_sub_field( 'eyebrow' ),
	'heading' => (string) get_sub_field( 'heading' ),
	'lead'    => (string) get_sub_field( 'lead' ),
] );
