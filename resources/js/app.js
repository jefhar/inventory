/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

require("./bootstrap");
require("jquery-ui-sortable");
require("formBuilder");
require("formBuilder/dist/form-render.min");
const AutoComplete = require("autocomplete-js");
require("./components/WorkOrder/WorkOrderIndex");
require("./components/WorkOrder/WorkOrderCreate");
const HTTP_OK = 200;
const HTTP_CREATED = 201;
const HTTP_ACCEPTED = 202;
AutoComplete();

$(() => {
  AutoComplete(
    {
      EmptyMessage: "No results found",
      HttpHeaders: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
      },
      MinChars: 2,
      Url: "/ajaxsearch",
      _RenderResponseItems: function(response) {
        let ul = document.createElement("ul");

        let limit = this._Limit();
        if (limit < 0) {
          response = response.reverse();
        } else if (limit === 0) {
          limit = response.length;
        }

        // This is where this implementation is different:
        for (
          let item = 0;
          item < Math.min(Math.abs(limit), response.length);
          item++
        ) {
          let li = document.createElement("li");
          let a = document.createElement("a");
          a.href = response[item].Url;
          a.innerText = response[item].Label;
          li.appendChild(a);
          li.setAttribute("data-autocomplete-value", response[item].Value);
          ul.appendChild(li);
        }
        return ul;
      },
      _Post: function(response) {
        const json = JSON.parse(response);
        let returnResponse = [];
        if (Array.isArray(json)) {
          console.log("json is array");
          for (let i = 0; i < Object.keys(json).length; i++) {
            console.log(json[i]);

            returnResponse[returnResponse.length] = {
              Value: json[i].name,
              Label: json[i].name,
              Url: json[i].url
            };
          }
        } else {
          console.log("json is not array");
          for (let value in json) {
            console.debug(value);
          }
        }
        return returnResponse;
      }
    },
    "#site-search"
  );
});

