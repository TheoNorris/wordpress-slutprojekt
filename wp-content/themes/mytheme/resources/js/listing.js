// Function to insert filter holder
function insertFilterHolder() {
  var productHolder = document.querySelector(".product-holder-listing");

  if (productHolder) {
    // Remove existing filter holder if it exists
    var existingFilterHolder = document.querySelector(".filter-holder");
    if (existingFilterHolder) {
      existingFilterHolder.parentNode.removeChild(existingFilterHolder);
    }

    var filterHolder = document.createElement("div");
    filterHolder.className = "filter-holder";

    var filters = document.querySelector(".woocommerce-ordering");
    var results = document.querySelector(".woocommerce-result-count");

    if (results) {
      filterHolder.appendChild(results); // Clone the results element
    }

    if (filters) {
      filterHolder.appendChild(filters); // Clone the filters element
    }

    var activeFilterItems = document.querySelector(
      ".wcapf-active-filter-items"
    );
    if (activeFilterItems) {
      // Insert filterHolder after activeFilterItems
      activeFilterItems.parentNode.insertBefore(
        filterHolder,
        activeFilterItems.nextSibling
      );
    } else {
      // Insert filterHolder as the first child
      productHolder.insertBefore(filterHolder, productHolder.firstChild);
    }
  }
}

// Function to call insertFilterHolder after AJAX completion
function ajaxCompleteCallback() {
  // Call insertFilterHolder after AJAX content is loaded
  insertFilterHolder();
}

// Hook into AJAX completion event
document.addEventListener("DOMContentLoaded", function () {
  // Tilldela klasser till de första tr och td elementen
document.querySelector('tbody tr:nth-child(1)').classList.add('first-row');
document.querySelector('tbody tr:nth-child(2)').classList.add('second-row');
document.querySelector('tbody tr:nth-child(1) td').classList.add('first-row-td');
document.querySelector('tbody tr:nth-child(2) td').classList.add('second-row-td');

  // Check if WooCommerce AJAX pagination is used
  jQuery(document).ajaxComplete(function (event, xhr, settings) {
    if (settings.url.indexOf("wc-ajax=get_refreshed_fragments") !== -1) {
      // Call the function to insert filter holder after AJAX content is loaded
      ajaxCompleteCallback();
    }
  });

  // For other AJAX scenarios
  jQuery(document).ajaxSuccess(function (event, xhr, settings) {
    // Call the function to insert filter holder after AJAX content is loaded
    ajaxCompleteCallback();
  });
});
