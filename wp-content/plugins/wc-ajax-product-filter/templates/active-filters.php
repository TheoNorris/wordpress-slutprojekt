<?php
/**
 * The template to show the active filters.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/templates/public
 * @author     wptools.io
 */

/**
 * @var string $title                The title.
 * @var string $show_title           Determines if we show the title.
 * @var string $layout               Determines the active filters' layout, possible values are simple, extended.
 * @var string $empty_message        No filter applied message.
 * @var string $clear_all_btn_label  The clear all button label.
 * @var string $clear_all_btn_layout The clear all button label.
 * @var string $show_clear_btn       Whether to show the clear filter button in heading or not.
 */

if ( ! WCAPF_Helper::found_wcapf() ) {
	return;
}

$layout = ! empty( $layout ) ? $layout : 'simple';
$title  = ! empty( $title ) ? $title : '';

$helper = new WCAPF_Helper;

$all_filters = $helper::get_active_filter_items( $layout );
$total       = count( $all_filters );

$clear_all_btn_label  = ! empty( $clear_all_btn_label ) ? $clear_all_btn_label : '';
$clear_all_btn_layout = ! empty( $clear_all_btn_layout ) ? $clear_all_btn_layout : 'block';
$show_clear_btn       = ! empty( $show_clear_btn );
$empty_message        = ! empty( $empty_message ) ? $empty_message : '';

if ( 'extended' === $layout ) {
	$clear_all_btn_layout = 'block';
}

$unique_id = wp_unique_id( 'af-' );
$classes   = array( 'wcapf-active-filters', 'wcapf-active-filters-' . $unique_id );
$classes[] = 'layout-' . $layout;
$classes[] = 'clear-all-btn-layout-' . $clear_all_btn_layout;

$reset_btn_class = 'wcapf-reset-filters-btn';

$continue = true;

if ( ! $all_filters && ! $empty_message ) {
	$continue = false;
}

$show_title = true;

if ( ! $title ) {
	$show_title = false;
}

if ( ! $show_title ) {
	$show_clear_btn = false;
}

$inner_classes = array( 'wcapf-filter' );

if ( $show_clear_btn && $all_filters ) {
	$inner_classes[] = 'filter-active';
}
?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-id="<?php echo esc_attr( $unique_id ); ?>">
	<?php if ( $continue ): ?>
		<div class="<?php echo esc_attr( implode( ' ', $inner_classes ) ); ?>">
			<?php
			WCAPF_Template_Loader::get_instance()->load(
				'filter-title',
				array(
					'show_title'     => $show_title,
					'filter_title'   => $title,
					'title_for'      => 'active-filters',
					'show_clear_btn' => $show_clear_btn,
				)
			);
			?>

			<div class="wcapf-filter-inner">
				<div class="wcapf-active-filter-items-wrapper">
					<?php if ( $all_filters ) : ?>
						<?php if ( 'simple' === $layout ) : ?>
							<div class="wcapf-active-filter-items">
								<?php
								$index = 0;
								$class = '';

								foreach ( $all_filters as $filter_data ) {
									$index ++;

									if ( $index === $total ) {
										$class = 'last-item';
									}

									echo $helper::get_active_filters_markup( $filter_data, $class );
								}

								if ( 'inline' === $clear_all_btn_layout ) {
									echo '<div class="wcapf-reset-filters-btn-wrapper">';
									echo $helper::get_reset_button_markup( $clear_all_btn_label, $reset_btn_class );
									echo '</div>';
								}
								?>
							</div>
						<?php else: ?>
							<?php
							foreach ( $all_filters as $filter_key => $filter_data ) {
								$filter_id = isset( $filter_data['filter_id'] ) ? $filter_data['filter_id'] : '';

								echo '<div class="wcapf-active-filter-group">';

								if ( $filter_id ) {
									echo '<h5>' . get_the_title( $filter_id ) . '</h5>';
								}

								echo '<div class="active-items">';

								echo $helper::get_active_filters_markup( $filter_data );

								echo '</div></div>';
							}
							?>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( ! $all_filters && $empty_message ) : ?>
						<div class="empty-filter-message"><?php echo esc_html( $empty_message ); ?></div>
					<?php endif; ?>

					<?php
					// 'Clear All' button.
					if ( $all_filters && $clear_all_btn_label && 'inline' !== $clear_all_btn_layout && ! $show_clear_btn ) {
						echo $helper::get_reset_button_markup( $clear_all_btn_label, $reset_btn_class );
					}
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
