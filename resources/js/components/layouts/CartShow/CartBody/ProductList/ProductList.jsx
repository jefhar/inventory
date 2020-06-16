import React from 'react'
import ProductListBody from './ProductListBody'
import ProductListHeader from './ProductListHeader'
import PropTypes from 'prop-types'
import { Table } from 'reactstrap'

const propTypes = {
  cartId: PropTypes.number,
  cartStatus: PropTypes.string,
  disabled: PropTypes.bool,
  handleDropClick: PropTypes.func.isRequired,
  handlePriceClick: PropTypes.func.isRequired,
  padding: PropTypes.number,
  products: PropTypes.array,
}
const defaultProps = {
  cartId: 0,
  disabled: true,
  products: [],
}

class ProductList extends React.Component {
  constructor(props) {
    super(props)
    this.state = {}
  }

  render() {
    return (
      <Table dark={true} hover={true} responsive={true}>
        <ProductListHeader />
        <ProductListBody
          handleDropClick={this.props.handleDropClick}
          handlePriceClick={this.props.handlePriceClick}
          padding={this.props.padding}
          products={this.props.products}
          disabled={this.props.disabled}
        />
      </Table>
    )
  }
}

ProductList.defaultProps = defaultProps
ProductList.propTypes = propTypes

export default ProductList
