<?php
/**
 * The template for the reset button.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/templates/public
 * @author     wptools.io
 */

/**
 * @var string $btn_label   The button label.
 * @var string $show_always Whether to show the reset filters button if no filter is applied.
 */

if ( ! WCAPF_Helper::found_wcapf() ) {
	return;
}

$helper = new WCAPF_Helper;

$all_filters = $helper::get_active_filters_data();

$btn_label   = isset( $btn_label ) ? $btn_label : '';
$show_always = isset( $show_always ) && $show_always;

$unique_id = wp_unique_id( 'rf-' );
$classes   = array( 'wcapf-reset-filters', 'wcapf-reset-filters-' . $unique_id );

$continue = true;

if ( ! $all_filters && ! $show_always ) {
	$continue = false;
}
?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-id="<?php echo esc_attr( $unique_id ); ?>">
	<?php if ( $continue ): ?>
		<div class="wcapf-filter">
			<?php echo $helper::get_reset_button_markup( $btn_label ); ?>
		</div>
	<?php endif; ?>
</div>
