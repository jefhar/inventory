import React from 'react'
import PropTypes from 'prop-types'
import { Button, Row } from 'reactstrap'
import NewUser from './NewUser'
import EditUserModal from './EditUserModal'
import EditUserIcon from '../../../Icons/EditUserIcon'

const propTypes = {
  chooseUser: PropTypes.func,
  disabled: PropTypes.bool,
  email: PropTypes.string,
  isLoaded: PropTypes.bool,
  isLocked: PropTypes.bool,
  isValidEmail: PropTypes.bool,
  name: PropTypes.string,
  onChange: PropTypes.func,
  showEditUser: PropTypes.bool,
  toggleUserModal: PropTypes.func,
  toggleNewUser: PropTypes.func,
  users: PropTypes.array,
  showNewUser: PropTypes.bool,
}

function UserChooser(props) {
  return (
    <>
      <Row className="d-flex justify-content-around">
        <Button
          outline={true}
          color="primary"
          disabled={props.showEditUser || props.disabled}
          onClick={props.toggleNewUser}
        >
          <>
            <svg
              className="bi bi-person-plus pr-1"
              fill="currentColor"
              height="1em"
              viewBox="0 0 16 16"
              width="1em"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M11 14s1 0 1-1-1-4-6-4-6 3-6 4 1 1 1 1h10zm-9.995-.944v-.002.002zM1.022 13h9.956a.274.274 0 0 0 .014-.002l.008-.002c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664a1.05 1.05 0 0 0 .022.004zm9.974.056v-.002.002zM6 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm4.5 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"
                fillRule="evenodd"
              />
              <path
                d="M13 7.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0v-2z"
                fillRule="evenodd"
              />
            </svg>{' '}
            Create new user
          </>
        </Button>{' '}
        <Button
          color="warning"
          outline={true}
          disabled={props.showNewUser || props.disabled}
          onClick={props.toggleUserModal}
        >
          <>
            <EditUserIcon /> Edit existing user
          </>
        </Button>
      </Row>
      <NewUser
        email={props.email}
        isValidEmail={props.isValidEmail}
        name={props.name}
        onChange={props.onChange}
        show={props.showNewUser}
      />
      <EditUserModal
        users={props.users}
        show={props.showEditUser}
        onClick={props.chooseUser}
        toggle={props.toggleUserModal}
      />
    </>
  )
}

UserChooser.propTypes = propTypes

export default UserChooser
