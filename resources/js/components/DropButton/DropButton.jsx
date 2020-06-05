import * as React from 'react'
import PropTypes from 'prop-types'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

import { Button } from 'reactstrap'
import { faTrash } from '@fortawesome/free-solid-svg-icons/faTrash'

const propTypes = {
  children: PropTypes.string,
  className: PropTypes.string,
  disabled: PropTypes.bool,
  onClick: PropTypes.func,
}

const DropButton = (props) => {
  console.info(props.children)
  const { children, disabled, onClick, ...other } = props

  let iconPadding = ''
  if (children) {
    iconPadding = 'pl-1'
  }

  return (
    <Button
      color="danger"
      disabled={disabled}
      onClick={onClick}
      outline={false}
      type="button"
      name="dropCart"
      {...other}
    >
      <FontAwesomeIcon icon={faTrash} />
      <span className={iconPadding}>{props.children}</span>
    </Button>
  )
}

DropButton.propTypes = propTypes
export default DropButton
