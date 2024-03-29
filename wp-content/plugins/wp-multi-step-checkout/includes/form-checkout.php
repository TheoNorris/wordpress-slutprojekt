<?php
/**
 * Checkout Form
 *
 * This is an overridden copy of the woocommerce/templates/checkout/form-checkout.php file.
 *
 * @package WPMultiStepCheckout
 */

defined( 'ABSPATH' ) || exit;

// check the WooCommerce MultiStep Checkout options
$options = get_option('wmsc_options');
require_once 'settings-array.php';
if ( !is_array($options) || count($options) === 0 ) {
    $defaults = get_wmsc_settings();
    $options = array();
    foreach($defaults as $_key => $_value ) {
        $options[$_key] = $_value['value'];
    }
} 
$options = array_map('stripslashes', $options);

// Use the WPML values instead of the ones from the admin form
if ( isset($options['t_wpml']) && $options['t_wpml'] == 1 ) {
    $defaults = get_wmsc_settings();
    foreach($options as $_key => $_value ) {
        if( substr($_key, 0, 2) == 't_' && $_key != 't_sign') {
            $options[$_key] = $defaults[$_key]['value'];
        }
    }
}

if ( !isset($options['c_sign']) ) $options['c_sign'] = '&';

// Get the steps
$steps = get_wmsc_steps();

// Set the step titles
$steps['billing']['title']  = $options['t_billing'];
$steps['shipping']['title'] = $options['t_shipping'];
$steps['review']['title']   = $options['t_order'];
$steps['payment']['title']  = $options['t_payment'];


// check the WooCommerce options
$is_registration_enabled = version_compare('3.0', WC()->version, '<=') ? $checkout->is_registration_enabled() : get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) == 'yes'; 
$has_checkout_fields = version_compare('3.0', WC()->version, '<=') ? $checkout->get_checkout_fields() : (is_array($checkout->checkout_fields) && count($checkout->checkout_fields) > 0 );
$show_login_step = ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) ? false : true;
$stop_at_login = ( ! $is_registration_enabled && $checkout->is_registration_required() && ! is_user_logged_in() ) ? true : false;
$checkout_url = apply_filters( 'woocommerce_get_checkout_url', version_compare( '2.5', WC()->version, '<=' ) ? wc_get_checkout_url() : WC()->cart->get_checkout_url() );

// Both options disabled for "Guest" on the WP Admin -> WooCommerce -> Settings -> Accounts & Privacy page
if ( ! $is_registration_enabled && $checkout->is_registration_required() && ! is_user_logged_in() && ! $show_login_step) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

// Swap the Payment and Review steps for german shops
$swap_payment_review = ( class_exists('WooCommerce_Germanized') || class_exists('Woocommerce_German_Market')) ? true : false;
$swap_payment_review = apply_filters('wpmc_swap_payment_review', $swap_payment_review);
if ( $swap_payment_review ) {
    $tmp = $steps['payment']['position'];
    $steps['payment']['position'] = $steps['review']['position'];
    $steps['review']['position'] = $tmp;
} 

// Disabled "Show the Shipping step" option on Multi-Step Checkout -> General Settings page 
if ( !$options['show_shipping_step'] ) {
    unset($steps['shipping']);
    $options['unite_billing_shipping'] = false;
    $steps['billing']['sections'][] = 'woocommerce_checkout_after_customer_details';
}

// Enabled "Show the Order and the Payment steps together" option on Multi-Step Checkout -> General Settings page 
if ( $options['unite_order_payment']) {
    $steps['review']['title'] = $options['t_order'] . ' '.esc_html($options['c_sign']).' ' . $options['t_payment']; 
    $steps['review']['class'] = $steps['review']['class'] . ' ' . $steps['payment']['class'];
    $steps['review']['sections'] = array('review', 'payment');
    if ( $swap_payment_review ) {
        $steps['review']['sections'] = array('payment', 'review');
    }
    unset($steps['payment']);
}

// Enabled "Show the Order and the Payment steps together" option on Multi-Step Checkout -> General Settings page 
if ( $options['unite_billing_shipping'] && $options['show_shipping_step'] ) {
    $steps['billing']['title'] = $options['t_billing'] . ' '.esc_html($options['c_sign']).' ' . $options['t_shipping']; 
    $steps['billing']['class'] = $steps['billing']['class'] . ' ' . $steps['shipping']['class'];
    $steps['billing']['sections'] = array('billing', 'shipping');
    unset($steps['shipping']);
}

// No checkout fields within the $checkout object
if ( !$has_checkout_fields) {
    unset($steps['shipping']);
}

// Pass the steps through a filter
$steps = apply_filters('wpmc_modify_steps', $steps);

// Sort the steps
uasort($steps, 'wpmc_sort_by_position');

// show the tabs
include dirname(__FILE__) . '/form-tabs.php';

do_action( 'wpmc_after_step_tabs' );

?>

<div style="clear: both;"></div>

<?php wc_print_notices(); ?>

<div style="clear: both;"></div>

<div class="wpmc-steps-wrapper">

