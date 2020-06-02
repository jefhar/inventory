import * as React from 'react'
import PropTypes from 'prop-types'
import { Col, CustomInput, Row, Spinner } from 'reactstrap'

class UserPermissions extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      permissionsSelected: [],
    }
  }

  setSelected(permission) {
    const permissionsSelected = this.state.permissionsSelected
    const index = permissionsSelected.indexOf(permission)
    if (index < 0) {
      permissionsSelected.push(permission)
    } else {
      permissionsSelected.splice(index, 1)
    }
    this.setState({ permissionsSelected: permissionsSelected })
  }

  render() {
    const permissions = JSON.parse(this.props.permissions)
    const permissionItems = permissions.map((permission) => (
      <Col key={permission.id} md={4} sm={6} xl={3}>
        <CustomInput
          color="secondary"
          disabled={this.props.isLocked}
          id={permission.id}
          label={permission.name}
          name="customSwitch"
          onChange={() => this.setSelected(permission.id)}
          type="switch"
        />
      </Col>
    ))

    return (
      <div className="border-top border-dark mt-4">
        <p className="lead">Select Permissions:</p>
        <Row className="flex-wrap align-content-end">{permissionItems}</Row>

        {this.props.isLoading && <Spinner color="gray-300" type="grow" />}
      </div>
    )
  }
}

UserPermissions.propTypes = {
  isLocked: PropTypes.bool,
  isLoading: PropTypes.bool,
  permissions: PropTypes.string,
}
export default UserPermissions
