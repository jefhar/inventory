import * as React from 'react'
import CartIcon from '../../CartIcon'
import PropTypes from 'prop-types'
import { CardHeader } from 'reactstrap'

const propTypes = {
  cartId: PropTypes.number,
  companyName: PropTypes.string,
  createdAt: PropTypes.string,
  firstName: PropTypes.string,
  lastName: PropTypes.string,
  phoneNumber: PropTypes.string,
  status: PropTypes.string,
}
const defaultProps = {
  cartId: 1,
  companyName: '########## ########### ###',
  createdAt: 'Thu Jan 01 00:00:00 1970 UTC',
  firstName: '########',
  lastName: '######',
  phoneNumber: '(###) ###-####x##',
  status: 'secondary',
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

CartHead.defaultProps = defaultProps
CartHead.propTypes = propTypes

export default CartHead
