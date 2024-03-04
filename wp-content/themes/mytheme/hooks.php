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
/* ---------------------------------------------------------------------------------------------------- */

// Ta bort den korta beskrivningen före varianterna


// Lägg till den nya åtgärden för att flytta den korta beskrivningen efter varianterna
add_action('woocommerce_after_variations_table', 'move_short_description_after_variations');


function move_short_description_after_variations() {
    global $product;
    
    // Kontrollera om det är en variabel produkt
    if ($product && $product->is_type('variable')) {
        // Output the short description
        wc_get_template('single-product/short-description.php');
    }
}

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

/* ------------------------------------------------------------------------------------- */

add_action( 'woocommerce_before_single_product', 'bbloomer_woocommerce_short_description_truncate_read_more' );
  
function bbloomer_woocommerce_short_description_truncate_read_more() { 
   wc_enqueue_js('
      var show_char = 200;
      var ellipses = "... ";
      var content = $(".woocommerce-product-details__short-description").html();
      if (content.length > show_char) {
         var a = content.substring(0, show_char);
         var html = "<span class=\'truncated\'>" + a + ellipses + "<a class=\'read-more\'>Read more</a></p></span><span class=\'truncated\' style=\'display:none\'>" + content + "<a class=\'read-less\'>Read less</a></span>";
         $(".woocommerce-product-details__short-description").html(html);
      }
      $(".read-more").click(function(e) {
         e.preventDefault();
         $(".woocommerce-product-details__short-description .truncated").toggle();
      });
     $(".read-less").click(function(e) {
         e.preventDefault();
         $(".woocommerce-product-details__short-description .truncated").toggle();
      });
   ');
}
