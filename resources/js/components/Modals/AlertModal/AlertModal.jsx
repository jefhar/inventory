import React from 'react'
import PropTypes from 'prop-types'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import InfoCircleIcon from '../../Icons/InfoCircleIcon'
import WarningTriangleIcon from '../../Icons/WarningTriangleIcon'

const propTypes = {
  body: PropTypes.element,
  header: PropTypes.element,
  isOpen: PropTypes.bool,
  toggle: PropTypes.func,
  type: PropTypes.string,
}

const defaultProps = {
  type: 'success',
}

const AlertModal = (props) => {
  const { body, header, isOpen, toggle, type, ...other } = props

  let icon
  if (type === 'success') {
    icon = <InfoCircleIcon />
  } else {
    icon = <WarningTriangleIcon />
  }

  return (
    <Modal isOpen={isOpen} toggle={toggle} {...other}>
      <ModalHeader toggle={toggle}>
        <>
          {icon} {header}
        </>
      </ModalHeader>
      <ModalBody>{body}</ModalBody>
      <ModalFooter>
        <Button color={type} outline={true} onClick={toggle}>
          <>{icon} Confirm</>
        </Button>
      </ModalFooter>
    </Modal>
  )
}

AlertModal.propTypes = propTypes
AlertModal.defaultProps = defaultProps
export default AlertModal
