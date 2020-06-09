import * as React from 'react'
import ReactDOM from 'react-dom'
import UserRoles from './UserRoles'
import UserPermissions from './UserPermissions'
import UserChooser from './UserChooser'
import { Card, CardBody, CardHeader, Container } from 'reactstrap'
import SaveButton from '../SaveButton'

class Dashboard extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      allPermissions: '[{"id":"NONE","name":"NONE"}]',
      allRoles: '[{"id":"NONE","name":"NONE"}]',
      email: '',
      isLocked: true,
      isPermissionsLoaded: false,
      isRolesLoaded: false,
      isUserLoaded: false,
      name: '',
      permissionsSelected: [],
      roleSelected: '',
    }
    this.onChange = this.onChange.bind(this)
    this.setChecked = this.setChecked.bind(this)
    this.setSelected = this.setSelected.bind(this)
  }

  async componentDidMount() {
    // Call AJAX, return a set of all Roles. Put it in this.state.allRoles
    const roleUrl = '/dashboard/roles'
    const permissionsUrl = '/dashboard/permissions'

    axios.get(roleUrl).then((result) => {
      console.info(result.data)
      this.setState({
        allRoles: JSON.stringify(result.data),
        isRolesLoaded: true,
        isRolesLocked: false,
      })
    })
    axios.get(permissionsUrl).then((result) => {
      console.info(result.data)
      this.setState({
        allPermissions: JSON.stringify(result.data),
        isPermissionsLoaded: true,
        isPermissionsLocked: false,
      })
    })

    const sleep = (ms) => {
      return new Promise((resolve) => setTimeout(resolve, ms))
    }
    await sleep(2000)
    console.info('done sleeping:')

    // Call AJAX, return a set of allPermissions. Put it in
    // this.state.allPermissions

    // Call AJAX, get list of all users. Put it in this.state.allUsers
    let allUsers = [
      {
        name: 'Owner',
        email: 'owner@example.com',
      },
      {
        name: 'technician',
        email: 'technician@example.com',
      },
    ]
    allUsers = JSON.stringify(allUsers)
    this.setState({
      allUsers: allUsers,
      isUserLoaded: true,
    })
  }

  setChecked(event) {
    const permission = event.target.value
    const permissionsSelected = this.state.permissionsSelected
    const index = permissionsSelected.indexOf(permission)
    if (index < 0) {
      permissionsSelected.push(permission)
    } else {
      permissionsSelected.splice(index, 1)
    }
    this.setState({ permissionsSelected: permissionsSelected })
  }

  setSelected(event) {
    this.setState({ roleSelected: event.target.value })
    const roleUrl = `/dashboard/roles/${event.target.value}`
    axios.get(roleUrl).then((result) => {
      const permissionsSelected = []
      result.data.forEach((permission) => {
        console.info('permission', permission[0])
        permissionsSelected.push(permission[0])
      })
      this.setState({ permissionsSelected: permissionsSelected })
    })
  }

  onChange(event) {
    const target = event.target
    const name = target.name
    const value = target.type === 'checkbox' ? target.checked : target.value
    console.info('inside onChange. `name`: ', name, ' `value`: ', value)

    // if name === 'email', check that value matches regex for email. (from
    // https://emailregex.com)
    if (name === 'email') {
      const simpleEmailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      const validEmail = simpleEmailRegex.test(value)
      console.info(name, value, validEmail)
      this.setState({
        isValidEmail: validEmail,
      })
    }

    this.setState({
      [name]: value,
    })
    this.setState((state) => {
      return {
        isLocked: !(state.isValidEmail && state.name.length >= 3),
      }
    })
  }

  render() {
    return (
      <Container>
        <Card>
          <CardHeader>
            <h1>
              <svg
                className="bi bi-people-fill pr-2 blue"
                width="1.5em"
                height="1.5em"
                viewBox="0 0 16 16"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fillRule="evenodd"
                  d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"
                />
              </svg>
              User Dashboard
            </h1>
          </CardHeader>
          <CardBody>
            <UserChooser
              className="py-1"
              email={this.state.email}
              isLoaded={this.state.isUserLoaded}
              isValidEmail={this.state.isValidEmail}
              name={this.state.name}
              onChange={this.onChange}
              users={this.state.allUsers}
              value={this.state.value}
            />
            <UserRoles
              className="py-1"
              isLoading={!this.state.isRolesLoaded}
              isLocked={this.state.isLocked}
              onClick={this.setSelected}
              roles={this.state.allRoles}
              roleSelected={this.state.roleSelected}
            />
            <UserPermissions
              className="py-1"
              isLoading={!this.state.isPermissionsLoaded}
              isLocked={this.state.isLocked}
              onChange={this.setChecked}
              permissions={this.state.allPermissions}
              permissionsSelected={this.state.permissionsSelected}
            />
            <SaveButton isLocked={this.state.isLocked}>Save User</SaveButton>
          </CardBody>
        </Card>
      </Container>
    )
  }
}

export default Dashboard

if (document.getElementById('dashboard')) {
  console.info('got dashboard')
  ReactDOM.render(<Dashboard />, document.getElementById('dashboard'))
}
