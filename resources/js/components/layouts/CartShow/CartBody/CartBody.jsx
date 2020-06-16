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
    console.info('Requested click of price button for Product ', productId)
    console.info('products', this.props.products)
    console.info('productId is an ', typeof productId)
    if (typeof productId !== 'number') {
      console.info('productId not a number branch')
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

      console.info(
        'this.props.products[productId]',
        this.props.products[productId]
      )
      console.info('filtered product', product)
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
    console.log(`changePrice(${product}, ${event}`)
    console.info('product: ', product)
    console.info('event', event)

    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value
    product.price = parseFloat(value)
    this.setState({
      product: product,
    })
  }

  handleDropClick(productId) {
    console.info('Requested drop of Product ', productId)
  }

  resetPriceAndClose() {
    this.this.togglePriceModal()
  }

  calculateTotalCost() {
    // return 69.96
    let totalPrice = 0
    const products = this.props.products
    console.info('products', products)
    for (let i = 0; i < products.length; ++i) {
      console.log(`products[${i}]`, products[i])
      totalPrice += products[i].price
    }
    console.log(totalPrice)
    return Number.parseFloat(totalPrice).toFixed(2)
  }

  render() {
    const { cartId, cartStatus, changeStatusRequest, padding } = this.props

    const totalCost = this.calculateTotalCost()
    console.info(typeof totalCost)
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
