jQuery(document).ready(function ($) {
  var page = 1; // Initialize page number

  $("#load-more-btn").on("click", function () {
    var button = $(this);

    if ($(".nomore-p").length > 0) {
      $("#load-more-btn").hide();
      $(".nomore-p").remove();
    } else {
      $("#load-more-btn").show();
    }

    $.ajax({
      url: ajax_variables.ajaxUrl,
      type: "POST",
      dataType: "html",
      data: {
        action: "mytheme_getbyajax",
        nonce: ajax_variables.nonce,
        page: page,
      },
      success: function (response) {
        $(".products").append(response);
        page++;

        var productCount = $(".products .product").length;
        $(".woocommerce-result-count label").text(productCount);
      },
      error: function (xhr, status, error) {
        console.error("AJAX error:", error);
      },
    });
  });
});
