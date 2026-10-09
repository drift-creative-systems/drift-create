<?php
/**
 * Module: Team — team members (wp-admin → Team) as cards: photo, name, role,
 * suite, a "Read bio" toggle (native <details>, no JS) and links. Shows
 * everyone in menu order unless specific members are picked; can be limited
 * to one suite.
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$members = drift_team_members( (array) get_sub_field( 'members' ), (string) get_sub_field( 'only_suite' ) );
if ( ! $members ) {
	return;
}
$columns = '4' === (string) get_sub_field( 'columns' ) ? '4' : '3';
$eyebrow = (string) get_sub_field( 'eyebrow' );
$heading = (string) get_sub_field( 'heading' );
$lead    = (string) get_sub_field( 'lead' );
?>
<section<?php drift_section_id(); ?> class="section team" style="<?php echo drift_accent_style( (string) get_sub_field( 'accent_suite' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>">
	<div class="wrap">
		<?php if ( $eyebrow || $heading || $lead ) : ?>
			<header class="suite__head">
				<?php if ( $eyebrow ) : ?><p class="eyebrow eyebrow--accent"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
				<?php if ( $heading ) : ?><h2 class="h2"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
				<?php if ( $lead ) : ?><p class="lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
			</header>
		<?php endif; ?>
		<div class="team__grid team__grid--<?php echo esc_attr( $columns ); ?>">
			<?php
			foreach ( $members as $member ) :
				$m        = drift_team_meta( $member->ID );
				$photo_id = (int) get_post_thumbnail_id( $member );
				?>
				<article class="member" style="--app: <?php echo esc_attr( $m['accent'] ); ?>">
					<div class="member__photo">
						<?php
						if ( $photo_id ) {
							// The name is printed right below, so a missing alt falls back to it.
							$alt = (string) get_post_meta( $photo_id, '_wp_attachment_image_alt', true );
							echo wp_get_attachment_image( $photo_id, 'medium_large', false, [
								'class'   => 'member__img',
								'alt'     => $alt ?: $member->post_title,
								'sizes'   => '(max-width: 620px) 100vw, (max-width: 960px) 50vw, 380px',
								'loading' => 'lazy',
							] );
						} else {
							// No photo: initials on the soft grey tile.
							$initials = implode( '', array_map( static fn( $part ) => mb_substr( $part, 0, 1 ), array_slice( preg_split( '/\s+/', trim( $member->post_title ) ), 0, 2 ) ) );
							echo '<span class="member__initials" aria-hidden="true">' . esc_html( mb_strtoupper( $initials ) ) . '</span>';
						}
						?>
					</div>
					<?php if ( $m['suite_label'] ) : ?><p class="member__suite"><?php echo esc_html( $m['suite_label'] ); ?></p><?php endif; ?>
					<h3 class="member__name"><?php echo esc_html( $member->post_title ); ?></h3>
					<?php if ( $m['role'] ) : ?><p class="member__role"><?php echo esc_html( $m['role'] ); ?></p><?php endif; ?>
					<?php if ( $m['bio'] ) : ?>
						<details class="member__bio">
							<summary>Read bio<span class="sr"> for <?php echo esc_html( $member->post_title ); ?></span></summary>
							<div class="member__bio-text"><?php echo wp_kses_post( wpautop( $m['bio'] ) ); ?></div>
						</details>
					<?php endif; ?>
					<?php if ( $m['links'] ) : ?>
						<ul class="member__links">
							<?php foreach ( $m['links'] as $link ) : ?>
								<li><a href="<?php echo esc_url( $link['url'], [ 'http', 'https', 'mailto', 'tel' ] ); ?>"<?php echo $link['target'] ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $link['title'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
