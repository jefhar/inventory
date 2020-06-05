import React from 'react'
import PropTypes from 'prop-types'
import ControlPanel from './ControlPanel/ControlPanel'
import ProductList from './ProductList'
import { CardBody } from 'reactstrap'

const propTypes = {
  cartId: PropTypes.number,
  cartStatus: PropTypes.string,
  changeStatusRequest: PropTypes.func,
}
const defaultProps = {
  cartStatus: 'void',
}

class CartBody extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      disabled: this.props.cartStatus !== 'open',
      products: [],
      totalCost: 0,
      isLoading: true,
    }
  }

  componentDidMount() {
    // Make AJAX call to get products belonging to this cart, put in products.
    function sleep(ms) {
      return new Promise((resolve) => setTimeout(resolve, ms))
    }

    sleep(1000).then(() => {
      this.setState({
        products: [
          {
            product_id: 18,
            manufacturer_id: 1,
            type_id: 1,
            work_order_id: 18,
            cart_id: 59,
            price: 99.99,
            model: 'Computer Specialist',
            serial: '3178415173',
            status: 'In Cart',
            values: { serial: '3178415173' },
            created_at: '2020-06-04T00:22:03.000000Z',
            updated_at: '2020-06-04T17:48:56.000000Z',
            manufacturer: {
              id: 1,
              name: 'Gusikowski, Crist and Homenick',
              created_at: '2020-06-04T00:22:03.000000Z',
              updated_at: '2020-06-04T00:22:03.000000Z',
            },
          },
        ],
        isLoading: false,
      })
    })
  }

  render() {
    return (
      <CardBody>
        <ControlPanel
          cartId={this.props.cartId}
          cartStatus={this.props.cartStatus}
          disabled={this.state.disabled}
          totalCost={this.state.totalCost}
          changeStatusRequest={this.props.changeStatusRequest}
        />
        <ProductList
          cartId={this.props.cartId}
          cartStatus={this.props.cartStatus}
          disabled={this.state.disabled}
          products={this.state.products}
          isLoading={this.state.isLoading}
        />
      </CardBody>
    )
  }
}

CartBody.propTypes = propTypes
CartBody.defaultProps = defaultProps
export default CartBody
