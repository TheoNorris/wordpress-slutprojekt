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


/* function hero_section_shortcode($atts, $content = null) {
    return '<section class="small-banners">' . do_shortcode($content) . '</section>';
}
add_shortcode('section', 'hero_section_shortcode');


function left_section_shortcode($atts, $content = null) {
    return '<section class="left">' . do_shortcode($content) . '</section>';
}
add_shortcode('left_section', 'left_section_shortcode');

function playstation_shortcode($atts, $content = null) {
    return '<div class="play">' . do_shortcode($content) . '</div>';
}
add_shortcode('playstation', 'playstation_shortcode');
 */

 
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

