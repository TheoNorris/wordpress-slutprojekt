<?php
require_once('settings.php');
require_once('shortcodes.php');
require_once('hooks.php');

// Enqueue Font Awesome stylesheet
function enqueue_font_awesome() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css', array(), '5.15.3', 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_font_awesome');

// Enqueue Poppins font
function enqueue_poppins_font() {
    wp_enqueue_style('poppins-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap', array(), null);
}
add_action('wp_enqueue_scripts', 'enqueue_poppins_font');



function mytheme_enqueue(){
    $theme_directory = get_template_directory_uri();
   /*  wp_enqueue_style('mystyle', $theme_directory . '/style.css');
    wp_enqueue_style('product-style', $theme_directory . './style/product_style.css', array('mystyle'), '1.0', 'all'); */
     wp_enqueue_script('app', $theme_directory . '/app.js');
 }

add_action('wp_enqueue_scripts', 'mytheme_enqueue');

function mytheme_init(){
    $menus = array(
        'huvudmeny' => 'huvudmeny' ,
        'cart-meny' => 'cart-meny' ,
        'services' => 'services',
        'assistance' => 'assistance',
        
    );
register_nav_menus($menus);
}
add_action('after_setup_theme', 'mytheme_init');

