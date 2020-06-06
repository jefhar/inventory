import * as React from 'react'
import PropTypes from 'prop-types'
import { Button } from 'reactstrap'
import { faDollarSign } from '@fortawesome/free-solid-svg-icons/faDollarSign'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

const propTypes = {
  disabled: PropTypes.bool,
  onClick: PropTypes.func.isRequired,
  price: PropTypes.number,
  productId: PropTypes.number.isRequired,
}

const PriceButton = (props) => {
  return (
    <Button
      color="secondary"
      disabled={props.disabled}
      onClick={props.onClick}
      outline={true}
      size="sm"
      title="Click to change product price"
    >
      <div style={{ display: 'flex-inline' }}>
        <FontAwesomeIcon icon={faDollarSign} className="green" />
        <span className="pl-1 text-white">{props.price.toFixed(2)}</span>
      </div>
    </Button>
  )
}

PriceButton.propTypes = propTypes

export default PriceButton
