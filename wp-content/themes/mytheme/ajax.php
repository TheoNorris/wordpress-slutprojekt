<?php 

function init_ajax(){
    add_action("wp_ajax_mytheme_getbyajax", "mytheme_getbyajax");
    add_action("wp_ajax_nopriv_mytheme_getbyajax", "mytheme_getbyajax");

    add_action("wp_enqueue_scripts", "mytheme_enqueue_scripts");
}

add_action("init", "init_ajax");

function mytheme_enqueue_scripts(){
    wp_enqueue_script("mytheme_jquery", get_template_directory_uri() . "/resources/js/jquery.js", array(), false, array());
    wp_enqueue_script("mytheme_ajax", get_template_directory_uri() . "/resources/js/ajax.js", array("mytheme_jquery"), false, array());

    wp_localize_script("mytheme_ajax", "ajax_variables", array(
        "ajaxUrl" => admin_url("admin-ajax.php"),
        "nonce" => wp_create_nonce("mytheme_ajax_nonce"),
        
    ));
}


function mytheme_getbyajax() {
    // Check nonce and permissions
    check_ajax_referer('mytheme_ajax_nonce', 'nonce');

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1; // Get the page number from the AJAX request

    // Calculate the offset to start from the 10th product
    $offset = ($page - 1) * 9 + 9; // Start from the 10th product

    // Modify the query args to fetch the products starting from the 10th
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 9, // Adjust as needed
        'orderby'        => 'date',
        'order'          => 'ASC',
        'offset'         => $offset, // Start from the 10th product
    );

    $query = new WP_Query($args);

    $products_html = '';

    if ($query->have_posts()) {
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product'); // Output the product template
        }
        $products_html = ob_get_clean();
    }

    wp_reset_postdata();

    if (empty($products_html)) {
        echo 'No more products';
    } else {
        echo $products_html;
    }
    
    wp_die(); // Always include this to terminate the script properly
}