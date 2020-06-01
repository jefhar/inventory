import * as React from 'react'
import PropTypes from 'prop-types'
import { Button, ButtonGroup, Spinner } from 'reactstrap'

class UserPermissions extends React.Component {
  constructor(props) {
    super(props)
    console.log('constructing.')
    this.state = {
      permissionsSelected: [],
    }
  }

  setSelected(permission) {
    console.info(`Chosen ${permission}.`)
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
    if (this.props.isLoaded) {
      const permissions = JSON.parse(this.props.permissions)
      const permissionItems = permissions.map((permission) => (
        <Button
          key={permission.id}
          color="secondary"
          onClick={() => this.setSelected(permission.id)}
          active={this.state.permissionsSelected.includes(`${permission.id}`)}
        >
          {permission.name}
        </Button>
      ))
      return (
        <>
          <p className="lead">Select Permissions:</p>
          <ButtonGroup className="flex-wrap align-content-end">
            {permissionItems}
          </ButtonGroup>
        </>
      )
    } else {
      return (
        <div className="row py-4 px-1 bg-gray-100">
          <Spinner color="gray-300" type="grow" />
          <span className="pl-2 text-black-50">
            Loading User Permissions&hellip;
          </span>
        </div>
      )
    }
  }
}

UserPermissions.propTypes = {
  isLoaded: PropTypes.bool,
  permissions: PropTypes.string,
}
export default UserPermissions
