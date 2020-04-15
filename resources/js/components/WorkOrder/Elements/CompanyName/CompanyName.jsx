import * as React from 'react'
import { AsyncTypeahead } from 'react-bootstrap-typeahead'
import 'react-bootstrap-typeahead/css/Typeahead.css'

function CompanyName (props) {
  return (
    <AsyncTypeahead
      allowNew={true}
      autoFocus={true}
      bsSize="sm"
      id={props.id}
      inputProps={{
        className: 'form-control form-control-sm',
        name: 'company_name',
        required: 'required'
      }}
      isLoading={props.isLoading}
      labelKey={props.labelKey}
      newSelectionPrefix={props.newSelectionPrefix}
      onBlur={props.onBlur}
      onChange={props.handleChange}
      onSearch={props.onSearch}
      options={props.options}
      placeholder={props.placeholder}
      selected={props.selected}
      selectHintOnEnter={true}
    />
  )
}

export default CompanyName
