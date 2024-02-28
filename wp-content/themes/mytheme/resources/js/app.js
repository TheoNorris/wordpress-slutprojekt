import "./listing";

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
});
