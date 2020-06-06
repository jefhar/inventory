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
      priceModal: {
        isOpen: false,
        productId: 0,
      },
      totalCost: 0,
    }
    this.toggleModal = this.toggleModal.bind(this)
  }

  // Manipulate this.state.currentProducts Here

  toggleModal(productId) {
    console.info('Requested click of price button for Product ', productId)
    this.setState((state) => {
      return {
        priceModal: {
          isOpen: !state.priceModal.isOpen,
          productId: productId,
        },
      }
    })
  }

  handleDropClick(productId) {
    console.info('Requested drop of Product ', productId)
  }

  calculateTotalCost() {
    return 69.96
  }

  render() {
    let product = {}
    const { isOpen, currentProducts } = this.state.priceModal
    if (isOpen) {
      for (let i = 0; i < currentProducts.length; ++i) {
        if (currentProducts[i].product_id === this.state.productId) {
          product = currentProducts[i]
          break
        }
      }
    }

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
          handlePriceClick={this.toggleModal}
          padding={this.props.padding}
          products={this.state.currentProducts}
        />
        <PriceModal
          isOpen={isOpen}
          product={product}
          toggle={this.toggleModal}
        />
      </CardBody>
    )
  }
}

CartBody.defaultProps = defaultProps
CartBody.propTypes = propTypes

export default CartBody
