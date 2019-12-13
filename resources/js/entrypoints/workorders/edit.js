/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */
import React from "react";
import axios from "axios";

require("../../bootstrap");
require("formBuilder/dist/form-builder.min");
require("formBuilder/dist/form-render.min");

/**
 * Next, we may create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const AutoComplete = require("autocomplete-js");

function update() {
  const inventoryButton = document.getElementById("add_inventory_button");
  const isLockedButton = document.getElementById("lock_button");
  const lockIcon = document.getElementById("lock-icon");
  const outline = document.getElementById("outline");
  const lockHeader = document.getElementById("locked_header");
  const locked = {
    clickTo: "Click to Unlock work order.",
    isLockedButton: "btn-outline-danger",
    lockHeader: "Locked",
    lockIcon: "fa-unlock-alt",
    outline: "border-primary"
  };
  const unlocked = {
    clickTo: "Click to Lock work order.",
    isLockedButton: "btn-outline-success",
    lockHeader: "Unlocked",
    lockIcon: "fa-lock",
    outline: "border-warning"
  };
  const updateUI = (add, remove) => {
    isLockedButton.classList.add(add.isLockedButton);
    lockIcon.classList.add(add.lockIcon);
    outline.classList.add(add.outline);

    isLockedButton.classList.remove(remove.isLockedButton);
    lockIcon.classList.remove(remove.lockIcon);
    outline.classList.remove(remove.outline);

    $('[data-toggle="tooltip"]').attr("data-original-title", add.clickTo);
  };

  if (isLockedButton.dataset.isLocked === "true") {
    updateUI(locked, unlocked);
    lockHeader.innerText = locked.lockHeader;
  } else {
    updateUI(unlocked, locked);
    lockHeader.innerText = unlocked.lockHeader;
  }
  inventoryButton.disabled = isLockedButton.dataset.isLocked === "true";
}

$(function() {
  $('[data-toggle="tooltip"]').tooltip();
  $("#productModal").on("shown.bs.modal", event => {
    // Defer attaching event listener until modal opens
    // Because #product_type is not attached until modal opens
    document
      .getElementById("product_type")
      .addEventListener("change", event => {
        const { value } = event.target;
        const select = document.getElementById("product_type");
        let $formContainer = $(document.getElementById("typeForm"));
        axios
          .get(`/types/${value}`, value)
          .then(response => {
            const formData = response.data;
            formData.unshift(
              {
                type: "header",
                className: "mt-3",
                label: select.options[select.selectedIndex].innerText,
                subtype: "h3"
              },
              {
                className: "form-control",
                dataAutocomplete: "/ajaxsearch/manufacturer",
                label: "Manufacturer",
                name: "manufacturer",
                required: "true",
                subtype: "text",
                type: "text"
              },
              {
                className: "form-control",
                dataAutocomplete: "/ajaxsearch/model",
                label: "Model",
                name: "model",
                required: "true",
                subtype: "text",
                type: "text"
              }
            );

            $("form", $formContainer).formRender({
              formData: formData
            });
            // Add autocomplete to Manufacturer
            AutoComplete();
            // Add autocomplete to Model
          })
          .catch(error => {
            console.log("error");
            console.log(error);
            // Create warning alert
            // Attach warning alert as a child to $formContainer
            /*
<div class="alert alert-warning" role="alert">
A simple warning alert—check it out!
</div>
*/
            let alert = document.createElement("div");
            alert.classList.add("alert", "alert-warning");
            alert.innerText = error;
            $formContainer.append(alert);
          });
      });
    document
      .getElementById("productSubmit")
      .addEventListener("click", event => {
        const formData = document.getElementById("productForm");
        const postData = {
          type: document.getElementById("product_type").value,
          workOrderId: document.getElementById("lock_button").dataset
            .workOrderId
        };
        for (let i = 0; i < formData.length; i++) {
          postData[formData[i].name] = formData[i].value;
        }
        // Post Form
        const url = "/products";
        axios
          .post(url, postData)
          .then(response => {
            const { id, model, created_at } = response.data;
            const { name: manufacturer } = response.data.manufacturer;
            const { name: type } = response.data.type;
            // Add Row to `<tbody id="products_table">`
            const tr = document.createElement("tr");
            tr.innerHTML = `<td>${id}</td>\
<td>${manufacturer}</td>\
<td>${model}</td>\
<td>${type}</td>\
<td>${created_at}</td>`;
            document.getElementById("products_table").appendChild(tr);
            const productForm = document.getElementById("productForm");
            while (productForm.hasChildNodes()) {
              productForm.removeChild(productForm.lastChild);
            }
            // close modal
            $("#productModal").modal("hide");
            $(".modal-backdrop").remove();
          })
          .catch(error => {
            console.log(error);
            const errorAlert = document.createElement("div");
            errorAlert.classList.add(
              "alert",
              "alert-warning",
              "alert-dismissible",
              "fade",
              "show"
            );
            errorAlert.innerHTML = `${error}\
<button type="button" class="close" data-dismiss="alert" aria-label="Close">\
<span aria-hidden="true">&times;</span>\
</button>`;
            document.getElementById("productError").appendChild(errorAlert);
          });
      });
  });

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
    const isLockedButton = document.getElementById("lock_button");
    const updateAlert = document.createElement("div");
    const alertRow = document.getElementById("alert_row");
    while (alertRow.hasChildNodes()) {
      alertRow.removeChild(alertRow.lastChild);
    }

    const url = `/workorders/${isLockedButton.dataset.workOrderId}`;
    const data = {
      company_name: document.getElementById("company_name").value,
      email: document.getElementById("email").value,
      first_name: document.getElementById("first_name").value,
      intake: document.getElementById("intake").value,
      last_name: document.getElementById("last_name").value,
      phone_number: document.getElementById("phone_number").value
    };

    // send PATCH via axios
    axios
      .patch(url, data)
      .then(response => {
        // At some point, give user a visual indication that fields have been
        // updated. Even better, add onChange to the fields, and if
        // they're dirty, give visual indication when changed.
        updateAlert.classList.add(
          "alert",
          "alert-dismissible",
          "alert-success",
          "col-6",
          "fade",
          "mt-3",
          "offset-3",
          "shadow",
          "show"
        );
        updateAlert.innerHTML = `<h4 class="alert-heading">Success</h4>\
<p>Work Order successfully updated.</p>\
<button type="button" class="close" data-dismiss="alert" aria-label="Close">\
<span aria-hidden="true">&times;</span>\
</button>`;
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
