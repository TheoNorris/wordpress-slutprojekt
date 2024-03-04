function insertFilterHolder() {
  var productHolder = document.querySelector(".product-holder-listing");
  if (productHolder) {
    var existingFilterHolder = document.querySelector(".filter-holder");
    if (existingFilterHolder) {
      existingFilterHolder.parentNode.removeChild(existingFilterHolder);
    }
    var filterHolder = document.createElement("div");
    filterHolder.className = "filter-holder";
    var filters = document.querySelector(".woocommerce-ordering");
    var results = document.querySelector(".woocommerce-result-count");
    if (results) {
      filterHolder.appendChild(results);
    }
    if (filters) {
      filterHolder.appendChild(filters);
    }
    var activeFilterItems = document.querySelector(
      ".wcapf-active-filter-items"
    );
    if (activeFilterItems) {
      activeFilterItems.parentNode.insertBefore(
        filterHolder,
        activeFilterItems.nextSibling
      );
    } else {
      productHolder.insertBefore(filterHolder, productHolder.firstChild);
    }
  }
}
function ajaxCompleteCallback() {
  insertFilterHolder();
}
document.addEventListener("DOMContentLoaded", function() {
  jQuery(document).ajaxComplete(function(event, xhr, settings) {
    if (settings.url.indexOf("wc-ajax=get_refreshed_fragments") !== -1) {
      ajaxCompleteCallback();
    }
  });
  jQuery(document).ajaxSuccess(function(event, xhr, settings) {
    ajaxCompleteCallback();
  });
});
var checkout = document.querySelector(".content-checkout");
if (checkout.innerHTML.includes("Checkout")) {
  checkout.innerHTML = checkout.innerHTML.replace("Checkout", "");
}
document.addEventListener("DOMContentLoaded", function() {
});
