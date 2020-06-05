import React from 'react'
import PropTypes from 'prop-types'
import { Spinner } from 'reactstrap'

const propTypes = {
  cartId: PropTypes.number,
  cartStatus: PropTypes.string,
  disabled: PropTypes.bool,
  products: PropTypes.array,
  isLoading: PropTypes.bool,
}
const defaultProps = {
  cartId: 0,
  disabled: true,
  products: [],
  isLoading: true,
}

class ProductList extends React.Component {
  constructor(props) {
    super(props)
    this.state = {}
    this.deleteRow = this.deleteRow.bind(this)
  }

  deleteRow() {
    console.info('Hello from deleteRow')
  }

  render() {
    if (this.props.isLoading) {
      return <Spinner color="gray-300 type=grow" />
    }
    const productId = 1
    return (
      <>
        <p>Product List</p>
        <button onClick={this.deleteRow.bind(this, productId)}>
          Delete Row
        </button>
      </>
    )
  }
}

ProductList.propTypes = propTypes
ProductList.defaultProps = defaultProps

export default ProductList
