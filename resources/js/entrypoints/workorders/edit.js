/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */
import React from "react";
import axios from "axios";

require("../../bootstrap");

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

/*

if (document.getElementById("workorders_edit")) {
  console.log("got workorders_edit");
  ReactDOM.render(
    <WorkOrderEdit />,
    document.getElementById("workorders_edit")
  );
}

*/

function update() {
  const isLockedButton = document.getElementById("lock_button");
  const inventoryButton = document.getElementById("add_inventory_button");
  const lockIcon = document.getElementById("lock-icon");

  if (isLockedButton.dataset.isLocked === "true") {
    inventoryButton.disabled = true;
    isLockedButton.classList.add("btn-outline-warning");
    isLockedButton.classList.remove("btn-outline-success");
    lockIcon.classList.add("fa-unlock-alt");
    lockIcon.classList.remove("fa-lock");
    $('[data-toggle="tooltip"]')
      .tooltip("hide")
      .attr("data-original-title", "Unlock work order.")
      .tooltip("show");
  } else {
    inventoryButton.disabled = false;
    isLockedButton.classList.add("btn-outline-success");
    isLockedButton.classList.remove("btn-outline-warning");
    lockIcon.classList.add("fa-lock");
    lockIcon.classList.remove("fa-unlock-alt");
    $('[data-toggle="tooltip"]')
      .tooltip("hide")
      .attr("data-original-title", "Lock work order.")
      .tooltip("show");
  }
}

$(function() {
  $('[data-toggle="tooltip"]').tooltip();

  // Lock/Unlock work order
  document.getElementById("lock_button").addEventListener("click", () => {
    const isLockedButton = document.getElementById("lock_button");
    const wantOrderToBeLocked = !(isLockedButton.dataset.isLocked === "true");
    const data = { is_locked: wantOrderToBeLocked };
    const url = `/workorders/${isLockedButton.dataset.workOrderId}`;
    axios
      .patch(url, data)
      .then(response => {
        isLockedButton.dataset.isLocked = response.data.is_locked;
        update();
      })
      .catch(error => {
        console.log(error.response.data);
      });
  });

  // Submit changed field data to UPDATE
  document.getElementById("update_button").addEventListener("click", event => {
    console.log("update_button clicked.");
    const isLockedButton = document.getElementById("lock_button");
    const updateAlert = document.createElement("div");

    // Remove any previous alerts
    const alertRow = document.getElementById("alert_row");
    while (alertRow.hasChildNodes()) {
      alertRow.removeChild(alertRow.lastChild);
    }

    // Gather the information
    const url = `/workorders/${isLockedButton.dataset.workOrderId}`;
    const data = {
      company_name: document.getElementById("company_name").value,
      first_name: document.getElementById("first_name").value,
      last_name: document.getElementById("last_name").value,
      phone_number: document.getElementById("phone_number").value,
      email: document.getElementById("email").value,
      intake: document.getElementById("intake").value
    };
    // send PATCH via axios

    axios
      .patch(url, data)
      .then(response => {
        console.log("response");
        console.log(response.data);
        // Give user a visual indication that fields have been updated.
        // Even better, add onChange to the fields, and if they're dirty, give
        // visual indication.
        updateAlert.classList.add(
          "alert",
          "alert-success",
          "mt-3",
          "col-6",
          "offset-3"
        );
        updateAlert.innerHTML =
          '<h4 class="alert-heading">Success</h4><p>Work Order successfully updated.</p>';
      })
      .catch(error => {
        console.debug(error);
        updateAlert.innerText = error.toString();
        updateAlert.classList.add(
          "alert",
          "alert-warning",
          "mt-3",
          "col-6",
          "offset-3"
        );
      })
      .finally(() => {
        alertRow.appendChild(updateAlert);
      });

    event.preventDefault();
  });

  update();
});
