import * as React from 'react'
import PropTypes from 'prop-types'
import { Button, ButtonGroup, Spinner } from 'reactstrap'

const propTypes = {
  isLoading: PropTypes.bool,
  isLocked: PropTypes.bool,
  onClick: PropTypes.func,
  roles: PropTypes.string,
  roleSelected: PropTypes.string,
}

function UserRoles(props) {
  const roles = JSON.parse(props.roles)
  const roleButtons = roles.map((role) => (
    <Button
      active={props.roleSelected === `${role.id}`}
      color="primary"
      disabled={props.isLocked}
      key={role.id}
      onClick={props.onClick}
      value={role.id}
    >
      {role.name}
    </Button>
  ))

  let validatorClass = ''
  let validatorMessage = ''
  if (!props.roleSelected) {
    validatorClass = 'is-invalid red small'
    validatorMessage = 'You must select a user role.'
  }

  return (
    <div className="border-top border-dark mt-4">
      <p className="lead">Select a Role:</p>
      <ButtonGroup>{roleButtons}</ButtonGroup>
      {props.isLoading && <Spinner color="gray-300" type="grow" />}

      <div className={validatorClass}>{validatorMessage}</div>
    </div>
  )
}

UserRoles.propTypes = propTypes

UserRoles.defaultProps = {}

export default UserRoles
