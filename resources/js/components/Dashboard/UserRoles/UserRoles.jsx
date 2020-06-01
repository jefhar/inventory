import * as React from 'react'
import PropTypes from 'prop-types'
import { Button, ButtonGroup, Spinner } from 'reactstrap'

class UserRoles extends React.Component {
  constructor(props) {
    super(props)
    console.log('constructing.')
    this.setSelected = this.setSelected.bind(this)
    this.state = {
      roleSelected: null,
    }
  }

  setSelected(role) {
    console.info(`Chosen ${role}.`)
    this.setState({ roleSelected: role })
  }

  render() {
    if (this.props.isLoaded) {
      const roles = JSON.parse(this.props.roles)

      const roleItems = roles.map((role) => (
        <Button
          key={role.id}
          color="primary"
          onClick={() => this.setSelected(role.id)}
          active={this.state.roleSelected === `${role.id}`}
        >
          {role.name}
        </Button>
      ))

      return (
        <>
          <p className="lead">Select a Role:</p>
          <ButtonGroup>{roleItems}</ButtonGroup>
        </>
      )
    } else {
      return (
        <div className="row py-4 px-1 bg-gray-100">
          <Spinner color="gray-300" type="grow" />
          <span className="pl-2 text-black-50">
            Select or create a user to assign a role&hellip;
          </span>
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
