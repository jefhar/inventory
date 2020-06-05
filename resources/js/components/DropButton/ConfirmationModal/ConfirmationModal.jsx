import * as React from 'react'
import PropTypes from 'prop-types'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import capitalize from 'lodash/capitalize'

const propTypes = {
  className: PropTypes.string,
  isOpen: PropTypes.bool,
  toggle: PropTypes.func,
  type: PropTypes.string,
  onDestroy: PropTypes.func,
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
      <Modal isOpen={this.props.isOpen} className={this.props.className}>
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

ConfirmationModal.propTypes = propTypes
ConfirmationModal.defaultProps = defaultProps

export default ConfirmationModal
