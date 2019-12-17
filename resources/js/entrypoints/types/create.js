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

const AutoComplete = require("autocomplete-js");

jQuery($ => {
  const options = {
    disableFields: [
      "autocomplete",
      "button",
      "file",
      "header",
      "hidden",
      "paragraph",
      "starRating"
    ]
  };
  $("#fb-editor").formBuilder();
  console.info("sup");
});
