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

    wp_localize_script("mytheme_ajax", "ajax_variabels", array(
        "ajaxUrl" => admin_url("admin-ajax.php"),
        "nonce" => wp_create_nonce("mytheme_ajax_nonce"),
        
    ));
}

function mytheme_getbyajax(){
    $result = array();
    wp_send_json($result);
}










add_shortcode('live_search', 'live_search_function');
function live_search_function() { ?>

    <input type="text" name="keyword" id="keyword" onkeyup="fetchData()" placeholder="Search"></input>

    <div id="productfetch"></div>

    <?php
}

add_action('wp_footer', 'ajax_fetch');
function ajax_fetch() { ?>

    <script type="text/javascript">
        function fetchData() {
            if (document.getElementById('keyword').value.trim().length == 0) {
                jQuery('#productfetch').html('');
            } else {
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    data: { action: 'data_fetch', keyword: jQuery('#keyword').val() },
                    success: function(data) {
                        jQuery('#productfetch').html(data);
                    }
                });
            }
        }
    </script>
<?php
}

add_action('wp_ajax_data_fetch', 'product_fetch');
add_action('wp_ajax_nopriv_data_fetch', 'product_fetch');
function product_fetch() {
    $the_query = new WP_Query(array(
        'posts_per_page' => -1,
        's' => esc_attr($_POST['keyword']),
        'post_type' => 'product'
    ));

    if ($the_query->have_posts()) :
        while ($the_query->have_posts()) : $the_query->the_post(); ?>

            <h2><a href="<?php echo esc_url(post_permalink()); ?>"><?php the_title(); ?></a></h2>
<?php
        endwhile;
        wp_reset_postdata();
    endif;
    die();
}
