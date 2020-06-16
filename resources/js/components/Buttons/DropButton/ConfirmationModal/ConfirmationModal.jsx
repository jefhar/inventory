import * as React from 'react'
import capitalize from 'lodash/capitalize'
import PropTypes from 'prop-types'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'

const propTypes = {
  className: PropTypes.string,
  isOpen: PropTypes.bool,
  onDestroy: PropTypes.func,
  toggle: PropTypes.func,
  type: PropTypes.oneOf(['cart', 'product']),
}
const defaultProps = {
  isOpen: false,
}

class ConfirmationModal extends React.Component {
  constructor(props) {
    super(props)
    this.state = {}
  }

  render() {
    const title = `Destroy ${capitalize(this.props.type)}?`

    return (
      <Modal className={this.props.className} isOpen={this.props.isOpen}>
        <ModalHeader toggle={this.props.toggle}>
          <span className="text-danger">
            <i className="fas fa-exclamation-triangle mr-1"></i>
          </span>
          {title}
        </ModalHeader>
        <ModalBody>
          <div className="left-danger-border">
            <p className={this.props.type === 'cart' ? 'd-block' : 'd-none'}>
              This will permanently destroy the cart and return all items to
              available inventory. Are you sure sure you want to do this?
            </p>
            <p className={this.props.type === 'product' ? 'd-block' : 'd-none'}>
              This will remove the product from the cart. Are you sure sure you
              want to do this?
            </p>
            <p>
              <em>This cannot be undone.</em>
            </p>
          </div>
        </ModalBody>
        <ModalFooter>
          <Button color="secondary" outline={true} onClick={this.props.toggle}>
            Cancel
          </Button>{' '}
          <Button color="danger" onClick={this.props.onDestroy}>
            Confirm
          </Button>
        </ModalFooter>
      </Modal>
    )
  }
}

ConfirmationModal.defaultProps = defaultProps
ConfirmationModal.propTypes = propTypes

export default ConfirmationModal
