import * as React from 'react'
import PropTypes from 'prop-types'
import { Row } from 'reactstrap'
import DropButton from '../../../DropButton'
import InvoiceButton from '../../../InvoiceButton'

const propTypes = {
  cartId: PropTypes.number,
  cartStatus: PropTypes.string,
  changeStatusRequest: PropTypes.func,
  disabled: PropTypes.bool,
  totalCost: PropTypes.number,
  className: PropTypes.string,
}
const defaultProps = {
  cartId: 0,
  disabled: true,
  totalCost: 0,
}

const ControlPanel = (props) => {
  const {
    cartId,
    className,
    cartStatus,
    disabled,
    totalCost,
    changeStatusRequest,
    ...other
  } = props

  const rowClass = `${props.className} justify-content-around mb-2`
  return (
    <>
      <Row className={rowClass}>
        <InvoiceButton
          className="mx-5"
          onClick={changeStatusRequest.bind(this, 'invoice')}
          disabled={disabled}
          {...other}
        >
          Mark Invoiced
        </InvoiceButton>
        <DropButton
          className="mx-5"
          onClick={changeStatusRequest.bind(this, 'void')}
          disabled={disabled}
          {...other}
        >
          Destroy Cart
        </DropButton>
      </Row>
      <Row className="justify-content-end">
        <span>Cart Total: ${totalCost.toFixed(2)}</span>
      </Row>
    </>
  )
}

ControlPanel.propTypes = propTypes
ControlPanel.defaultProps = defaultProps
export default ControlPanel
