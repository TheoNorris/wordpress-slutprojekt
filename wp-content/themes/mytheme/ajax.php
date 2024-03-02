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


/* unction my_update_cart($cart_item_key, $qty){
    // Kontrollera om nödvändig data har skickats via POST
    if( isset($_POST['cart_item_key']) && isset($_POST['qty']) ) {
        // Hämta data från POST-förfrågan
        $cart_item_key = sanitize_text_field( $_POST['cart_item_key'] );
        $qty = intval( $_POST['qty'] );

        // Uppdatera varukorgen med den givna kvantiteten för den specifika varan
        WC()->cart->set_quantity( $cart_item_key, $qty );

        // Hämta den uppdaterade varukorgen
        $cart = WC()->cart->get_cart();

        // Beräkna nya totalbeloppet och subtotalbeloppet
        $subtotal = WC()->cart->get_subtotal();
        $total = WC()->cart->get_total();

        // Skapa ett svarsobjekt
        $result = array(
            'success' => true,
            'message' => 'Varukorgen uppdaterad!',
            'cart' => $cart, // Skicka den uppdaterade varukorgen tillbaka
            'subtotal' => $subtotal, // Skicka tillbaka subtotalbeloppet
            'total' => $total // Skicka tillbaka totalbeloppet
        );
    } else {
        // Om nödvändig data inte har skickats via POST
        $result = array(
            'success' => false,
            'message' => 'Nödvändig data saknas för att uppdatera varukorgen'
        );
    }

    // Skicka svar i JSON-format tillbaka till klienten
    wp_send_json($result);
}

// Funktion för att lägga till JavaScript-kod i sidfoten
add_action( 'wp_footer', 'ts_quantity_plus_minus' );
function ts_quantity_plus_minus() {
    // CHECK IF THE CURRENT PAGE IS A PRODUCT PAGE, OTHERWISE, DO NOTHING
    if ( ! is_cart() ) return;
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){  
            // ADD EVENT LISTENERS TO PLUS AND MINUS BUTTONS INSIDE THE CART FORM 
            $('form.woocommerce-cart-form').on('click', 'button.plus, button.minus', function() {
                // RETRIEVE THE QUANTITY INPUT FIELD
                let qty = $( this ).closest( 'form.woocommerce-cart-form' ).find( '.qty' );
                let val   = parseFloat(qty.val());
                let max = parseFloat(qty.attr( 'max' ));
                let min = parseFloat(qty.attr( 'min' ));
                let step = parseFloat(qty.attr( 'step' ));
                
                // INCREMENT / DECREMENT QUANTITY BASED ON BUTTON
                if ( $( this ).is( '.plus' ) ) {
                    if ( max && ( max <= val ) ) {
                        qty.val( max );
                    } else {
                        qty.val( val + step );
                    }
                } else {
                    if ( min && ( min >= val ) ) {
                        qty.val( min );
                    } else if ( val > 1 ) {
                        qty.val( val - step );
                    }
                }

                let cart_item_key = qty.attr('name').replace('cart[', '').replace(']', '');

                // Trigger the update_cart function with the new quantity and cart item key
                 my_update_cart(cart_item_key, qty.val()); 
            });
            
            // PLUS AND MINUS BUTTONS
            $('form.woocommerce-cart-form .quantity').prepend('<button type="button"  class="minus" >-</button>');
            $('form.woocommerce-cart-form .quantity').append('<button type="button" class="plus" >+</button>');
        });
    </script>
    <?php
} 
*/
/* ------------------ */










