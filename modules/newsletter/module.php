<?php
/**
 * Module: Newsletter — heading and text beside the mailing list sign-up
 * (Brevo double opt-in, Drift Settings → Newsletter). Hidden until Brevo is
 * set up; logged-in admins see a note saying so.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$heading = (string) get_sub_field( 'heading' );
if ( '' === $heading ) {
	return;
}
if ( ! drift_newsletter_ready() ) {
	if ( current_user_can( 'edit_theme_options' ) ) {
		echo '<p class="wrap notice notice--err">Newsletter module hidden: add the Brevo API key and double opt-in template ID in Drift Settings → Newsletter. Only admins see this.</p>';
	}
	return;
}
$eyebrow = (string) get_sub_field( 'eyebrow' );
$lead    = (string) get_sub_field( 'lead' );
// The form redirects back to this anchor, so it always needs one. Not "newsletter": the footer uses that.
$anchor = sanitize_title( (string) get_sub_field( 'section_id' ) ) ?: 'signup';
?>
<section id="<?php echo esc_attr( $anchor ); ?>" class="section newsletter">
	<div class="wrap newsletter__grid">
		<div>
			<?php if ( $eyebrow ) : ?><p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
			<h2 class="h2"><?php echo esc_html( $heading ); ?></h2>
			<?php if ( $lead ) : ?><p class="lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
		</div>
		<?php drift_newsletter_form( $anchor ); ?>
	</div>
</section>
