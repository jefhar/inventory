import React from 'react'
import PropTypes from 'prop-types'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import InfoCircleIcon from '../../Icons/InfoCircleIcon'

const propTypes = {
  isOpen: PropTypes.bool,
  header: PropTypes.element,
  toggle: PropTypes.func,
  body: PropTypes.element,
}

const AlertModal = (props) => {
  const { header, isOpen, toggle, body, ...other } = props

  return (
    <Modal isOpen={isOpen} toggle={toggle} {...other}>
      <ModalHeader toggle={toggle}>
        <>
          <InfoCircleIcon /> {header}
        </>
      </ModalHeader>
      <ModalBody>{body}</ModalBody>
      <ModalFooter>
        <Button color="primary" outline={true} onClick={toggle}>
          <>
            <InfoCircleIcon /> Confirm
          </>
        </Button>
      </ModalFooter>
    </Modal>
  )
}

AlertModal.propTypes = propTypes
export default AlertModal