<div id="checkout_coupon" class="woocommerce_checkout_coupon" style="display: none;">
	<?php do_action( 'wpmc-woocommerce_checkout_coupon_form', $checkout ); ?>
</div>

<div id="woocommerce_before_checkout_form" class="woocommerce_before_checkout_form" data-step="<?php echo apply_filters('woocommerce_before_checkout_form_step', 'step-review'); ?>" style="display: none;">
    <?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
</div>

<!-- Step: Login -->
<?php 
    if ( $show_login_step ) {
        wmsc_step_content_login($checkout, $stop_at_login); 
    }

    if ( $stop_at_login ) { 
        echo '</div>'; // closes the "wpmc-steps-wrapper" div 
        return false; 
    } 

    ?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( $checkout_url ); ?>" enctype="multipart/form-data">

<?php $first_step = ( $show_login_step ) ? '' : ' current';


foreach( $steps as $_id => $_step ) {

    echo '<!-- Step: '.$_step['title'].' -->'; 

	echo '<div class="wpmc-step-item '.$_step['class']. $first_step . '">';
    if ( isset($_step['sections'] ) ) {
        foreach ( $_step['sections'] as $_section ) {
            if ( strpos($_section, 'woocommerce_') === 0 ) {
                do_action( $_section );
            } else {
                do_action('wmsc_step_content_' . $_section);
            }
            if ( $_step['title'] === 'Payment' ) {
                echo '<div class="order-summary">';
                
                // Display Products
                do_action( 'woocommerce_review_order_before_cart_contents' );
            
                echo '<div class="order-products">';
                echo '<h2> Summary </h2>';
            
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            
                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                    echo '<div class="product-item">';
                    echo '<div class="product-thumbnail">';
                    
                    $thumbnail = $_product->get_image( 'woocommerce_thumbnail' );
                    if ( $thumbnail ) {
                        echo wp_kses_post( $thumbnail );
                    } else {
                        echo '<img src="' . wc_placeholder_img_src() . '" alt="Placeholder" />';
                    }
                    
                    echo '</div>'; // Close product-thumbnail
            
                    echo '<div class="product-details">';
                    echo '<p class="product-name">' . wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ) . '</p>';
                    echo '<p class="product-quantity"> <label>Qty:</label> ' . apply_filters( 'woocommerce_checkout_cart_item_quantity', sprintf( '%s', $cart_item['quantity'] ), $cart_item, $cart_item_key ) . '</p>';
                    echo '<p class="product-subtotal">' . apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ) . '</p>';
                    echo '</div>'; // Close product-details
            
                    echo '</div>'; // Close product-item
                    }
                    }
            
                    echo '</div>'; // Close order-products div
            
                
                    do_action( 'woocommerce_review_order_after_cart_contents' );
            
                    // Display Shipping Address
                    echo '<div class="order-address">';
                    echo '<h2>' . esc_html__( 'Address', 'woocommerce' ) . '</h2>';
                    echo '<div>';
                    echo '<p>' . WC()->customer->get_shipping_address_1() . ',</p>';
                    echo '<p>' . WC()->customer->get_shipping_city() . ', ' . WC()->customer->get_shipping_postcode() . '</p>';
                    echo '</div>';
                    echo '</div>'; // Close order-address div
                
                    // Display Subtotal
                    echo '<div class="order-subtotal">';
                    echo '<h2>' . esc_html__( 'Subtotal', 'woocommerce' ) . '</h2>';
                    echo '<p>' . WC()->cart->get_cart_subtotal() . '</p>';
                    echo '</div>'; // Close order-subtotal div
                
                    // Display Taxes
                    echo '<div class="order-taxes">';
                    echo '<h2>' . esc_html__( 'Estimated Tax', 'woocommerce' ) . '</h2>';
                    echo '<p>kr' . WC()->cart->get_taxes_total() . '</p>';
                    echo '</div>'; // Close order-taxes div
            
                     // Display Shipping Estimate
                    echo '<div class="order-shipping">';
                    echo '<h2>' . esc_html__( 'Estimate shipping & Handling', 'woocommerce' ) . '</h2>';
                    $shipping_total = WC()->cart->get_cart_shipping_total();
            
                    if ( 'Free!' !== $shipping_total ) {
                        $shipping_total = str_replace( 'Free!', 'kr', $shipping_total );
                    } else {
                        $shipping_total = str_replace( '!', '', $shipping_total );
                    }
            
                    echo '<p>' . $shipping_total . '</p>';
                    echo '</div>'; // Close order-shipping div
            
                
                    // Display Order Total
                    echo '<div class="order-total">';
                    echo '<h2>' . esc_html_e( 'Order Total', 'woocommerce' ) . '</h2>';
                    echo '<p>' . wc_price( WC()->cart->get_total( 'edit' ) ) . '</p>';
                    echo '</div>'; // Close order-total div
                
                    echo '</div>'; // Close order-summary div
                }
            
        }
    } else {
        do_action('wmsc_step_content_' . $_id);
    }
    
    echo '</div>';
	$first_step = '';

} ?>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</div>

<?php include dirname(__FILE__) . '/form-buttons.php'; ?>
