<?php
/* *******CART******* */

//change text on proceed to checkout btn
function woocommerce_button_proceed_to_checkout() {
	
    $new_checkout_url = WC()->cart->get_checkout_url();
    ?>
    <a href="<?php echo $new_checkout_url; ?>" class="checkout-button button alt wc-forward">
    
    <?php _e( 'Checkout', 'woocommerce' ); ?></a>
    
<?php
}

/* //removes shipping 
    add_filter( 'woocommerce_cart_needs_shipping', 'filter_cart_needs_shipping' );
    function filter_cart_needs_shipping( $needs_shipping ) {
        if ( is_cart() ) {
            $needs_shipping = false;
        }
        return $needs_shipping;
    } */


// Hook för att lägga till beräknad skatt i kundvagnen och kassan
add_action( 'woocommerce_cart_totals_before_shipping', 'display_estimated_tax', 20 );
/* add_action( 'woocommerce_review_order_before_order_total', 'display_estimated_tax', 20 );
 */
function display_estimated_tax() {
    $cart_tax = WC()->cart->get_cart_contents_tax();
    if ( $cart_tax > 0 ) {
        ?>
        <tr class="order-tax">
            <th><?php _e( 'Estimated Tax', 'your-textdomain' ); ?></th>
            <td data-title="<?php esc_attr_e( 'Estimated Tax', 'your-textdomain' ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
           
        </tr>
        <?php
    }
}


add_filter('woocommerce_breadcrumb_defaults', 'custom_change_breadcrumb_separator');

    function custom_change_breadcrumb_separator($defaults) {
        $defaults['delimiter'] = '<span> > </span>';
        return $defaults;
    }

