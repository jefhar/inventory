import * as React from 'react'
import ReactDOM from 'react-dom'
import SaveButton from '../../Buttons/SaveButton'
import UserChooser from './UserChooser'
import UserPermissions from './UserPermissions'
import UserRoles from './UserRoles'

import { Card, CardBody, CardHeader, Container } from 'reactstrap'
import AlertModal from '../../Modals/AlertModal'

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
      isSavedLocked: false,
      isUserLoaded: false,
      name: '',
      permissionsSelected: [],
      roleSelected: '',
      showUserToast: false,
    }

    this.chooseUser = this.chooseUser.bind(this)
    this.onInputChange = this.onInputChange.bind(this)
    this.saveUser = this.saveUser.bind(this)
    this.setChecked = this.setChecked.bind(this)
    this.setSelected = this.setSelected.bind(this)
    this.toggleAlertModal = this.toggleAlertModal.bind(this)
    this.toggleEditUserModal = this.toggleEditUserModal.bind(this)
    this.toggleEditUserModal = this.toggleEditUserModal.bind(this)
    this.toggleNewUser = this.toggleNewUser.bind(this)
  }

  componentDidMount() {
    console.info('componentDidMount()')
    this.refreshData()
  }

  async refreshData() {
    console.info('refreshData()')
    // Call AJAX, return a set of all Roles. Put it in this.state.allRoles
    const roleUrl = '/dashboard/roles'
    const permissionsUrl = '/dashboard/permissions'
    const usersUrl = '/dashboard/users'

    const [roleResponse, permissionResponse, userResponse] = await Promise.all([
      axios.get(roleUrl),
      axios.get(permissionsUrl),
      axios.get(usersUrl),
    ])
    this.setState({
      allPermissions: JSON.stringify(permissionResponse.data),
      allRoles: JSON.stringify(roleResponse.data),
      allUsers: userResponse.data,
      isPermissionsLoaded: true,
      isPermissionsLocked: false,
      isRolesLoaded: true,
      isRolesLocked: false,
      isUserLoaded: true,
    })
  }

  setChecked(event) {
    console.info('setChecked()')
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
    console.info('setSelected()')
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

  onInputChange(event) {
    console.info('onInputChange()')
    const target = event.target
    const name = target.name
    const value = target.type === 'checkbox' ? target.checked : target.value
    console.info('inside onChange. `name`: ', name, ' `value`: ', value)

    // Regex for email from https://emailregex.com
    if (name === 'email') {
      // eslint-disable-next-line no-useless-escape
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

  chooseUser() {
    console.info('hello from chooseUser() ')
    const key = document.getElementById('usersList').options.selectedIndex
    const user = this.state.allUsers[key]
    console.info(user)
    this.setState({
      email: user.email,
      isLocked: false,
      isPermissionsLoaded: true,
      isRolesLoaded: true,
      isUserLoaded: true,
      isValidEmail: true,
      name: user.name,
      permissionsSelected: user.permissions,
      roleSelected: user.role,
      showEditUser: false,
      showNewUser: true,
    })
  }

  saveUser() {
    console.info('saveUser()')
    const user = {
      email: this.state.email,
      name: this.state.name,
    }
    const role = this.state.roleSelected
    const permissions = this.state.permissionsSelected
    const data = {
      permissions: permissions,
      role: role,
      user: user,
    }
    console.info('data:', data)
    axios.post('/dashboard/users', data).then((response) => {
      console.info(response)
      this.setState({
        alertModalBody: (
          <>
            The selected user has been saved. An email to {user.email} has been
            queued with instructions for the user.
          </>
        ),
        alertModalHeader: <>User Saved</>,
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
        showAlertModal: true,
        userToast: response.data,
      })
      this.refreshData()
    })
  }

  toggleNewUser() {
    console.info('toggleNewUser()')
    this.setState((state) => {
      return {
        showNewUser: !state.showNewUser,
      }
    })
  }

  toggleEditUserModal() {
    console.info('toggleEditUserModal')
    this.setState((state) => {
      return {
        showEditUser: !state.showEditUser,
      }
    })
  }

  toggleAlertModal() {
    console.info('toggleAlertModal()')
    this.setState((state) => {
      return {
        showAlertModal: !state.showAlertModal,
      }
    })
  }

  render() {
    console.info('render()')
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
              </svg>{' '}
              User Dashboard
            </h1>
          </CardHeader>
          <CardBody>
            <UserChooser
              disabled={!this.state.isUserLoaded}
              className="py-1"
              email={this.state.email}
              isLoaded={this.state.isUserLoaded}
              isValidEmail={this.state.isValidEmail}
              name={this.state.name}
              onChange={this.onInputChange}
              users={this.state.allUsers}
              value={this.state.value}
              chooseUser={this.chooseUser}
              toggleUserModal={this.toggleEditUserModal}
              showEditUser={this.state.showEditUser}
              toggleNewUser={this.toggleNewUser}
              showNewUser={this.state.showNewUser}
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
            <SaveButton
              isLocked={this.state.isSavedLocked}
              onClick={this.saveUser}
            >
              Save User
            </SaveButton>
          </CardBody>
        </Card>
        <AlertModal
          isOpen={this.state.showAlertModal}
          toggle={this.toggleAlertModal}
          header={this.state.alertModalHeader}
          body={this.state.alertModalBody}
        />
      </Container>
    )
  }
}

export default Dashboard

if (document.getElementById('dashboard')) {
  console.info('got dashboard')
  ReactDOM.render(<Dashboard />, document.getElementById('dashboard'))
}
