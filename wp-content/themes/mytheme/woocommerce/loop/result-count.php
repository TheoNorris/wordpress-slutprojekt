<?php
/**
 * Result Count
 *
 * Shows text: Showing x - x of x results.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/result-count.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woo.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p class="woocommerce-result-count">
    <?php
    // phpcs:disable WordPress.Security
    if ( 1 === intval( $total ) ) {
        _e( 'Showing the single result', 'woocommerce' );
    } elseif ( $total <= $per_page || -1 === $per_page ) {
        /* translators: %d: total results */
        printf( _n( 'Selected Products: <label> %d</label>', 'Selected Products: <label> %d</label>', $total, 'woocommerce' ), $total );
    } else {
        /* translators: %d: number of products per page */
        printf( __( 'Selected Products per page: <label> %d</label>', 'woocommerce' ), $per_page );
    }
    // phpcs:enable WordPress.Security
    ?>
</p>