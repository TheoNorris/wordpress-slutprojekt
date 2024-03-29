<?php
// Function to generate WooCommerce product category subheader navigation
function custom_woocommerce_category_subheader_navigation() {
    // Get product categories
    $product_categories = get_terms( array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ) );

    // Initialize output variable
    $output = '';

    // Check if there are product categories
    if ($product_categories) {
        $output .= '<div class="subheader-navigation">';
        $output .= '<ul>';
        
        // Loop through product categories
        foreach ($product_categories as $category) {
            // Exclude "Uncategorized"
            if ($category->parent == 0 && $category->name != 'Uncategorized') {
                // Get category thumbnail
                $thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );
                $image = wp_get_attachment_url( $thumbnail_id );
                // Output category with thumbnail
                $output .= '<li><img src="' . $image . '" alt="' . $category->name . '">';
                $output .= '<a href="' . get_term_link($category) . '">' . $category->name . '</a></li>';
            }
        }

        $output .= '</ul>';
        $output .= '</div>';
    }

    // Return the output
    return $output;
}
add_shortcode('woocommerce_category_subheader_navigation', 'custom_woocommerce_category_subheader_navigation');
 
// Define the shortcode function
function custom_div_structure_shortcode($atts, $content = null) {
    // Set up attributes with defaults
    $atts = shortcode_atts(
        array(),
        $atts,
        'custom_div_structure'
    );

    // Define the image URLs
    $playstation_image_url = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/02/PlayStation.png';
    $air_pods_max_image_url = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/02/hero__gnfk5g59t0qe_xlarge_2x-1.png'; 
    $apple_vision_pro_image_url = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/02/image-36.png'; 
    $macbook_image_url = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/02/MacBook-Pro-14.png';

    // Get the attachment IDs based on the image URLs
    $playstation_image_id = attachment_url_to_postid($playstation_image_url);
    $air_pods_max_image_id = attachment_url_to_postid($air_pods_max_image_url);
    $apple_vision_pro_image_id = attachment_url_to_postid($apple_vision_pro_image_url);
    $macbook_image_id = attachment_url_to_postid($macbook_image_url);
    
    // Get the image URLs and other metadata
    $playstation_image_data = wp_get_attachment_image_src($playstation_image_id, 'full');
    $air_pods_max_image_data = wp_get_attachment_image_src($air_pods_max_image_id, 'full');
    $apple_vision_pro_image_data = wp_get_attachment_image_src($apple_vision_pro_image_id, 'full');
    $macbook_image_data = wp_get_attachment_image_src($macbook_image_id, 'full');

    // Return the HTML structure
    ob_start(); ?>
    <div class="small_banners">
        <div class="playstation">
            <img src="<?php echo $playstation_image_data[0]; ?>" alt="Playstation Image">
            <div class="text">
            <h1>Playstation 5</h1>
            <p>Incredibly powerful CPUs, GPUs, and an SSD with 
                integrated I/O 
                will redefine your PlayStation experience.</p></div>
        </div>
        <div class="pods_container">
            <div class="air_pods_max">
                <img src="<?php echo $air_pods_max_image_data[0]; ?>" alt="AirPods Max Image">
                <div class="text">
                <h2>Apple AirPods <span class="bold">Max</span></h2>
                <p>Computational audio. Listen, it's powerful</p></div>
            </div>
            <div class="apple_vision_pro">
                <img src="<?php echo $apple_vision_pro_image_data[0]; ?>" alt="Apple Vision Pro Image">
                <div class="text">
                <h2>Apple </br> Vision <span class="bold">Pro</span></h2>
                <p>An immersive way to experience entertainment</p>
                </div>
            </div>
        </div>
        <div class="macbook">
            <div class="mac_text">
            <h1>Macbook <span class="bold">Air</span></h1>
            <p>The new 15‑inch MacBook Air makes room for more of what 
                you love with a spacious Liquid Retina display.</p>
                <button><a href="http://wordpress-slutprojekt.test/shop/">Shop Now</a></button></div>
            <img src="<?php echo $macbook_image_data[0]; ?>" alt="MacBook Image">
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('custom_div_structure', 'custom_div_structure_shortcode');


/* --------------------------------------------------------- */

add_shortcode('live_search', 'live_search_function');
function live_search_function() { ?>

    <input type="text" name="keyword" id="keyword" placeholder="Search" onkeyup="fetch()"></input>

    <div id="productfetch"></div>

    <?php
}

add_action( 'wp_footer', 'ajax_fetch' );
function ajax_fetch() { ?>

<script type="text/javascript">

    function fetch() {
        
        if( document.getElementById('keyword').value.trim().length == 0 ) {

            jQuery('#productfetch').html('');

        } else {

            jQuery.ajax( {

                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                data: { action: 'data_fetch', keyword: jQuery('#keyword').val() },
                success: function(data) {
                    jQuery('#productfetch').html( data );
                }
            });
        }
    }
    // Denna funktion visar productfetch när användaren börjar skriva
    jQuery(document).ready(function() {
            jQuery('#keyword').on('input', function() {
                if (jQuery(this).val().length > 0) {
                    jQuery('#productfetch').show();
                } else {
                    jQuery('#productfetch').hide();
                }
            });
        });
</script>
<?php
}

add_action('wp_ajax_data_fetch' , 'product_fetch');
add_action('wp_ajax_nopriv_data_fetch','product_fetch');
function product_fetch() {

    $the_query = new WP_Query( array( 'posts_per_page' => -1, 's' => esc_attr( $_POST['keyword'] ), 'post_type' => 'product' ) );

    if( $the_query->have_posts() ) :
        while( $the_query->have_posts() ): $the_query->the_post(); ?>
    <div class="product-li">
    <h3><a href="<?php echo esc_url( post_permalink() ); ?>"><?php the_title();?></a></h3>
    <?php
    // Hämta bild för produkten
            $thumbnail_id = get_post_thumbnail_id();
            $image_url = wp_get_attachment_image_src($thumbnail_id, 'thumbnail');
            if ($image_url) {
                ?>
                <img src="<?php echo $image_url[0]; ?>" alt="<?php the_title_attribute(); ?>">
                <?php
            }
            ?> 
            </div>
        <?php endwhile;
        wp_reset_postdata();
    endif;
die();
}


/* ------------------------------------------------------------ */
function custom_atributes_shortcode_function() {
    $output = '<div class="custom-shortcode-wrapper">';

     // Image 1
    $image_url_1 = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/03/Screensize.png'; // Change this to the URL of your desired image
    $output .= '<div class="custom-shortcode-item">';
    $output .= '<img src="' . esc_url($image_url_1) . '" alt="Image 1">';
    $output .= '<div class="text_">';
    $output .= '<h5 class="color_text">Screen size</h5>';
    $output .= '<h5>6.7"</h5>';
    $output .= '</div>';
    $output .= '</div>';

    // Image 2
    $image_url_2 = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/03/smartphone-rotate-2-svgrepo-com-2.png'; 
    $output .= '<div class="custom-shortcode-item">';
    $output .= '<img src="' . esc_url($image_url_2) . '" alt="Image 2">';
    $output .= '<div class="text_">';
    $output .= '<h5 class="color_text">CPU</h5>';
    $output .= '<h5>Apple A16 Bionic</h5>';
    $output .= '</div>';
    $output .= '</div>';

    // Image 2
    $image_url_3 = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/03/smartphone-rotate-2-svgrepo-com-2-1.png'; // Change this to the URL of your desired image
    $output .= '<div class="custom-shortcode-item">';
    $output .= '<img src="' . esc_url($image_url_3) . '" alt="Image 2">';
    $output .= '<div class="text_">';
    $output .= '<h5 class="color_text">Number of Cores</h5>';
    $output .= '<h5>6</h5>';
    $output .= '</div>';
    $output .= '</div>';
 
     // Image 4
     $image_url_4 = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/03/smartphone-rotate-2-svgrepo-com-2-2.png'; // Change this to the URL of your desired image
     $output .= '<div class="custom-shortcode-item">';
     $output .= '<img src="' . esc_url($image_url_4) . '" alt="Image 2">';
     $output .= '<div class="text_">';
     $output .= '<h5 class="color_text">Main camera</h5>';
     $output .= '<h5>48-12-12</h5>';
     $output .= '</div>';
     $output .= '</div>';
  // Image 5
  $image_url_5 = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/03/smartphone-rotate-2-svgrepo-com-2-3.png'; // Change this to the URL of your desired image
  $output .= '<div class="custom-shortcode-item">';
  $output .= '<img src="' . esc_url($image_url_5) . '" alt="Image 1">';
  $output .= '<div class="text_">';
  $output .= '<h5 class="color_text">Front-camera</h5>';
  $output .= '<h5>12 MP</h5>';
  $output .= '</div>';
  $output .= '</div>';

  // Image 6
  $image_url_6 = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/03/smartphone-rotate-2-svgrepo-com-2-4.png'; // Change this to the URL of your desired image
  $output .= '<div class="custom-shortcode-item">';
  $output .= '<img src="' . esc_url($image_url_6) . '" alt="Image 2">';
  $output .= '<div class="text_">';
  $output .= '<h5 class="color_text">Battery capacity</h5>';
  $output .= '<h5>4323 mAh</h5>';
  $output .= '</div>';
  $output .= '</div>';


    $output .= '</div>';

    return $output;
}
add_shortcode('custom_atributes_shortcode', 'custom_atributes_shortcode_function');

/* -------------------USP shortcode----------------------- */

function custom_usp_shortcode_function() {
    $output = '<div class="custom-usp-wrapper">';

    // USP 1
    $usp_1_icon = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/03/delivery-truck-svgrepo-com-1-1.png'; 
    $output .= '<div class="custom-usp-item">';
    $output .= '<div class="pic_">';
    $output .= '<img src="' . esc_url($usp_1_icon) . '" alt="USP Icon 1">';
    $output .= '</div>';
    $output .= '<div class="text_">';
    $output .= '<h5 class="color_text">Free Delivery</h5>';
    $output .= '<h5>1-2 day</h5>';
    $output .= '</div>';
    $output .= '</div>';

    // USP 2
    $usp_2_icon = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/03/shop-2-svgrepo-com-2.png'; 
    $output .= '<div class="custom-usp-item">';
    $output .= '<div class="pic_">';
    $output .= '<img src="' . esc_url($usp_2_icon) . '" alt="USP Icon 2">';
    $output .= '</div>';
    $output .= '<div class="text_">';
    $output .= '<h5 class="color_text">In Stock</h5>';
    $output .= '<h5>Today</h5>';
    $output .= '</div>';
    $output .= '</div>';

    // USP 3
    $usp_3_icon = 'http://wordpress-slutprojekt.test/wp-content/uploads/2024/03/verify.png'; 
    $output .= '<div class="custom-usp-item">';
    $output .= '<div class="pic_">';
    $output .= '<img src="' . esc_url($usp_3_icon) . '" alt="USP Icon 3">';
    $output .= '</div>';
    $output .= '<div class="text_">';
    $output .= '<h5 class="color_text">Guaranteed</h5>';
    $output .= '<h5>1 year</h5>';
    $output .= '</div>';
    $output .= '</div>';

    $output .= '</div>';

    return $output;
}
add_shortcode('custom_usp_shortcode', 'custom_usp_shortcode_function');

/* ------------------------------------------- */

function custom_details_shortcode_function() {
    $output = '<div class="custom-details-wrapper">';

    // Screen section
    $output .= '<h3 class="details_title">Screen</h3>'; 
    $output .= '<div class="custom-detail-item">';
    $output .= '<p class="p_element">Screen diagonal</p>'; 
    $output .= '<p class="p_element_2">6.7"</p>'; 
    $output .= '</div>';

    $output .= '<div class="custom-detail-item">';
    $output .= '<p class="p_element">The screen resolution</p>'; 
    $output .= '<p class="p_element_2">2796x1290</p>'; 
    $output .= '</div>';

    $output .= '<div class="custom-detail-item">';
    $output .= '<p class="p_element">The screen refresh rate</p>'; 
    $output .= '<p class="p_element_2">120 Hz</p>'; 
    $output .= '</div>';

    $output .= '<div class="custom-detail-item">';
    $output .= '<p class="p_element">The pixel density</p>'; 
    $output .= '<p class="p_element_2">460 ppi</p>'; 
    $output .= '</div>';

    $output .= '<div class="custom-detail-item">';
    $output .= '<p class="p_element">Screen type</p>'; 
    $output .= '<p class="p_element_2">OLED</p>'; 
    $output .= '</div>';

    $output .= '<div class="custom-detail-item">';
    $output .= '<div class="p_element_left">Additionally</div>'; 
    $output .= '<div class="p_element_wrapp">'; 
    $output .= '<p class="p_element_2">Dynamic Island</p>'; 
    $output .= '<p class="p_element_2">Always-On display</p>'; 
    $output .= '<p class="p_element_2">HDR display</p>'; 
    $output .= '<p class="p_element_2">True Tone</p>'; 
    $output .= '<p class="p_element_2">Wide color (P3)</p>'; 
    $output .= '</div>';
    $output .= '</div>';

    // CPU section
    $output .= '<h3 class="details_title">CPU</h3>'; 
    $output .= '<div class="custom-detail-item">';
    $output .= '<p class="p_element">CPU</p>'; 
    $output .= '<p class="p_element_2">A16 Bionic</p>'; 
    $output .= '</div>';

    $output .= '<div class="custom-detail-item">';
    $output .= '<p class="p_element">Number of cores</p>'; 
    $output .= '<p class="p_element_2">6</p>'; 
    $output .= '</div>';

    $output .= '</div>';

    return $output;
}
add_shortcode('custom_details_shortcode', 'custom_details_shortcode_function');
