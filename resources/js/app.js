/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

require("./bootstrap");

// Add AutoComplete [in NavBar]
window.AutoComplete = require("autocomplete-js");
/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
// Copy to entrypoint path and add related line to webpack.mix.js
// Anything here will be on all pages.
AutoComplete();

$(function() {
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
        console.log("_RenderResponseItems");
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
        console.log("_Post");
        console.log(JSON.parse(response));
        const json = JSON.parse(response);
        console.log("/_Post");
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
