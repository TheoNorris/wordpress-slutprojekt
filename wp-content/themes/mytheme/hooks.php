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

//removes shipping 
    add_filter( 'woocommerce_cart_needs_shipping', 'filter_cart_needs_shipping' );
    function filter_cart_needs_shipping( $needs_shipping ) {
        if ( is_cart() ) {
            $needs_shipping = false;
        }
        return $needs_shipping;
    }

    add_filter('woocommerce_breadcrumb_defaults', 'custom_change_breadcrumb_separator');

    function custom_change_breadcrumb_separator($defaults) {
        $defaults['delimiter'] = '<span> > </span>';
        return $defaults;
    }

    add_filter( 'woocommerce_result_count', 'custom_result_count_text' );

    function custom_result_count_text( $result_count ) {
    // Get the total number of products
    $total_products = WC()->query->found_posts;

    // Replace the default text with custom text
    $custom_text = sprintf( 'Selected products: %d', $total_products );
    
    return '<p class="woocommerce-result-count">' . $custom_text . '</p>';
}