import * as React from 'react'
import PropTypes from 'prop-types'
import { Button } from 'reactstrap'
import { faSave } from '@fortawesome/free-solid-svg-icons/faSave'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

const propTypes = {
  children: PropTypes.string,
  isLocked: PropTypes.bool,
}

function SaveButton(props) {
  console.info('SaveButton Props:', props)
  if (props.isLocked) {
    return null
  }

  return (
    <>
      <Button color="success" outline={true}>
        <>
          <FontAwesomeIcon className="pr-1" icon={faSave} />
          {props.children}
        </>
      </Button>
      <p>{JSON.stringify(props)}</p>
    </>
  )
}

SaveButton.propTypes = propTypes

export default SaveButton
