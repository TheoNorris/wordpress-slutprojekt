<?php

if(!defined('ABSPATH')){
    exit;
}
require_once('vite.php');

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

function mytheme_modify_shop_query($query) {
    if (is_shop() && $query->is_main_query()) {
        $query->set('orderby', 'date');
        $query->set('order', 'ASC');
    }
}
add_action('pre_get_posts', 'mytheme_modify_shop_query');







