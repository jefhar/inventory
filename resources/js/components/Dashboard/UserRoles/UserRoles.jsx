import * as React from 'react'
import PropTypes from 'prop-types'
import { Button, ButtonGroup, Spinner } from 'reactstrap'

function UserRoles(props) {
  const roles = JSON.parse(props.roles)
  const roleItems = roles.map((role) => (
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

  return (
    <div className="border-top border-dark mt-4">
      <p className="lead">Select a Role:</p>
      <ButtonGroup>{roleItems}</ButtonGroup>
      {props.isLoading && <Spinner color="gray-300" type="grow" />}
      <p>{props.roleSelected}</p>
      <p>{JSON.stringify(props.isLocked)}</p>
    </div>
  )
}

UserRoles.propTypes = {
  isLoading: PropTypes.bool,
  isLocked: PropTypes.bool,
  onClick: PropTypes.func,
  roles: PropTypes.string,
  roleSelected: PropTypes.string,
}

UserRoles.defaultProps = {}

export default UserRoles
