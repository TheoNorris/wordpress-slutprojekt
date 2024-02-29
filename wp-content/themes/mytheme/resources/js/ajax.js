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
