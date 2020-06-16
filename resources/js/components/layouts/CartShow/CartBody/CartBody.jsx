import React from 'react'
import ControlPanel from './ControlPanel/ControlPanel'
import ProductList from './ProductList'
import PropTypes from 'prop-types'
import { CardBody } from 'reactstrap'
import PriceModal from '../../../Modals/PriceModal'

const propTypes = {
  cartId: PropTypes.number,
  cartStatus: PropTypes.string,
  changeStatusRequest: PropTypes.func,
  products: PropTypes.array,
  disabled: PropTypes.bool,
  padding: PropTypes.number,
}

const defaultProps = {
  cartStatus: 'void',
}

class CartBody extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      currentProducts: this.props.products,
      priceModal: {
        isOpen: false,
        productId: 0,
      },
      totalCost: 0,
    }
    this.togglePriceModal = this.togglePriceModal.bind(this)
  }

  // Manipulate this.state.currentProducts Here

  togglePriceModal(productId) {
    if (typeof productId !== 'number') {
      this.setState((state) => {
        return {
          priceModal: {
            isOpen: !state.priceModal.isOpen,
          },
        }
      })
    } else {
      const product = this.props.products.filter(
        (product) => product.id === productId
      )

      this.setState((state) => {
        return {
          priceModal: {
            isOpen: !state.priceModal.isOpen,
            product: product[0],
            originalPrice: product[0].price,
          },
        }
      })
    }
  }

  changePrice(product, event) {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value
    product.price = parseFloat(value)
    this.setState({
      product: product,
    })
  }

  handleDropClick(productId) {
    // It's already dropped by the time code gets here...
    console.info('Requested drop of Product ', productId)
  }

  resetPriceAndClose() {
    this.this.togglePriceModal()
  }

  calculateTotalCost() {
    // return 69.96
    let totalPrice = 0
    const products = this.props.products
    for (let i = 0; i < products.length; ++i) {
      totalPrice += products[i].price
    }

    return Number.parseFloat(totalPrice).toFixed(2)
  }

  render() {
    const { cartId, cartStatus, changeStatusRequest, padding } = this.props

    const totalCost = this.calculateTotalCost()

    return (
      <CardBody>
        <ControlPanel
          cartId={cartId}
          cartStatus={cartStatus}
          changeStatusRequest={changeStatusRequest}
          disabled={this.props.disabled}
          totalCost={totalCost}
        />
        <ProductList
          cartId={cartId}
          cartStatus={cartStatus}
          disabled={this.props.disabled}
          handleDropClick={this.handleDropClick}
          handlePriceClick={this.togglePriceModal}
          padding={padding}
          products={this.props.products}
        />
        <PriceModal
          changePrice={this.changePrice.bind(
            this,
            this.state.priceModal.product
          )}
          isOpen={this.state.priceModal.isOpen}
          originalPrice={this.state.priceModal.originalPrice}
          product={this.state.priceModal.product}
          toggle={this.togglePriceModal}
        />
      </CardBody>
    )
  }
}

CartBody.defaultProps = defaultProps
CartBody.propTypes = propTypes

export default CartBody
