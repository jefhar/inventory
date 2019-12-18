/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */
// import React from 'react'
/*
require('jqueryui')
require('jqueryui-sortable')
require('formBuilder')
require('formBuilder/dist/form-render.min')
*/
import $ from "jquery";

window.jQuery = $;
window.$ = $;

require("jquery-ui-sortable");
require("formBuilder");
require("formBuilder/dist/form-render.min");

const AutoComplete = require("autocomplete-js");

/**
 * Toggles the edit mode for the demo
 * @return {Boolean} editMode
 */
function toggleEdit() {
  const button = document.getElementById("previewButton");
  const editing = button.dataset.showOnClick;
  let renderPreview = false;
  console.info("toggleEdit: " + editing);
  if (editing === "preview") {
    button.innerHTML = 'Edit&emsp;&emsp;<i class="far fa-edit"></i>';
    button.dataset.showOnClick = "edit";
  } else {
    button.innerHTML = 'Preview&emsp;&emsp;<i class="far fa-eye"></i>';
    button.dataset.showOnClick = "preview";
    renderPreview = true;
  }
  document
    .getElementById("formbuilder")
    .classList.toggle("form-rendered", !renderPreview);
}

jQuery($ => {
  const options = {
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
      toggleEdit(false);
      $("#fb-render").formRender({ formData });
      console.info(JSON.stringify(formData));
    },
    showActionButtons: false
  };
  const formBuilder = $("#fb-editor").formBuilder(options);
  $("#fb-render").formRender();

  document.getElementById("previewButton").onclick = function() {
    console.info("clicked previewButton");
    toggleEdit();
    const formData = formBuilder.actions.getData("json", true);
    console.info(formData);
    $("#fb-render").formRender({ formData });
  };
});
