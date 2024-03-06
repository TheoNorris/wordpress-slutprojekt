<?php
require_once('ajax.php');
require_once('settings.php');
require_once('shortcodes.php');
require_once('hooks.php');

function mytheme_enqueue(){
    $theme_directory = get_template_directory_uri();

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


