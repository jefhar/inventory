import * as React from 'react'
import PropTypes from 'prop-types'
import { AsyncTypeahead } from 'react-bootstrap-typeahead'
import 'react-bootstrap-typeahead/css/Typeahead.css'
import 'react-bootstrap-typeahead/css/Typeahead-bs4.css'

const propTypes = {
  className: PropTypes.string,
  handleChange: PropTypes.func.isRequired,
  id: PropTypes.string.isRequired,
  isLoading: PropTypes.bool.isRequired,
  labelKey: PropTypes.string.isRequired,
  newSelectionPrefix: PropTypes.string.isRequired,
  onBlur: PropTypes.func.isRequired,
  onSearch: PropTypes.func.isRequired,
  options: PropTypes.array,
  placeholder: PropTypes.string.isRequired,
  selected: PropTypes.any,
  valid: PropTypes.bool,
}

const CompanyName = ({
  className,
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
  valid,
}) => {
  const isValid = valid
  const isInvalid = !valid
  return (
    <AsyncTypeahead
      allowNew={true}
      autoFocus={true}
      id={id}
      inputProps={{
        className: `${className}`,
        name: 'company_name',
        required: 'required',
      }}
      isInvalid={isInvalid}
      isLoading={isLoading}
      isValid={isValid}
      labelKey={labelKey}
      newSelectionPrefix={newSelectionPrefix}
      onBlur={onBlur}
      onChange={handleChange}
      onSearch={onSearch}
      options={options}
      placeholder={placeholder}
      selected={selected}
      selectHintOnEnter={true}
      size="sm"
    />
  )
}

CompanyName.propTypes = propTypes

export default CompanyName
