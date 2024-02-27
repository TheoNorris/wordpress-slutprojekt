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
            if ($category->name != 'Uncategorized') {
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

//