import * as React from 'react'
import PropTypes from 'prop-types'
import { AsyncTypeahead } from 'react-bootstrap-typeahead'
import 'react-bootstrap-typeahead/css/Typeahead.css'
import 'react-bootstrap-typeahead/css/Typeahead-bs4.css'

const CompanyName = ({
  handleChange,
  id,
  isLoading,
  labelKey,
  newSelectionPrefix,
  onBlur,
  onSearch,
  options,
  placeholder,
  selected,
}) => {
  return (
    <AsyncTypeahead
      allowNew={true}
      autoFocus={true}
      size="sm"
      id={id}
      inputProps={{
        className: 'form-control form-control-sm',
        name: 'company_name',
        required: 'required',
      }}
      isLoading={isLoading}
      labelKey={labelKey}
      newSelectionPrefix={newSelectionPrefix}
      onBlur={onBlur}
      onChange={handleChange}
      onSearch={onSearch}
      options={options}
      placeholder={placeholder}
      selected={selected}
      selectHintOnEnter={true}
    />
  )
}

CompanyName.propTypes = {
  handleChange: PropTypes.func.isRequired,
  id: PropTypes.string.isRequired,
  isLoading: PropTypes.bool.isRequired,
  labelKey: PropTypes.string.isRequired,
  newSelectionPrefix: PropTypes.string.isRequired,
  onBlur: PropTypes.func.isRequired,
  onSearch: PropTypes.func.isRequired,
  options: PropTypes.array.isRequired,
  placeholder: PropTypes.string.isRequired,
  selected: PropTypes.any.isRequired,
}

export default CompanyName
