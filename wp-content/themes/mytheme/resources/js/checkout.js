var checkout = document.querySelector(".content-checkout");

if (checkout.innerHTML.includes("Checkout")) {
  checkout.innerHTML = checkout.innerHTML.replace("Checkout", "");
}

// Function to update star ratings
function updateStarRatings() {
  // Get all elements with the .star-rating class
  var starRatingElements = document.querySelectorAll(".star-rating");

  // Iterate over each element
  starRatingElements.forEach(function (element) {
    // Get the aria-label attribute value
    var ariaLabel = element.getAttribute("aria-label");

    // Extract the rating value from the aria-label (assuming it's formatted as "Rated X out of 5")
    var rating = parseFloat(ariaLabel.match(/\d+\.\d+/)[0]); // Extracts floating-point numbers

    // Insert the rating value directly into the .star-rating element
    element.textContent = rating;
  });
}

// Function to handle AJAX completion
function handleAjaxComplete() {
  // Update star ratings after AJAX content is loaded
  updateStarRatings();
}

// Hook into AJAX completion event
document.addEventListener("DOMContentLoaded", function () {
  // Check if WooCommerce AJAX pagination is used
  jQuery(document).ajaxComplete(function (event, xhr, settings) {
    if (settings.url.indexOf("wc-ajax=get_refreshed_fragments") !== -1) {
      // Call the function to update star ratings after AJAX content is loaded
      handleAjaxComplete();
    }
  });

  // For other AJAX scenarios
  jQuery(document).ajaxSuccess(function (event, xhr, settings) {
    // Call the function to update star ratings after AJAX content is loaded
    handleAjaxComplete();
  });
});
