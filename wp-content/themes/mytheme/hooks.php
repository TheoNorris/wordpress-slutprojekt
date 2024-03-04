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


    

// Change text pon Home page on Add ti cart btn
add_filter( 'woocommerce_product_add_to_cart_text', 'custom_add_to_cart_text' );

function custom_add_to_cart_text( $text ) {
    if ( is_front_page() && $text === 'Add to cart' ) {     
        $text = 'Buy Now';
    }
    return $text;
}

function modify_shipping_method_full_label($full_label, $method) {
    // Modify the full label text as needed
    $modified_full_label = str_replace(':', '', $full_label);
    
    // Return the modified full label
    return $modified_full_label;
}

// Add filter hook to modify the shipping method full label
add_filter('woocommerce_cart_shipping_method_full_label', 'modify_shipping_method_full_label', 10, 2);


// Remove or modify placeholders for address fields
function modify_checkout_address_fields($fields) {
    // Unset placeholder values for address fields
    foreach ($fields as $field_key => $field) {
        if (isset($field['placeholder'])) {
            $fields[$field_key]['placeholder'] = '';
        }
    }
    return $fields;
}
add_filter('woocommerce_default_address_fields', 'modify_checkout_address_fields');

function remove_checkout_email_default_value($value, $input) {
    if ($input === 'billing_email') {
        return '';
    }
    return $value;
}
add_filter('woocommerce_checkout_get_value', 'remove_checkout_email_default_value', 10, 2);

add_filter('woocommerce_package_rates', 'restrict_shipping_options', 10, 2);

// RESTRICT SHIPPING OPTIONS
function restrict_shipping_options($rates, $package) {
    
    $free_shipping_available = false;
    foreach ($rates as $rate) {
        if ($rate->method_id === 'free_shipping') {
            $free_shipping_available = true;
            break;
        }
    }

    if ($free_shipping_available) {
        foreach ($rates as $key => $rate) {
            if ($rate->method_id !== 'free_shipping') {
                unset($rates[$key]);
            }
        }
    }

    return $rates;
}

if ( ! function_exists( 'wmsc_step_content_payment_modified' ) ) {
    function wmsc_step_content_payment_modified() {
        echo '<h3 id="payment_heading">' . __( 'Payment', 'woocommerce' ) . '</h3>';

        // Include the order summary
        echo '<div class="order-summary">';
        echo '<h4>' . __( 'Order Summary', 'your-text-domain' ) . '</h4>';
        // Add code to display order summary here
        echo '</div>';

        do_action( 'wpmc-woocommerce_checkout_payment' );
        do_action( 'woocommerce_checkout_after_order_review' );
    }
}
add_action( 'wmsc_step_content_payment_modified', 'wmsc_step_content_payment_modified' );