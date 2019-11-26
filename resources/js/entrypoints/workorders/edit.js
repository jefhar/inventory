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

function toggleLock() {
  const isLockedButton = document.getElementById("lock_button");
  const wantOrderToBeLocked = !(isLockedButton.dataset.isLocked === "true");
  const data = { is_locked: wantOrderToBeLocked };
  const url = "/workorders/" + isLockedButton.dataset.workOrderId;
  axios
    .patch(url, data)
    .then(response => {
      isLockedButton.dataset.isLocked = response.data.is_locked;
      update();
    })
    .catch(error => {
      console.log(error.response);
    });
}

$(function() {
  $('[data-toggle="tooltip"]').tooltip();

  const isLockedButton = document.getElementById("lock_button");
  isLockedButton.addEventListener("click", toggleLock);
  update();
});
