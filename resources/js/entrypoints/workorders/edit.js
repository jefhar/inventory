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

jQuery(function($) {
  // $("typeForm").formBuilder();
});
/*
// Sample formbuilder code
jQuery(function($) {
  let $fbEditor = $(document.getElementById("fb-editor"));
  let $formContainer = $(document.getElementById("fb-rendered-form"));
  let fbOptions = {
    onSave: function() {
      $fbEditor.toggle();
      $formContainer.toggle();
      $("form", $formContainer).formRender({
        formData: formBuilder.formData
      });
    }
  };
  let formBuilder = $fbEditor.formBuilder(fbOptions);

  $(".edit-form", $formContainer).click(function() {
    $fbEditor.toggle();
    $formContainer.toggle();
  });
});
*/

$(function() {
  $('[data-toggle="tooltip"]').tooltip();
  $("#productModal")
    .on("show.bs.modal", event => {
      console.log("Modal opening.");
    })
    .on("shown.bs.modal", event => {
      console.log("Modal has opened for business.");

      // Defer attaching event listener until modal opens.
      document
        .getElementById("product_type")
        .addEventListener("change", event => {
          console.log("caught select change.");
          const { value } = event.target;
          let $formContainer = $(document.getElementById("typeForm"));
          axios
            .get(`/types/${value}`, value)
            .then(response => {
              const formData = response.data;

              $("form", $formContainer).formRender({
                formData: formData
              });
            })
            .catch(error => {
              console.log(error);
              // Create warning alert
              // Attach warning alert as a child to $formContainer
              /*
          <div class="alert alert-warning" role="alert">
A simple warning alert—check it out!
</div>
           */
              let alert = document
                .createElement("div")
                .classList.add("alert", "alert-warning");
              alert.innerText = error.data;
              $formContainer.append(alert);
            });
        });
      document
        .getElementById("productSubmit")
        .addEventListener("click", event => {
          const formData = document.getElementById("productForm");
          const postData = {
            type: document.getElementById("product_type").value,
            workorderId: document.getElementById("lock_button").dataset
              .workOrderId
          };
          for (let i = 0; i < formData.length; i++) {
            let child = formData[i];
            postData[formData[i].name] = formData[i].value;
          }
          // Post Form
          const isLockedButton = document.getElementById("lock_button");
          const url = "/products";
          axios
            .post(url, postData)
            .then(response => {
              console.log(response.data);
              // Add Row to `<tbody id="products_table">`
              // destroy form children
              // close modal
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
              errorAlert.innerHTML = `${error}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>`;
              console.log(errorAlert);
              document.getElementById("productError").appendChild(errorAlert);

              // Create alert
              // Add to form
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
        console.log("response");
        console.log(response.data);
        // Give user a visual indication that fields have been
        // updated. Even better, add onChange to the fields, and if
        // they're dirty, give visual indication.
        updateAlert.classList.add(
          "alert",
          "alert-success",
          "col-6",
          "mt-3",
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

  // Product Select creates new form
  const handleProductChange = event => {
    console.log("event: ");
    console.log(event);
  };

  update();
});
