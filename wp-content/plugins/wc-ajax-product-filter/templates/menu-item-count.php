<?php
/**
 * The template for displaying the walker menu item count.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/templates/
 * @author     wptools.io
 */

/**
 * @var int $count
 */

$for_screen_reader = sprintf(
	_n( '%d product', '%d products', $count, 'wc-ajax-product-filter' ),
	number_format_i18n( $count )
);
?>

<span class="wcapf-nav-item-count"><span aria-hidden="true"><?php echo esc_html( $count ); ?></span><span class="screen-reader-text"><?php echo esc_html( $for_screen_reader ); ?></span></span>
