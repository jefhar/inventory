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
                options={this.state.options}
                labelKey="company_name"
                allowNew={true}
                autoFocus={true}
                bsSize="sm"
                inputProps={{
                    name: "company_name",
                    required: "required",
                    className: "form-control form-control-sm"
                }}
                placeholder="Client's company name"
                newSelectionPrefix="New Client:"
                id="client.company_name"
                selectHintOnEnter={true}
            />
        );
    }
}

export default ClientCompanyName;
