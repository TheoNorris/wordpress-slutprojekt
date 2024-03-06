jQuery(document).ready(function ($) {
  var page = 1; // Initialize page number

  $("#load-more-btn").on("click", function () {
    $.ajax({
      url: ajax_variables.ajaxUrl,
      type: "POST",
      dataType: "html", // Expecting HTML response
      data: {
        action: "mytheme_getbyajax",
        nonce: ajax_variables.nonce,
        page: page, // Send current page number
      },
      success: function (response) {
        $(".products").append(response); // Append the HTML response directly
        page++; // Increment page number for next request
      },
      error: function (xhr, status, error) {
        console.error("AJAX error:", error);
      },
    });
  });
});
