import * as React from 'react'
import DropButton from '../../../../../DropButton'
import PriceButton from './PriceButton'
import PropTypes from 'prop-types'

const propTypes = {
  disabled: PropTypes.bool,
  handleDropClick: PropTypes.func.isRequired,
  handlePriceClick: PropTypes.func.isRequired,
  padding: PropTypes.number,
  product: PropTypes.object,
}
const ProductRow = (props) => {
  console.info(props)
  return (
    <tr>
      <th scope="row">
        <a className="btn btn-info" href={`/inventory/${props.product.id}`}>
          {_.padStart(props.product.id.toString(), props.padding, '0')}
        </a>
      </th>

      <td>{props.product.manufacturer.name}</td>
      <td>{props.product.model}</td>
      <td>{props.product.type.name}</td>
      <td>{props.product.serial}</td>
      <td>
        <PriceButton
          disabled={props.disabled}
          onClick={props.handlePriceClick}
          price={props.product.price}
          productId={props.product.id}
        ></PriceButton>
      </td>
      <td>
        <DropButton
          disabled={props.disabled}
          onClick={props.handleDropClick}
        ></DropButton>
      </td>
    </tr>
  )
}

ProductRow.propTypes = propTypes

export default ProductRow
