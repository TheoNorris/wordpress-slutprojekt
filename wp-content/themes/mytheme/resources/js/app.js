import "./listing";
import "./checkout";

document.addEventListener("DOMContentLoaded", function () {
  var contentElement = document.querySelector(".content");

  if (contentElement) {
    // Loop through child nodes
    for (var i = 0; i < contentElement.childNodes.length; i++) {
      var node = contentElement.childNodes[i];

      // Check if it's a text node and contains the text "Cart"
      if (
        node.nodeType === Node.TEXT_NODE &&
        node.textContent.trim() === "Cart"
      ) {
        // Remove the node
        contentElement.removeChild(node);
        break; // Stop looping once "Cart" is found and removed
      }
    }
  }

  // Lyssna på klickhändelser för plus- och minusknapparna
  $(document).on("click", ".prqu_minus, .prqu_plus", function (event) {
    event.preventDefault(); // Förhindra standardbeteendet för knapparna

    var $input = $(this).siblings("input.prqu_input"); // Hitta närliggande inmatningsfält för kvantitet
    var new_quantity = parseInt($input.val()); // Hämta aktuell kvantitet

    // Öka eller minska kvantiteten beroende på vilken knapp som klickades
    if ($(this).hasClass("prqu_minus")) {
      new_quantity = new_quantity - 1;
      new_quantity = new_quantity < 1 ? 1 : new_quantity;
    } else {
      new_quantity = new_quantity + 1;
    }

    // Uppdatera kvantiteten i inmatningsfältet
    $input.val(new_quantity);

    // Uppdatera priset direkt på sidan (utan att använda AJAX)
    var price_per_unit = parseFloat(
      $(this)
        .closest(".woocommerce-cart-form__cart-item")
        .find(".product-price .woocommerce-Price-amount")
        .text()
        .replace(/[^0-9.-]+/g, "")
    );
    var subtotal = (new_quantity * price_per_unit) / 100;

    // Formatera priset
    var formatted_price = formatPrice(subtotal);

    // Uppdatera subtotalen på sidan
    $(this)
      .closest(".woocommerce-cart-form__cart-item")
      .find(".product-subtotal .woocommerce-Price-amount")
      .html(formatted_price);

    // Uppdatera subtotalen på kundvagnens totalsida
    var formatted_subtotal = formatPrice(subtotal); // Definiera formatted_subtotal
    $('.cart-subtotal td[data-title="Subtotal"]').text(formatted_subtotal);

    // Beräkna det totala priset
    var total_price = calculateTotalPrice(subtotal);

    // Formatera det totala priset
    var formatted_total_price = formatPrice(total_price);

    // Uppdatera det totala priset på kundvagnens totalsida
    $('.order-total td[data-title="Total"]').text(formatted_total_price);
  });

  function formatPrice(price) {
    return parseFloat(price).toLocaleString("sv-SE", {
      style: "currency",
      currency: "SEK",
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  }

  function calculateTotalPrice(subtotal) {
    // Lägg till eventuella avgifter eller fraktkostnader till subtotalen för att beräkna det totala priset
    var total_price = subtotal; // Exempel: Om det inte finns några avgifter, är det totala priset samma som subtotalen

    // Här kan du lägga till eventuella ytterligare avgifter eller fraktkostnader baserat på dina affärsregler

    return total_price;
  }
});
