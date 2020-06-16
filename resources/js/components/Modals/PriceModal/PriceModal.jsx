import React from 'react'
import PropTypes from 'prop-types'
import { Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import CancelButton from '../../Buttons/CancelButton'
import SaveButton from '../../Buttons/SaveButton'

const propTypes = {
  changePrice: PropTypes.func,
  isOpen: PropTypes.bool,
  originalPrice: PropTypes.number,
  product: PropTypes.object,
  toggle: PropTypes.func,
}

const defaultProps = {
  product: {
    price: 0,
  },
}

class PriceModal extends React.Component {
  constructor(props) {
    super(props)
    this.savePrice = this.savePrice.bind(this)
  }

  savePrice() {
    console.log(this.props.product)
    const url = `/products/${this.props.product.id}`
    const price = this.props.product.price
    const data = {
      price: price,
    }
    axios.patch(url, data).then((response) => {
      console.info(response)
      this.props.toggle()
    })
  }

  render() {
    return (
      <Modal isOpen={this.props.isOpen}>
        <ModalHeader>Product {this.props.product.product_id}</ModalHeader>
        <ModalBody>
          <form className="form-inline" id="form">
            <div className="form-group">
              <label className="mr-2" htmlFor="productPriceInput">
                Unit price:
              </label>{' '}
              $
              <input
                aria-describedby="originalPriceHelp"
                className="form-control ml-1"
                id="productPriceInput"
                min="0"
                pattern="[\d+]\.?[\d\d]?"
                placeholder={this.props.product.price}
                required
                step="0.01"
                type="number"
                value={this.props.product.price}
                onChange={this.props.changePrice}
              />
            </div>
            <div className="invalid-feedback">
              Please enter a positive dollar value.
            </div>
          </form>
          <small id="originalPriceHelp" className="form-text text-muted">
            Changing from {this.props.originalPrice}.
          </small>
        </ModalBody>
        <ModalFooter>
          <br />
          <SaveButton onClick={this.savePrice}>Save</SaveButton>

          <CancelButton onClick={this.props.toggle} />
        </ModalFooter>
      </Modal>
    )
  }
}

PriceModal.propTypes = propTypes
PriceModal.defaultProps = defaultProps
export default PriceModal
