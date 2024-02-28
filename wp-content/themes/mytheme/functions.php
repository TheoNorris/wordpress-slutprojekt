<?php

if(!defined('ABSPATH')){
    exit;
}
require_once('vite.php');
require_once('ajax.php');
//initialize theme
require_once(get_template_directory() . '/init.php');

function mytheme_add_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );

/* remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 ); */


add_action('wp_enqueue_scripts', 'enqueue_woocommerce_scripts');

function enqueue_woocommerce_scripts() {
    if (function_exists('is_woocommerce') && is_woocommerce()) {
        wp_enqueue_script('wc-add-to-cart-variation');
    }
}




add_action('wp_ajax_update_cart_item_quantity', 'update_cart_item_quantity');
add_action('wp_ajax_nopriv_update_cart_item_quantity', 'update_cart_item_quantity');

function update_cart_item_quantity() {
    if (isset($_POST['cart_item_key']) && isset($_POST['new_quantity'])) {
        $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
        $new_quantity = (int)$_POST['new_quantity'];

        // Uppdatera varukorgen med den nya kvantiteten
        WC()->cart->set_quantity($cart_item_key, $new_quantity);

        // Ladda om varukorgen och rendera den på nytt
        wc_get_template('cart/cart.php');
    }
    wp_die();
}

