import * as React from 'react'
import CartBody from './CartBody/CartBody'
import CartHead from './CartHead'
import PropTypes from 'prop-types'
import ReactDOM from 'react-dom'
import { Card, Container } from 'reactstrap'

const STATUS_INVOICED = 'invoiced'
const STATUS_OPEN = 'open'
const STATUS_VOID = 'void'

const propTypes = {
  cartCreatedAt: PropTypes.string,
  cartId: PropTypes.string,
  cartStatus: PropTypes.string,
  clientCompanyName: PropTypes.string,
  clientFirstName: PropTypes.string,
  clientLastName: PropTypes.string,
  clientPhoneNumber: PropTypes.string,
  productPadding: PropTypes.number,
  products: PropTypes.string,
}
const defaultProps = {
  cartStatus: STATUS_VOID, // {'invoiced', 'open', 'void'}
}

class CartShow extends React.Component {
  constructor(props) {
    super(props)
    console.info('CartShow props', props)
    this.state = { currentCartStatus: props.cartStatus }
    this.changeStatus = this.changeStatus.bind(this)
  }

  changeStatus(newStatus) {
    console.info(
      `User requests to change status of ${this.props.cartId} to '${newStatus}'`
    )
  }

  render() {
    return (
      <Container>
        <Card className={`border border-cart_${this.state.currentCartStatus}`}>
          <CartHead
            cartId={parseInt(this.props.cartId, 10)}
            companyName={this.props.clientCompanyName}
            createdAt={this.props.cartCreatedAt}
            firstName={this.props.clientFirstName}
            lastName={this.props.clientLastName}
            phoneNumber={this.props.clientPhoneNumber}
            status={this.state.currentCartStatus}
          ></CartHead>
          <CartBody
            cartId={parseInt(this.props.cartId, 10)}
            cartStatus={this.state.currentCartStatus}
            changeStatusRequest={this.changeStatus}
            padding={parseInt(this.props.productPadding, 10)}
            startingProducts={this.props.products}
          ></CartBody>
        </Card>
      </Container>
    )
  }
}

CartShow.defaultProps = defaultProps
CartShow.propTypes = propTypes
CartShow.STATUS_INVOICED = STATUS_INVOICED
CartShow.STATUS_OPEN = STATUS_OPEN
CartShow.STATUS_VOID = STATUS_VOID

export default CartShow

if (document.getElementById('CartShow')) {
  console.log('got CartShow')
  const dataset = document.getElementById('CartShow').dataset
  ReactDOM.render(
    <CartShow {...dataset} />,
    document.getElementById('CartShow')
  )
}
