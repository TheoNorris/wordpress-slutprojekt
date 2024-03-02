jQuery(document).ready(function ($) {
  // Example AJAX request
  $.ajax({
    url: ajax_variabels.ajaxUrl,
    type: "POST",
    dataType: "json",
    data: {
      action: "mytheme_getbyajax",
      nonce: ajax_variabels.nonce,
    },
    success: function (response) {
      console.log("Response from server:", response);
    },
    error: function (xhr, status, error) {
      console.error("AJAX error:", error);
    },
  });
});


/* --------------------------------------------- */

/* jQuery(document).ready(function ($) {
  
    
    $.ajax({
      url: ajax_variabels.ajaxUrl,
      type: "POST",
      dataType: "json",
      data: {
        action: "ts_quantity_plus_minus",
        nonce: ajax_variabels.nonce,
        
      },
      success: function (response) {
        // Uppdatera varukorgen baserat på AJAX-svaret om det behövs
        console.log("Response for knappar:", response);
        // Kontrollera om AJAX-svaret innehåller information om en uppdatering av kundvagnen
        if (response.success) {
            // Uppdatera kundvagnens totala belopp och antal varor någonstans på sidan
            $('.cart-subtotal').html(response.data.subtotal_html);
            $('.cart-total').html(response.data.total_html);
            $('.widget_shopping_cart_content').html(response.data.cart_html);
    
            // Om det finns en varukorgs-widget på sidan som behöver uppdateras
            // Du kan också uppdatera andra delar av sidan som är relevanta för kundvagnen
        } else {
            console.log("Kundvagnen uppdaterades inte."); // Logga ett felmeddelande om uppdateringen misslyckades
        }
    },
      error: function (xhr, status, error) {
        console.error("AJAX error:", error);
      },
    });
  }); */


