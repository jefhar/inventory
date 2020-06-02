import React, { useState } from 'react'
import PropTypes from 'prop-types'
import { Button, Row } from 'reactstrap'
import NewUser from '../NewUser'

function UserChooser(props) {
  const [showNewUser, toggleNewUser] = useState(false)
  return (
    <>
      <Row className="d-flex justify-content-around">
        <Button
          outline={true}
          color="primary"
          onClick={() => toggleNewUser(!showNewUser)}
        >
          <svg
            className="bi bi-person-plus pr-2"
            width="1.5rem"
            height="1.5rem"
            viewBox="0 0 16 16"
            fill="currentColor"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fillRule="evenodd"
              d="M11 14s1 0 1-1-1-4-6-4-6 3-6 4 1 1 1 1h10zm-9.995-.944v-.002.002zM1.022 13h9.956a.274.274 0 0 0 .014-.002l.008-.002c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664a1.05 1.05 0 0 0 .022.004zm9.974.056v-.002.002zM6 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm4.5 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"
            />
            <path
              fillRule="evenodd"
              d="M13 7.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0v-2z"
            />
          </svg>{' '}
          Create new user
        </Button>{' '}
        <Button outline={true} color="warning">
          <svg
            className="bi bi-person-check pr-2"
            width="1.5rem"
            height="1.5rem"
            viewBox="0 0 16 16"
            fill="currentColor"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fillRule="evenodd"
              d="M11 14s1 0 1-1-1-4-6-4-6 3-6 4 1 1 1 1h10zm-9.995-.944v-.002.002zM1.022 13h9.956a.274.274 0 0 0 .014-.002l.008-.002c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664a1.05 1.05 0 0 0 .022.004zm9.974.056v-.002.002zM6 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6.854.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"
            />
          </svg>{' '}
          Edit existing user
        </Button>
      </Row>
      <NewUser
        show={showNewUser}
        onChange={props.onChange}
        isLocked={props.isLocked}
        name={props.name}
        email={props.email}
      />
    </>
  )
}

UserChooser.propTypes = {
  email: PropTypes.string,
  isLoaded: PropTypes.bool,
  isLocked: PropTypes.bool,
  name: PropTypes.string,
  onChange: PropTypes.func,
}

export default UserChooser
