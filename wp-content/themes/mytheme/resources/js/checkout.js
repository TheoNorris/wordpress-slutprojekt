var checkout = document.querySelector(".content-checkout");

if (checkout.innerHTML.includes("Checkout")) {
  checkout.innerHTML = checkout.innerHTML.replace("Checkout", "");
}

// Function to update the main element's class based on the current checkout step
function updateMainClass() {
  // Check if there is an li with class '.wpmc-payment.current'
  if ($("li.wpmc-payment.current").length > 0) {
    // Update the main element's class to 'content-checkout-payment'
    $("main")
      .removeClass("content-frontpage")
      .addClass("content-checkout-payment");
  } else {
    // If the above condition is not met, revert to the default class 'content'
    $("main").removeClass("content-checkout-payment").addClass("content");
  }
}

// Function to fetch the updated content or data from the server
function fetchUpdatedContent() {
  // Make an AJAX request to the server
  $.ajax({
    url: "your-server-endpoint-url",
    type: "GET",
    dataType: "html",
    success: function (response) {
      // Update the page content with the response data
      $("#your-content-container").html(response);
      // Call the function to update the main element's class
      updateMainClass();
    },
    error: function (xhr, status, error) {
      // Handle errors if any
      console.error(error);
    },
  });
}

// Call the function initially when the page loads
$(document).ready(function () {
  updateMainClass();
});

// Call the function whenever there is a change in the checkout step
// For example, when a user clicks on a navigation button to proceed to the next step
$(".wpmc-nav-button").on("click", function () {
  fetchUpdatedContent();
});
