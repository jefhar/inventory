import * as React from 'react'
import PropTypes from 'prop-types'

import { Button } from 'reactstrap'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faCheckCircle } from '@fortawesome/free-solid-svg-icons/faCheckCircle'

const propTypes = {
  children: PropTypes.string,
  disabled: PropTypes.bool,
  onClick: PropTypes.func,
}

const InvoiceButton = (props) => {
  const { children, disabled, onClick, ...other } = props
  return (
    <Button
      className="pr-1"
      color="primary"
      disabled={disabled}
      onClick={onClick}
      outline={true}
      type="button"
      name="InvoiceCart"
      {...other}
    >
      <>
        <FontAwesomeIcon icon={faCheckCircle} />
        <span className="pl-1">{props.children}</span>
      </>
    </Button>
  )
}

InvoiceButton.propTypes = propTypes
export default InvoiceButton
