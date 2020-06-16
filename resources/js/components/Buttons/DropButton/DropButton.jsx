import * as React from 'react'
import { useState } from 'react'
import PropTypes from 'prop-types'
import { Button } from 'reactstrap'
import { faTrash } from '@fortawesome/free-solid-svg-icons/faTrash'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import ConfirmationModal from './ConfirmationModal'

const propTypes = {
  children: PropTypes.string,
  className: PropTypes.string,
  disabled: PropTypes.bool,
  onClick: PropTypes.func,
  type: PropTypes.oneOf(['cart', 'product']),
}

const DropButton = (props) => {
  const { children, disabled, onClick, type, ...other } = props
  const [isOpen, toggleModal] = useState(false)

  const getConfirmation = (something) => {
    console.info(`pressed drop button from a ${type}`)
    console.info('something', something)
    // Trigger Modal
    toggleModal((isOpen) => !isOpen)
    // Send props.onClick to modal
  }

  let iconPadding = ''
  if (children) {
    iconPadding = 'pl-1'
  }

  return (
    <>
      <Button
        color="danger"
        disabled={disabled}
        name="dropCart"
        onClick={getConfirmation}
        outline={false}
        type="button"
        {...other}
      >
        <FontAwesomeIcon icon={faTrash} />
        <span className={iconPadding}>{props.children}</span>
      </Button>
      <ConfirmationModal
        isOpen={isOpen}
        toggle={() => toggleModal((isOpen) => !isOpen)}
        onDestroy={onClick}
        type={type}
      ></ConfirmationModal>
    </>
  )
}

DropButton.propTypes = propTypes

export default DropButton
