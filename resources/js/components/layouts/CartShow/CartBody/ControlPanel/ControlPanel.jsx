import * as React from 'react'
import DropButton from '../../../../Buttons/DropButton'
import InvoiceButton from '../../../../Buttons/InvoiceButton'
import PropTypes from 'prop-types'
import { Row } from 'reactstrap'

const propTypes = {
  cartId: PropTypes.number,
  cartStatus: PropTypes.string,
  changeStatusRequest: PropTypes.func,
  className: PropTypes.string,
  disabled: PropTypes.bool,
  totalCost: PropTypes.number,
}
const defaultProps = {
  cartId: 0,
  disabled: true,
  totalCost: 0,
}

const ControlPanel = (props) => {
  const {
    cartId,
    cartStatus,
    changeStatusRequest,
    className,
    disabled,
    totalCost,
    ...other
  } = props

  const rowClass = `${props.className} justify-content-around mb-2`
  return (
    <>
      <Row className={rowClass}>
        <InvoiceButton
          className="mx-5"
          disabled={disabled}
          onClick={changeStatusRequest.bind(this, 'invoiced')}
          {...other}
        >
          Mark Invoiced
        </InvoiceButton>
        <DropButton
          className="mx-5"
          disabled={disabled}
          onClick={changeStatusRequest.bind(this, 'void')}
          {...other}
        >
          Destroy Cart
        </DropButton>
      </Row>
      <Row className="justify-content-end">
        <span className="h5">Cart Total: ${totalCost.toFixed(2)}</span>
      </Row>
    </>
  )
}

ControlPanel.defaultProps = defaultProps
ControlPanel.propTypes = propTypes

export default ControlPanel
