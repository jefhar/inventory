import * as React from 'react'
import PropTypes from 'prop-types'
import { Col, CustomInput, Row, Spinner } from 'reactstrap'

const propTypes = {
  isLoading: PropTypes.bool,
  isLocked: PropTypes.bool,
  onChange: PropTypes.func,
  permissions: PropTypes.string,
  permissionsSelected: PropTypes.array,
}

const defaultProps = {
  permissionsSelected: [],
}

function UserPermissions(props) {
  const permissions = JSON.parse(props.permissions)
  const permissionItems = permissions.map((permission) => (
    <Col key={permission.id} md={4} sm={6} xl={3}>
      <CustomInput
        disabled={props.isLocked}
        id={permission.id}
        label={permission.name}
        name="customSwitch"
        onChange={props.onChange}
        type="switch"
        value={permission.id}
        checked={props.permissionsSelected.includes(`${permission.id}`)}
      />
    </Col>
  ))

  return (
    <div className="border-top border-dark mt-4">
      <p className="lead">Select Permissions:</p>
      <Row className="flex-wrap align-content-end">{permissionItems}</Row>

      {props.isLoading && <Spinner color="gray-300" type="grow" />}
      <p>{JSON.stringify(props.permissionsSelected)}</p>
    </div>
  )
}

UserPermissions.propTypes = propTypes
UserPermissions.defaultProps = defaultProps

export default UserPermissions
