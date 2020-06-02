import * as React from 'react'
import PropTypes from 'prop-types'
import { Button, ButtonGroup, Spinner } from 'reactstrap'

class UserRoles extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      roleSelected: '{}',
    }
    this.setSelected = this.setSelected.bind(this)
  }

  setSelected(role) {
    this.setState({ roleSelected: role })
  }

  render() {
    console.info(this.props.roles)
    const roles = JSON.parse(this.props.roles)

    const roleItems = roles.map((role) => (
      <Button
        active={this.props.roleSelected === `${role.id}`}
        color="primary"
        disabled={this.props.isLocked}
        key={role.id}
        onClick={this.props.onClick}
        value={role.id}
      >
        {role.name}
      </Button>
    ))

    return (
      <div className="border-top border-dark mt-4">
        <p className="lead">Select a Role:</p>
        <ButtonGroup>{roleItems}</ButtonGroup>
        {this.props.isLoading && <Spinner color="gray-300" type="grow" />}
        <p>{this.props.roleSelected}</p>
        <p>{JSON.stringify(this.props.isLocked)}</p>
      </div>
    )
  }
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
