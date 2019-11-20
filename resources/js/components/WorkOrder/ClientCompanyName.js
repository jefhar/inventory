import React, { Component } from "react";
import { AsyncTypeahead } from "react-bootstrap-typeahead";
import "react-bootstrap-typeahead/css/Typeahead.css";

class ClientCompanyName extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: false
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

    render() {
        return (
            <AsyncTypeahead
                isLoading={this.state.isLoading}
                onSearch={query => {
                    this.setState({ isLoading: true });
                    axios
                        .get(`/ajaxsearch/company_name?q=${query}`)
                        .then(response => {
                            this.setState({
                                isLoading: false,
                                options: response.data
                            });
                        })
                        .catch(error => {
                            console.debug(error);
                        });
                }}
                defaultSelected={[]}
                id="client.company_name"
                name="client_company_name"
                required="required"
                placeholder="Client's company name"
                className=" form-control form-control-sm"
                options={this.state.options}
                labelKey="company_name"
            />
        );
    }
}

export default ClientCompanyName;
