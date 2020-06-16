import * as React from 'react'
import DropButton from '../../../../../../Buttons/DropButton'
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

      <td>{props.product.manufacturer_name}</td>
      <td>{props.product.model}</td>
      <td>{props.product.type_name}</td>
      <td>{props.product.serial}</td>
      <td>
        <PriceButton
          disabled={props.disabled}
          onClick={props.handlePriceClick}
          price={props.product.price}
          productId={props.product.id}
        />
      </td>
      <td>
        <DropButton
          disabled={props.disabled}
          onClick={props.handleDropClick.bind(props.product)}
          type="product"
        ></DropButton>
      </td>
    </tr>
  )
}

ProductRow.propTypes = propTypes

export default ProductRow
