import * as React from 'react'
import PropTypes from 'prop-types'
import { Button } from 'reactstrap'
import { faSave } from '@fortawesome/free-solid-svg-icons/faSave'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

const propTypes = {
  children: PropTypes.string,
  isLocked: PropTypes.bool,
  onClick: PropTypes.func,
}

function SaveButton(props) {
  // console.info('SaveButton Props:', props)
  const { isLocked, onClick, children, ...other } = props
  if (isLocked) {
    return null
  }

  return (
    <>
      <Button color="success" onClick={onClick} outline={true} {...other}>
        <>
          <FontAwesomeIcon className="pr-1" icon={faSave} />
          {children}
        </>
      </Button>
    </>
  )
}

SaveButton.propTypes = propTypes

export default SaveButton
