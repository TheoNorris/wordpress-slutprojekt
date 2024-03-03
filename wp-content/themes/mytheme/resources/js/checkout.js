var checkout = document.querySelector(".content-checkout");

if (checkout.innerHTML.includes("Checkout")) {
  checkout.innerHTML = checkout.innerHTML.replace("Checkout", "");
}

jQuery(document).ready(function ($) {
  $('.woocommerce-checkout input[type="text"]').removeAttr("placeholder");

  $(".input-text").removeAttr("value");
});
