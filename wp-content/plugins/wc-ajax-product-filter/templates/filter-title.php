<?php
/**
 * The filter title template.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/templates
 * @author     wptools.io
 */

$filter_key          = isset( $filter_key ) ? $filter_key : '';
$show_title          = isset( $show_title ) ? $show_title : '';
$filter_title        = isset( $filter_title ) ? $filter_title : '';
$help_text           = isset( $help_text ) ? $help_text : '';
$enable_accordion    = isset( $enable_accordion ) ? $enable_accordion : '';
$is_expanded         = isset( $is_expanded ) ? $is_expanded : '';
$accordion_header_id = isset( $accordion_header_id ) ? $accordion_header_id : '';
$accordion_panel_id  = isset( $accordion_panel_id ) ? $accordion_panel_id : '';
$show_clear_btn      = isset( $show_clear_btn ) ? $show_clear_btn : '';
$title_for           = isset( $title_for ) ? $title_for : '';

if ( ! $show_title ) {
	return;
}

$classes = 'wcapf-filter-title';

if ( $enable_accordion ) {
	$classes .= ' has-accordion';
}
?>

<h4 class="<?php echo esc_attr( $classes ); ?>">
	<span class="wcapf-filter-title-inner">
		<?php echo esc_html( $filter_title ); ?>

		<?php if ( $help_text ) : ?>
			<span class="wcapf-filter-tooltip" data-content="<?php echo esc_attr( $help_text ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="28" viewBox="0 0 24 28">
					<path
						d="M14 21.5v-3c0-0.281-0.219-0.5-0.5-0.5h-3c-0.281 0-0.5 0.219-0.5 0.5v3c0 0.281 0.219 0.5 0.5 0.5h3c0.281 0 0.5-0.219 0.5-0.5zM18 11c0-2.859-3-5-5.688-5-2.547 0-4.453 1.094-5.797 3.328-0.141 0.219-0.078 0.5 0.125 0.656l2.063 1.563c0.078 0.063 0.187 0.094 0.297 0.094 0.141 0 0.297-0.063 0.391-0.187 0.734-0.938 1.047-1.219 1.344-1.437 0.266-0.187 0.781-0.375 1.344-0.375 1 0 1.922 0.641 1.922 1.328 0 0.812-0.422 1.219-1.375 1.656-1.109 0.5-2.625 1.797-2.625 3.313v0.562c0 0.281 0.219 0.5 0.5 0.5h3c0.281 0 0.5-0.219 0.5-0.5v0c0-0.359 0.453-1.125 1.188-1.547 1.188-0.672 2.812-1.578 2.812-3.953zM24 14c0 6.625-5.375 12-12 12s-12-5.375-12-12 5.375-12 12-12 12 5.375 12 12z" />
				</svg>
			</span>
		<?php endif; ?>

		<?php do_action( 'wcapf_after_filter_title', $show_clear_btn, $title_for, $filter_key ); ?>
	</span>

	<?php if ( $enable_accordion ) : ?>
		<button
			type="button"
			id="<?php echo esc_attr( $accordion_header_id ); ?>"
			class="wcapf-filter-accordion-trigger"
			aria-controls="<?php echo esc_attr( $accordion_panel_id ); ?>"
			aria-expanded="<?php echo esc_attr( $is_expanded ); ?>"
		>
			<svg
				xmlns="http://www.w3.org/2000/svg"
				width="24px" height="24px"
				viewBox="0 0 24 24" fill="none">
				<path d="M4 8L12 16L20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"
					  stroke-linejoin="round" />
			</svg>
		</button>
	<?php endif; ?>
</h4>
