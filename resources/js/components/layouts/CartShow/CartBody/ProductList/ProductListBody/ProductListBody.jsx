import * as React from 'react'
import ProductRow from './ProductRow'
import PropTypes from 'prop-types'

const propTypes = {
  handleDropClick: PropTypes.func.isRequired,
  handlePriceClick: PropTypes.func.isRequired,
  onClick: PropTypes.func,
  padding: PropTypes.number,
  products: PropTypes.array,
}

const ProductListBody = (props) => {
  const products = props.products

  return (
    <tbody>
      {products.map((product) => (
        <ProductRow
          handleDropClick={props.handleDropClick.bind(this, product.id)}
          handlePriceClick={props.handlePriceClick.bind(this, product.id)}
          key={product.id}
          padding={props.padding}
          product={product}
        />
      ))}
    </tbody>
  )
}

ProductListBody.propTypes = propTypes

export default ProductListBody
