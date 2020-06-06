import React from 'react'
import PropTypes from 'prop-types'
import { Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import CancelButton from '../../Buttons/CancelButton'

const propTypes = {
  isOpen: PropTypes.bool,
  product: PropTypes.object,
  toggle: PropTypes.func,
}

class PriceModal extends React.Component {
  constructor(props) {
    super(props)
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
              />
            </div>
            <div className="invalid-feedback">
              Please enter a positive dollar value.
            </div>
          </form>
          <small id="originalPriceHelp" className="form-text text-muted">
            Changing from {this.props.product.price}.
          </small>
        </ModalBody>
        <ModalFooter>
          <br />
          <button
            id="costSubmitButton"
            type="submit"
            className="btn btn-outline-primary"
            value="Save"
          >
            <i className="far fa-save mr-1"></i>Save
          </button>
          <CancelButton onClick={this.props.toggle} />
        </ModalFooter>
      </Modal>
    )
  }
}

PriceModal.propTypes = propTypes
export default PriceModal
