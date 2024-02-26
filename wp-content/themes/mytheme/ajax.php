<?php 

function init_ajax(){
    add_action("wp_ajax_mytheme_getbyajax", "mytheme_getbyajax");
    add_action("wp_ajax_nopriv_mytheme_getbyajax", "mytheme_getbyajax");

    add_action("wp_enqueue_scripts", "mytheme_enqueue_scripts");
}

add_action("init", "init_ajax");

function mytheme_enqueue_scripts(){
    wp_enqueue_script("mytheme_jquery", get_template_directory_uri() . "/resources/scripts/jquery.js", array(), false, array());
    wp_enqueue_script("mytheme_ajax", get_template_directory_uri() . "/resources/scripts/ajax.js", array("mytheme_jquery"), false, array());

    wp_localize_script("mytheme_ajax", "ajax_variabels", array(
        "ajaxUrl" => admin_url("admin-ajax.php"),
        "nonce" => wp_create_nonce("mytheme_ajax_nonce"),
        
    ));
}

function mytheme_getbyajax(){
    $result = array();
    wp_send_json($result);
}