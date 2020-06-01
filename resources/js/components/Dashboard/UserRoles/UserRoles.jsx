import * as React from 'react'
import PropTypes from 'prop-types'
import { FormGroup, Input, Label, Spinner } from 'reactstrap'

class UserRoles extends React.Component {
  constructor(props) {
    super(props)
    console.log('constructing.')
  }

  render() {
    if (this.props.isLoaded) {
      console.info('this.props.roles', this.props.roles)
      const roles = JSON.parse(this.props.roles)
      console.info('roles', roles)

      const roleItems = roles.map((role) => (
        <FormGroup check inline key={role.id}>
          <Input type="radio" name="role" value={role.name} />
          <Label check className="text-gray-100">
            {role.name}
          </Label>
        </FormGroup>
      ))

      return <div className="row py-4 px-2 bg-info">{roleItems}</div>
    } else {
      return (
        <div className="row py-4 px-1 bg-gray-100">
          <Spinner color="gray-300" type="grow" />
          <span className="pl-2 text-black-50">Loading User Roles&hellip;</span>
        </div>
      )
    }
  }
}

UserRoles.propTypes = {
  isLoaded: PropTypes.bool,
  roles: PropTypes.string,
}

export default UserRoles
