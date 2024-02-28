var productHolder = document.querySelector(".product-holder-listing");

if (productHolder) {
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

  productHolder.insertBefore(filterHolder, productHolder.firstChild);
}
