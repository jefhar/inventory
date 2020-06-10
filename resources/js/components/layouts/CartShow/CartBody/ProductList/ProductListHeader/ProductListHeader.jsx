import * as React from 'react'

const ProductListHeader = () => {
  return (
    <thead className="thead-light">
      <tr>
        <th scope="col">Product ID</th>
        <th scope="col">Make</th>
        <th scope="col">Model</th>
        <th scope="col">Type</th>
        <th scope="col">Serial</th>
        <th scope="col">Price</th>
        <th scope="col">Remove</th>
      </tr>
    </thead>
  )
}

export default ProductListHeader
