<?php
/**
 * Roadmap board: Now / Next / Later columns and a "Recently shipped" list.
 * Used by the Roadmap module and app pages. Columns without items are left
 * out; nothing is printed when there's nothing to show. Styles live in
 * modules/roadmap/module.css (enqueued for both, see drift_layouts_for_request()).
 *
 * $args['items']    array     Items from drift_app_roadmap() / drift_roadmap_items().
 * $args['stages']   string[]  Board stages to show (default now, next, later).
 * $args['shipped']  int       How many shipped items to list (0 = none).
 * $args['limit']    int       Items per column (0 = all).
 * $args['show_app'] bool      Show which app each item belongs to (Roadmap module).
 *
 * @package Drift_Create
 */

defined( 'ABSPATH' ) || exit;

$labels   = drift_roadmap_stages();
$by_stage = drift_roadmap_by_stage( (array) ( $args['items'] ?? [] ) );
$stages   = array_intersect( (array) ( $args['stages'] ?? [ 'now', 'next', 'later' ] ), [ 'now', 'next', 'later' ] );
$limit    = max( 0, (int) ( $args['limit'] ?? 0 ) );
$show_app = ! empty( $args['show_app'] );

$columns = [];
foreach ( $stages as $stage ) {
	if ( $by_stage[ $stage ] ) {
		$columns[ $stage ] = $limit ? array_slice( $by_stage[ $stage ], 0, $limit ) : $by_stage[ $stage ];
	}
}
$shipped = array_slice( $by_stage['shipped'], 0, max( 0, (int) ( $args['shipped'] ?? 0 ) ) );
if ( ! $columns && ! $shipped ) {
	return;
}

// App details once per app, not once per item.
$apps = [];
foreach ( array_merge( $shipped, ...array_values( $columns ) ) as $item ) {
	$apps[ $item['app'] ] ??= drift_app_meta( $item['app'] );
}
?>
<div class="roadmap">
	<?php if ( $columns ) : ?>
		<div class="roadmap__cols">
			<?php foreach ( $columns as $stage => $items ) : ?>
				<div class="roadmap__col roadmap__col--<?php echo esc_attr( $stage ); ?>" data-filter-group>
					<h3 class="roadmap__stage"><?php echo esc_html( $labels[ $stage ] ); ?></h3>
					<ul class="roadmap__list">
						<?php foreach ( $items as $item ) : ?>
							<li class="roadmap__item" data-filter-tags="<?php echo esc_attr( $apps[ $item['app'] ]['suite'] ); ?>" style="--app: <?php echo esc_attr( $apps[ $item['app'] ]['accent'] ); ?>">
								<?php if ( $show_app ) : ?>
									<p class="roadmap__app">
										<?php echo drift_app_icon( $item['app'], 'roadmap__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
										<a href="<?php echo esc_url( drift_app_roadmap_url( $item['app'] ) ); ?>"><?php echo esc_html( get_the_title( $item['app'] ) ); ?></a>
									</p>
								<?php endif; ?>
								<p class="roadmap__title"><?php echo esc_html( $item['item'] ); ?></p>
								<?php if ( $item['note'] ) : ?><p class="roadmap__note"><?php echo esc_html( $item['note'] ); ?></p><?php endif; ?>
								<?php if ( $item['target'] ) : ?><p class="roadmap__target">Target: <?php echo esc_html( $item['target'] ); ?></p><?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( $shipped ) : ?>
		<div class="roadmap__shipped" data-filter-group>
			<h3 class="roadmap__stage">Recently shipped</h3>
			<ul class="roadmap__log">
				<?php foreach ( $shipped as $item ) : ?>
					<li class="roadmap__log-item" data-filter-tags="<?php echo esc_attr( $apps[ $item['app'] ]['suite'] ); ?>" style="--app: <?php echo esc_attr( $apps[ $item['app'] ]['accent'] ); ?>">
						<?php if ( $item['shipped'] ) : ?>
							<time class="roadmap__date" datetime="<?php echo esc_attr( mysql2date( 'Y-m-d', $item['shipped'] ) ); ?>"><?php echo esc_html( mysql2date( 'j M Y', $item['shipped'] ) ); ?></time>
						<?php endif; ?>
						<?php if ( $show_app ) : ?>
							<a class="roadmap__log-app" href="<?php echo esc_url( drift_app_roadmap_url( $item['app'] ) ); ?>"><?php echo esc_html( get_the_title( $item['app'] ) ); ?></a>
						<?php endif; ?>
						<span class="roadmap__log-text"><?php echo esc_html( $item['item'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</div>
