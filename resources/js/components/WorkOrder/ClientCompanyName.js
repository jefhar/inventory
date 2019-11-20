import React from "react";
import { AsyncTypeahead } from "react-bootstrap-typeahead";
import "react-bootstrap-typeahead/css/Typeahead.css";

function ClientCompanyName(props) {
    return (
        <AsyncTypeahead
            isLoading={props.isLoading}
            onSearch={props.onSearch}
            options={props.options}
            labelKey={props.labelKey}
            selected={props.selected}
            allowNew={true}
            autoFocus={true}
            bsSize="sm"
            inputProps={{
                name: "company_name",
                required: "required",
                className: "form-control form-control-sm"
            }}
            placeholder={props.placeholder}
            newSelectionPrefix={props.newSelectionPrefix}
            id={props.id}
            selectHintOnEnter={true}
        />
    );
}

export default ClientCompanyName;
