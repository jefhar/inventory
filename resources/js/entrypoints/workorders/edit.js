/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */
import React from "react";
import axios from "axios";

require("formBuilder/dist/form-builder.min");
require("formBuilder/dist/form-render.min");

/**
 * Next, we may create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const AutoComplete = require("autocomplete-js");

function update() {
  const inventoryButton = document.getElementById("addInventoryButton");
  const isLockedButton = document.getElementById("lockButton");
  const lockIcon = document.getElementById("lockIcon");
  const outline = document.getElementById("outline");
  const lockHeader = document.getElementById("lockedHeader");
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
    if (isLockedButton) {
      isLockedButton.classList.add(add.isLockedButton);

      lockIcon.classList.add(add.lockIcon);
    }
    outline.classList.add(add.outline);

    if (isLockedButton) {
      isLockedButton.classList.remove(remove.isLockedButton);

      lockIcon.classList.remove(remove.lockIcon);
    }
    outline.classList.remove(remove.outline);

    $('[data-toggle="tooltip"]').attr("data-original-title", add.clickTo);
  };

  if (document.getElementById("workOrderBody").dataset.isLocked === "true") {
    updateUI(locked, unlocked);
    lockHeader.innerText = locked.lockHeader;
  } else {
    updateUI(unlocked, locked);
    lockHeader.innerText = unlocked.lockHeader;
  }
  if (inventoryButton) {
    inventoryButton.disabled =
      document.getElementById("workOrderBody").dataset.isLocked === "true";
  }
}

$(function() {
  $('[data-toggle="tooltip"]').tooltip();
  $("#productModal").on("shown.bs.modal", event => {
    // Defer attaching event listener until modal opens
    // Because #productType is not attached until modal opens
    document.getElementById("productType").addEventListener("change", event => {
      const { value } = event.target;
      const select = document.getElementById("productType");
      let $formContainer = $(document.getElementById("typeForm"));
      let spinner = document.getElementById("spinner");
      spinner.classList.remove("invisible");
      spinner.classList.add("visible");
      console.log("visible");

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
          AutoComplete({
            _Cache: function(value) {
              value += this.Input.name;
              return this.$Cache[value];
            }
          });
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
        })
        .finally(() => {
          spinner.classList.remove("visible");
          spinner.classList.add("invisible");
        });
    });
    document
      .getElementById("productSubmit")
      .addEventListener("click", event => {
        const formData = document.getElementById("productForm");
        const postData = {
          type: document.getElementById("productType").value,
          workOrderId: document.getElementById("workOrderBody").dataset
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
            document.getElementById("productType").selectedIndex = 0;
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
    document.getElementById("cancelButton").addEventListener("click", event => {
      const productForm = document.getElementById("productForm");
      while (productForm.hasChildNodes()) {
        productForm.removeChild(productForm.lastChild);
      }
      document.getElementById("productType").selectedIndex = 0;
    });
  });

  // Lock/Unlock work order
  if (document.getElementById("lockButton")) {
    document.getElementById("lockButton").addEventListener("click", () => {
      const wantOrderToBeLocked = !(
        document.getElementById("workOrderBody").dataset.isLocked === "true"
      );
      const data = { is_locked: wantOrderToBeLocked };
      const url = `/workorders/${
        document.getElementById("workOrderBody").dataset.workOrderId
      }`;
      axios
        .patch(url, data)
        .then(response => {
          document.getElementById("workOrderBody").dataset.isLocked =
            response.data.is_locked;
          update();
        })
        .catch(error => {
          console.log(error.response.data);
        });
    });
  }
  // Submit changed field data to UPDATE
  if (document.getElementById("update_button")) {
    document
      .getElementById("update_button")
      .addEventListener("click", event => {
        const cardBody = document.getElementById("workOrderBody");
        const updateToast = document.createElement("div");
        const url = `/workorders/${cardBody.dataset.workOrderId}`;
        const data = {
          company_name: document.getElementById("company_name").value,
          email: document.getElementById("email").value,
          first_name: document.getElementById("first_name").value,
          intake: document.getElementById("intake").value,
          last_name: document.getElementById("last_name").value,
          phone_number: document.getElementById("phone_number").value
        };

        updateToast.id = "updateToast";
        updateToast.classList.add("toast");
        updateToast.style.position = "absolute";
        updateToast.style.top = "0";
        updateToast.style.right = "0";
        updateToast.dataset.delay = "8000";

        // send PATCH via axios
        axios
          .patch(url, data)
          .then(response => {
            // At some point, give user a visual indication that fields have been
            // updated. Even better, add onChange to the fields, and if
            // they're dirty, give visual indication when changed.

            updateToast.innerHTML = `\  
    <div class="toast-header">
      <i class="fas fa-check-square text-success"></i>&nbsp;
      <strong class="mr-auto">Success</strong>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body">
      Work Order successfully updated.
    </div>`;
          })
          .catch(error => {
            console.debug(error);
            updateToast.innerHTML = `  <div
  style="position: absolute; top: 0; right: 0;"
  class="toast"
  role="alert"
  aria-live="assertive" 
  aria-atomic="true">
    <div class="toast-header">
      <i class="fas fa-exclamation-circle text-warning"></i>&nbsp;
      <strong class="mr-auto">Warning</strong>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body">
      ${error}
    </div>
  </div>`;
          })
          .finally(() => {
            document.getElementById("workOrderBody").appendChild(updateToast);
            const $updateToast = $("#updateToast");
            $updateToast.toast();
            $updateToast.toast("show");
          });

        event.preventDefault();
      });
  }
  update();
});
