import React, { Component } from "react";
import {
    FormGroup,
    InputGroup,
    InputGroupAddon,
    InputGroupText
} from "reactstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCloudDownloadAlt } from "@fortawesome/free-solid-svg-icons";
import { AsyncTypeahead } from "react-bootstrap-typeahead";
import "react-bootstrap-typeahead/css/Typeahead.css";

class ClientCompanyName extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: false,
            options: [
                {
                    label: "Alexia Corp",
                    first_name: "Alex",
                    last_name: "Alpha",
                    client_id: 1
                },
                {
                    label: "Bablonia Corp",
                    first_name: "Bob",
                    last_name: "Baker",
                    client_id: 2
                },
                {
                    label: "Cucinia Corp",
                    first_name: "Charlie",
                    last_name: "Cook",
                    client_id: 3
                },
                {
                    label: "Dogginia Corp",
                    first_name: "Devon",
                    last_name: "Darko",
                    client_id: 4
                }
            ]
        };
    }

    getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(";");
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == " ") {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    getClients(event) {
        /*
    console.log(this.state.client_company_name)
    const { name, value } = event.target
    axios.get(`/autocomplete/${name}?q=${value}`)
      .then(response => {
        let autocompleteData = {
          items: response.data,
          value: value,
        }
        this.setState({
          [name]: autocompleteData
        })
      })
      .catch((error) => {
        console.debug(error)
      })
    return [
      { label: 'Alexia Corp', first_name: 'Alex', last_name: 'Alpha' },
      { label: 'Bablonia Corp', first_name: 'Bob', last_name: 'Baker' },
      { label: 'Cucinia Corp', first_name: 'Charlie', last_name: 'Cook' },
      { label: 'Dogginia Corp', first_name: 'Devon', last_name: 'Darko' }
    ]
     */
    }

    render() {
        return (
            <AsyncTypeahead
                isLoading={this.state.isLoading}
                onSearch={query => {
                    this.setState({ isLoading: true });
                    fetch(`/ajaxsearch/clients?q=${query}`)
                        .then(resp => resp.json())
                        .then(json =>
                            this.setState({
                                isLoading: false,
                                options: json.items
                            })
                        );
                }}
                defaultSelected={[]}
                id="client.company_name"
                name="client_company_name"
                required="required"
                placeholder="Client\'s company name"
                className="form-control form-control-sm"
                options={this.state.options}
            />
        );
    }
}

export default ClientCompanyName;
