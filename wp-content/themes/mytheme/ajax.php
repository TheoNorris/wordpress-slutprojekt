<?php 

function init_ajax(){
    add_action("wp_ajax_mytheme_getbyajax", "mytheme_getbyajax");
    add_action("wp_ajax_nopriv_mytheme_getbyajax", "mytheme_getbyajax");

    add_action("wp_enqueue_scripts", "mytheme_enqueue_scripts");
}

add_action("init", "init_ajax");

function mytheme_enqueue_scripts(){
 
    if ( ! is_checkout() ) {
        wp_enqueue_script("mytheme_jquery", get_template_directory_uri() . "/resources/js/jquery.js", array(), false, true);
    }
    
    wp_enqueue_script("mytheme_ajax", get_template_directory_uri() . "/resources/js/ajax.js", array("mytheme_jquery"), false, array());

    wp_localize_script("mytheme_ajax", "ajax_variables", array(
        "ajaxUrl" => admin_url("admin-ajax.php"),
        "nonce" => wp_create_nonce("mytheme_ajax_nonce"),
        
    ));
}


function mytheme_getbyajax() {
    
    check_ajax_referer('mytheme_ajax_nonce', 'nonce');

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1; 

   
    $offset = ($page - 1) * 9 + 9; 
    
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 9, 
        'orderby'        => 'date',
        'order'          => 'ASC',
        'offset'         => $offset, 
    );

    $query = new WP_Query($args);

    $products_html = '';

    if ($query->have_posts()) {
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product'); 
        }
        $products_html = ob_get_clean();
    }

    wp_reset_postdata();

    if (empty($products_html)) {
        echo '<p class="nomore-p nomore-products">No more products</p>';
    } else {
        echo $products_html;
    }
    
    wp_die(); 
}