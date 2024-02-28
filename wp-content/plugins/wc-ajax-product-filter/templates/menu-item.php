<?php
/**
 * The template for displaying the walker menu item.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/templates
 * @author     wptools.io
 */

/**
 * @var string[] $item The multidimensional array of item data.
 */

$item_attr = '';

if ( $item['enable_search'] ) {
	$item_attr = ' data-label="' . esc_attr( $item['name'] ) . '"';
}
?>

<span class="wcapf-filter-item-label"<?php echo $item_attr; ?>>
	<span class="wcapf-nav-item-text"><?php echo wp_kses_post( $item['name'] ); ?></span>

	<?php
	if ( $item['show_count'] ) {
		WCAPF_Template_Loader::get_instance()->load( 'menu-item-count', array( 'count' => $item['count'] ) );
	}
	?>
</span>
