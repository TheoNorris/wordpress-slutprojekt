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


    

// Change text pon Home page on Add to cart btn
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

function display_review_statistics_with_bars_labels_and_stars() {
    global $product;

    // Get average rating and review count
    $average_rating = $product->get_average_rating();
    $review_count = $product->get_review_count();

    // Calculate the width of the filled stars
    $width = ($average_rating / 5) * 100;

    // Get the number of full stars
    $full_stars = floor($average_rating);
    // Calculate the percentage of the last star needed
    $percentage_last_star = ($average_rating - $full_stars) * 100;

    // Display review statistics with bars, labels, and star images
    echo '<div class="review-statistics">';
    echo '<div class="avrg">';
    echo '<h2>' . $average_rating . ' </h2> <p> of ' . $review_count . ' reviews</p>';
    echo '<div class="rating" style="position: relative; width: 100%;">'; // Apply flexbox here
    // Full stars container
    echo '<div class="stars-container" style="position: relative; top: 0; left: 0; width: ' . $width . '%;">';

    // Output full stars
    for ($i = 1; $i <= $full_stars; $i++) {
        echo '<img src="' . get_template_directory_uri() . '/resources/images/full_star.png" alt="Full Star" style="position: relative;">';
    }

    // Partial star
    if ($percentage_last_star > 0 && $full_stars < 5) {
        echo '<img src="' . get_template_directory_uri() . '/resources/images/full_star.png" style="clip-path: inset(0 ' . (100 - $percentage_last_star) . '% 0 0); width: 10%; position: absolute; top: 0; left: ' . ($width - 50). '%;" alt="Partial Star">';
    }
    
    echo '</div>'; // Close stars container

    // Empty stars container
    echo '<div class="empty-stars-container" style="position: absolute; top: 0; left: 0; width: ' . $width . '%;">';
    // Output empty stars
    for ($i = 1; $i <= 5; $i++) {
        echo '<img src="' . get_template_directory_uri() . '/resources/images/empty_star.png" alt="Empty Star">';
    }
    echo '</div>'; // Close empty stars container
    echo '</div>'; // Close stars container
    echo '</div>';
    // Rating bars
    echo '<ul class="rating-bars">';
$rating_counts = array(
    5 => $product->get_rating_count(5),
    4 => $product->get_rating_count(4),
    3 => $product->get_rating_count(3),
    2 => $product->get_rating_count(2),
    1 => $product->get_rating_count(1)
);
$percentage_distribution = array();

$color = 'color: #FFB547;';
foreach ($rating_counts as $rating => $count) {
    if ($review_count != 0) {
        $percentage = ($count / $review_count) * 100;
    } else {
        // If $review_count is zero, set percentage to zero to avoid error
        $percentage = 0;
    }
    $percentage_distribution[$rating] = round($percentage, 2);

    $class = 'bar';
    if ($rating == 5) {
        $class .= ' excellent';
        $label = 'Excellent';
        $filled = $color . ' width: 90%;';
       /*  $excellent_count += $count;  */
    } elseif ($rating == 4) {
        $class .= ' good';
        $label = 'Good';
        $filled = $color . ' width: 70%;';
    } elseif ($rating >= 3) {
        $class .= ' average';
        $label = 'Average';
        $filled = $color . ' width: 50%;';
    } elseif ($rating >= 2) {
        $class .= ' below-average';
        $label = 'Below Average';
        $filled = $color . ' width: 50%;';
    } elseif ($rating >= 1) {
        $class .= ' poor';
        $label = 'Poor';
        $filled = $color . ' width: 50%;';
    }
    echo '<li class="' . $class . '">';

    // Adjusting the width of the outer <div> to accommodate the text label and the bar
    echo '<div style="width:20%; display: inline-block;">';
    echo $label;
    echo '</div>';
    
    if ($rating <= 5) {
        echo '<div style="background-color: grey; height: 5px; width: 70%; border-radius: 10px;">
            <div style="background-color: #FFB547; height: 100%;' . $filled . ' border-radius: 10px;"></div>
        </div>';
    }
    
    echo '<span style="float:right">' . $rating_counts[$rating] . '</span>';
    echo '</li>';
    
}
echo '</ul>';

    echo '</div>'; // Close review-statistics div
}

add_action( 'woocommerce_after_shop_loop_item_title', 'add_custom_rating_pro', 5 );

function add_custom_rating_pro() {
    global $product;

    if ( $product->get_review_count() > 0 ) {
        $average_rating = $product->get_average_rating();
        $rating_count = $product->get_review_count();

        $output = '<div class="woocommerce-product-rating">';

        for ($i = 1; $i <= 5; $i++) {
     
            if ($i <= $average_rating) {
            
                $output .= '<img src="' . get_template_directory_uri() . '/resources/images/full_star.png" alt="fullstar">';
            } else if ($i - 0.5 <= $average_rating) {
            
                $output .= '<img src="' . get_template_directory_uri() . '/resources/images/half_star.png" alt="halfstar">';
            } else {
         
                $output .= '<img src="' . get_template_directory_uri() . '/resources/images/empty_star.png" alt="emptystar">';
            }
        }

        $output .= '</div>';
        echo $output;
    }
}

add_filter( 'woocommerce_get_price_html', 'display_sale_price_with_red_color', 10, 2 );

function display_sale_price_with_red_color( $price_html, $product ) {
    if ( $product->is_on_sale() ) {
        $sale_price_html = wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_sale_price() ) ), wc_get_price_to_display( $product ) ) . $product->get_price_suffix();
        // Add inline CSS to color the sale price red
        $sale_price_html = '<span class="sale">' . $sale_price_html . '</span>';
        return $sale_price_html;
    }

    return $price_html;
}

function replace_select_options_text( $translated_text, $text, $domain ) {
    if ( 'woocommerce' === $domain && 'Select options' === $text ) {
        $translated_text = 'Buy Now';
    }
    return $translated_text;
}
add_filter( 'gettext', 'replace_select_options_text', 20, 3 );

// For authenticated users
add_action("wp_ajax_mytheme_getbyajax", "mytheme_getbyajax");

// For non-authenticated users
add_action("wp_ajax_nopriv_mytheme_getbyajax", "mytheme_getbyajax");