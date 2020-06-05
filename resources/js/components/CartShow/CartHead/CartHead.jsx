import * as React from 'react'
import PropTypes from 'prop-types'
import { CardHeader } from 'reactstrap'
import CartIcon from '../../CartIcon'

const propTypes = {
  cartId: PropTypes.number,
  status: PropTypes.string,
  companyName: PropTypes.string,
  firstName: PropTypes.string,
  lastName: PropTypes.string,
  phoneNumber: PropTypes.string,
  createdAt: PropTypes.string,
}
const defaultProps = {
  cartId: 1,
  status: 'secondary',
  companyName: '########## ########### ###',
  firstName: '########',
  lastName: '######',
  phoneNumber: '(###) ###-####x##',
  createdAt: 'Thu Jan 01 00:00:00 1970 UTC',
}

const CartHead = (props) => {
  return (
    <CardHeader>
      <h1>
        <CartIcon />
        Cart # {props.cartId}:{' '}
        <span id="cartStatus" className="capitalize">
          {props.status}
        </span>
      </h1>
      <p className="lead">{props.companyName}</p>
      <p>
        {props.firstName} {props.lastName}
        <i className="ml-4 mr-1 fas fa-phone-alt"></i>
        {props.phoneNumber}
        <br />
        Created at {props.createdAt}
      </p>
    </CardHeader>
  )
}

CartHead.propTypes = propTypes
CartHead.defaultProps = defaultProps
export default CartHead
