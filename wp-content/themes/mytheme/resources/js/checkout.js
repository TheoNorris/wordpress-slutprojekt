var checkout = document.querySelector(".content-checkout");

if (checkout.innerHTML.includes("Checkout")) {
  checkout.innerHTML = checkout.innerHTML.replace("Checkout", "");
}

