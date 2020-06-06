import React from 'react'
import ControlPanel from './ControlPanel/ControlPanel'
import ProductList from './ProductList'
import PropTypes from 'prop-types'
import { CardBody } from 'reactstrap'

const propTypes = {
  cartId: PropTypes.number,
  cartStatus: PropTypes.string,
  changeStatusRequest: PropTypes.func,
  padding: PropTypes.number,
  startingProducts: PropTypes.string,
}
const defaultProps = {
  cartStatus: 'void',
}

class CartBody extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      currentProducts: JSON.parse(this.props.startingProducts),
      disabled: this.props.cartStatus !== 'open',
      totalCost: 0,
    }
  }

  // Manipulate this.state.currentProducts Here

  handlePriceClick(productId) {
    console.info('Requested click of price button for Product ', productId)
  }

  handleDropClick(productId) {
    console.info('Requested drop of Product ', productId)
  }

  calculateTotalCost() {
    return 69.96
  }

  render() {
    const totalCost = this.calculateTotalCost()
    return (
      <CardBody>
        <ControlPanel
          cartId={this.props.cartId}
          cartStatus={this.props.cartStatus}
          changeStatusRequest={this.props.changeStatusRequest}
          disabled={this.state.disabled}
          totalCost={totalCost}
        />
        <ProductList
          cartId={this.props.cartId}
          cartStatus={this.props.cartStatus}
          disabled={this.state.disabled}
          handleDropClick={this.handleDropClick}
          handlePriceClick={this.handlePriceClick}
          padding={this.props.padding}
          products={this.state.currentProducts}
        />
      </CardBody>
    )
  }
}

CartBody.defaultProps = defaultProps
CartBody.propTypes = propTypes

export default CartBody
