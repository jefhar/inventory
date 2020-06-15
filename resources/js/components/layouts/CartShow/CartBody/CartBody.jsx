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
    console.info(this.props.products)
  }

  handleDropClick(productId) {
    console.info('Requested drop of Product ', productId)
  }

  calculateTotalCost() {
    return 69.96
  }

  render() {
    let product = {}
    const {
      cartId,
      cartStatus,
      changeStatusRequest,
      padding,
      products,
    } = this.props

    if (this.state.priceModal.isOpen) {
      for (let i = 0; i < products.length; ++i) {
        if (products[i].product_id === this.state.productId) {
          product = products[i]
          break
        }
      }
    }

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
          handlePriceClick={this.toggleModal}
          padding={padding}
          products={this.props.products}
        />
        <PriceModal
          isOpen={this.state.priceModal.isOpen}
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