if (document.getElementById("workorders_edit")) {
  console.info("workorders_edit");

  function WE_update() {
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
          console.info("error");
          console.info(error);
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
            const { luhn, model, created_at } = response.data;
            const { name: manufacturer } = response.data.manufacturer;
            const { name: type } = response.data.type;
            // Add Row to `<tbody id="products_table">`
            const tr = document.createElement("tr");
            tr.innerHTML = `<td>${luhn}</td>\
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
            console.info(error);
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
          WE_update();
        })
        .catch(error => {
          console.info(error.response.data);
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
            // At some point, give user a visual indication that fields have
            // been updated. Even better, add onChange to the fields, and if
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
  WE_update();
}

if (document.getElementById("types_create")) {
  console.info("types_create");
  const TC_formBuilderOptions = {
    controlOrder: [
      "text",
      "number",
      "select",
      "checkbox-group",
      "radio-group",
      "date",
      "textarea"
    ],
    dataType: "json",
    disabledSubtypes: {
      text: ["password", "color", "email"]
    },
    disableFields: [
      "autocomplete",
      "button",
      "file",
      "header",
      "hidden",
      "paragraph",
      "starRating"
    ],
    fieldRemoveWarn: true,
    onSave: (event, formData) => {
      TC_toggleEdit(false);
      $("#fb-render").formRender({ formData });
      console.info(JSON.stringify(formData));
    },
    showActionButtons: false
  };

  function TC_toggleEdit() {
    const previewButton = document.getElementById("previewButton");
    const editing = previewButton.dataset.showOnClick;
    let renderPreview = false;
    if (editing === "preview") {
      previewButton.innerHTML = 'Edit&emsp;&emsp;<i class="far fa-edit"></i>';
      previewButton.dataset.showOnClick = "edit";
    } else {
      previewButton.innerHTML = 'Preview&emsp;&emsp;<i class="far fa-eye"></i>';
      previewButton.dataset.showOnClick = "preview";
      renderPreview = true;
    }
    document
      .getElementById("formbuilder")
      .classList.toggle("form-rendered", !renderPreview);
  }

  /**
   * @return {boolean}
   */
  function TC_checkAndClear(formBuilder) {
    const formData = formBuilder.actions.getData("json", true);
    if (formData !== "[]") {
      if (window.confirm("Are you sure you want to clear all fields?")) {
        formBuilder.actions.clearFields();
        $("#fb-render").formRender({ formData });
        return true;
      }
      return false;
    }
    return true;
  }

  const TC_formBuilder = $("#fb-editor").formBuilder(TC_formBuilderOptions);
  $("#fb-render").formRender();

  document.getElementById("previewButton").onclick = () => {
    TC_toggleEdit();
    const formData = TC_formBuilder.actions.getData("json", true);
    $("#fb-render").formRender({ formData });
  };

  document.getElementById("clearButton").onclick = () => {
    TC_checkAndClear(TC_formBuilder);
  };

  document.getElementById("loadButton").onclick = () => {
    if (!TC_checkAndClear(TC_formBuilder)) {
      return;
    }

    console.info("attempting to launch modal.");
    $("#loadProductModal").modal("show");
  };

  document.getElementById("saveButton").onclick = () => {
    const formData = TC_formBuilder.actions.getData("json", true);
    if (formData === "[]") {
      alert("Nothing to save!");
      return;
    }

    // open save dialog modal
    $("#saveProductModal").modal("show");
  };

  $("#saveProductModal").on("shown.bs.modal", event => {
    document
      .getElementById("saveTypeButton")
      .addEventListener("click", event => {
        console.info("Save button clicked.");
        const typeName = document.getElementById("saveType").value;
        const formData = TC_formBuilder.actions.getData("json", true);
        let alert = "";
        _.throttle(
          axios
            .post("/types", {
              form: formData,
              name: typeName
            })
            .then(response => {
              if (response.status === HTTP_CREATED) {
                console.info("created.");
                document.getElementById(
                  "alert"
                ).innerHTML = `<div class="alert alert-success alert-dismissible fade show" role="alert">\
<h5>Product Type Saved.</h5>You may now use ${response.data.name} as a product type.\
<button type="button" class="close" data-dismiss="alert" aria-label="Close">\
<span aria-hidden="true">&times;</span>\
</button>\
</div>`;
              } else if (response.status === HTTP_ACCEPTED) {
                console.info("accepted.");
                const resave = window.confirm(
                  "Type already exists. Press OK to update, CANCEL to rename"
                );
                if (resave) {
                  axios
                    .post("/types", {
                      force: true,
                      form: formData,
                      name: typeName
                    })
                    .then(response => {
                      if (response.status === HTTP_OK) {
                        console.info("forced created.");
                        document.getElementById(
                          "alert"
                        ).innerHTML = `<div class="alert alert-info alert-dismissible fade show" role="alert">\
<h5>Product Type Updated.</h5>${response.data.name} has been updated. Existing products of this type have not been updated.\
<button type="button" class="close" data-dismiss="alert" aria-label="Close">\
<span aria-hidden="true">&times;</span>\
</button>\
</div>`;
                      } else {
                        console.info("unable to force create.");
                        document.getElementById(
                          "alert"
                        ).innerHTML = `<div class="alert alert-warning alert-dismissible fade show" role="alert">\
<h5>Saving failed.</h5>${typeName} has not been updated. Existing products of this type have not been updated.\
<strong>An unexpected response was tendered from the server. Please try again later.</strong>
<button type="button" class="close" data-dismiss="alert" aria-label="Close">\
<span aria-hidden="true">&times;</span>\
</button>\
</div>`;
                      }
                    });
                } else {
                  console.info("not force updating.");
                  document.getElementById(
                    "alert"
                  ).innerHTML = `<div class="alert alert-warning alert-dismissible fade show" role="alert">\
<h5>Saving canceled.</h5>${typeName} has not been saved. Please choose a different name for the product type.\
<button type="button" class="close" data-dismiss="alert" aria-label="Close">\
<span aria-hidden="true">&times;</span>\
</button>\
</div>`;
                }
              } else {
                console.info(response);
                console.info("Hmm.");
              }
            })
            .finally(response => {
              $("#saveProductModal").modal("hide");
              // Refresh typesList from server
              const typesList = document.getElementById("typesList");
              while (typesList.hasChildNodes()) {
                typesList.removeChild(typesList.lastChild);
              }

              axios.get("/types").then(response => {
                response.data.forEach(item => {
                  const option = document.createElement("option");
                  option.value = item.slug;
                  option.innerText = item.name;
                  typesList.append(option);
                });
              });
              console.info("setting timeout?");
              window.setTimeout(() => {
                if (document.getElementById("alert")) {
                  $(".alert").alert("close");
                }
              }, 8000);
            }),
          250,
          { leading: true, trailing: false }
        );
      });
  });

  $("#loadProductModal").on("shown.bs.modal", event => {
    // Defer attaching event listener until modal opens
    // Because #productType is not attached until modal opens
    document
      .getElementById("loadTypeButton")
      .addEventListener("click", event => {
        // Save selected slug
        const index = document.getElementById("typesList").selectedIndex;
        const value = document.getElementById("typesList").value;
        const spinner = document.getElementById("spinner");
        const formBuilder = document.getElementById("formbuilder");

        $("#loadProductModal").modal("hide");
        spinner.classList.remove("invisible");
        spinner.classList.add("visible");
        formBuilder.classList.remove("visible");
        formBuilder.classList.add("invisible");

        axios
          .get(`/types/${value}`, value)
          .then(response => {
            const formData = response.data;
            /*
        // These need an ID so they can be removed via formBuilder.actions.removeField('tmp_header');
        // Or maybe add them via prepend
        formData.unshift(
          {
          id: tmp_header
            type: 'header',
            className: 'mt-3',
            label: select.options[select.selectedIndex].innerText,
            subtype: 'h3'
          },
          {
            className: 'form-control',
            dataAutocomplete: '/ajaxsearch/manufacturer',
            label: 'Manufacturer',
            name: 'manufacturer',
            required: 'true',
            subtype: 'text',
            type: 'text'
          },
          {
            className: 'form-control',
            dataAutocomplete: '/ajaxsearch/model',
            label: 'Model',
            name: 'model',
            required: 'true',
            subtype: 'text',
            type: 'text'
          }
        )
         */

            TC_toggleEdit();
            $("#fb-render").formRender({ formData });
            TC_formBuilder.actions.setData(formData);
            document.getElementById("productType").innerHTML =
              "<h5>" +
              document.getElementById("typesList")[index].label +
              "</h5>";
          })
          .catch(error => {
            console.info("error");
            console.info(error);
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
            document.getElementById("formbuilder").append(alert);
          })
          .finally(() => {
            spinner.classList.remove("visible");
            spinner.classList.add("invisible");
            formBuilder.classList.add("visible");
            formBuilder.classList.remove("invisible");
          });
      });
  });
}
