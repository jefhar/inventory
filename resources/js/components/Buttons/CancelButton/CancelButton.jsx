import React from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faTimesCircle } from '@fortawesome/free-solid-svg-icons/faTimesCircle'
import { Button } from 'reactstrap'

const CancelButton = (props) => {
  return (
    <Button color="secondary" outline={true} onClick={props.onClick}>
      <>
        <FontAwesomeIcon icon={faTimesCircle} />
        <span className="pl-1">Cancel</span>
      </>
    </Button>
  )
}

export default CancelButton
