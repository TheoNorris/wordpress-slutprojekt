import "./listing";
import "./checkout";

document.addEventListener("DOMContentLoaded", function () {
  // Tilldela klasser till de första tr och td elementen
  document.querySelector("tbody tr:nth-child(1)").classList.add("first-row");
  document.querySelector("tbody tr:nth-child(2)").classList.add("second-row");
  document
    .querySelector("tbody tr:nth-child(1) td")
    .classList.add("first-row-td");
  document
    .querySelector("tbody tr:nth-child(2) td")
    .classList.add("second-row-td");
});
