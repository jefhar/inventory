import * as React from 'react'
import ReactDOM from 'react-dom'
import UserRoles from './UserRoles'
import WorkOrder from '../WorkOrder'
import UserPermissions from './UserPermissions'
import UserChooser from './UserChooser'
import { Card, CardBody, CardHeader, Container } from 'reactstrap'

class Dashboard extends React.Component {
  constructor(props) {
    super(props)
    console.log('constructing.')
    this.state = {
      allPermissions: '[{"id":"NONE","name":"NONE"}]',
      allRoles: '[{"id":"NONE","name":"NONE"}]',
      email: '',
      isLocked: true,
      isPermissionsLoaded: false,
      isRolesLoaded: false,
      isUserLoaded: false,
      name: '',
      roleSelected: '',
    }
    this.onChange = this.onChange.bind(this)
    this.setSelected = this.setSelected.bind(this)
  }

  async componentDidMount() {
    // Call AJAX, return a set of all Roles. Put it in this.state.allRoles
    const sleep = (ms) => {
      return new Promise((resolve) => setTimeout(resolve, ms))
    }
    await sleep(2000)
    console.info('done sleeping:')

    let allRoles = [
      { id: 'EMPLOYEE', name: 'Employee' },
      { id: 'OWNER', name: 'Owner' },
      { id: 'SALES_REP', name: 'Sales Representative' },
      { id: 'SUPER_ADMIN', name: 'Super Admin' },
      { id: 'TECHNICIAN', name: 'Technician' },
    ]
    allRoles = JSON.stringify(allRoles)
    this.setState({
      allRoles: allRoles,
      isRolesLoaded: true,
      isRolesLocked: false,
    })

    // Call AJAX, return a set of allPermissions. Put it in
    // this.state.allPermissions
    let allPermissions = [
      {
        id: 'CREATE_OR_EDIT_PRODUCT_TYPE',
        name: 'product.type.create_or_edit',
      },
      { id: 'CREATE_OR_EDIT_USERS', name: 'dashboard.use' },
      { id: 'EDIT_SAVED_PRODUCT', name: 'inventoryItem.view.edit' },
      { id: 'IS_EMPLOYEE', name: 'user.is.employee' },
      { id: 'MUTATE_CART', name: 'cart.mutate' },
      { id: 'SEE_ALL_OPEN_CARTS', name: 'carts.view.all_open' },
      { id: 'UPDATE_PRODUCT_PRICE', name: 'product.price.update' },
      { id: 'UPDATE_RAW_PRODUCTS', name: 'product.raw.update' },
      { id: 'WORK_ORDER_OPTIONAL_PERSON', name: 'workOrder.optional.person' },
    ]
    allPermissions = JSON.stringify(allPermissions)
    this.setState({
      allPermissions: allPermissions,
      isPermissionsLoaded: true,
      isPermissionsLocked: false,
    })

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

  setSelected(event) {
    this.setState({ roleSelected: event.target.value })
  }

  onChange(event) {
    const target = event.target
    console.info('target', target)
    const value = target.type === 'checkbox' ? target.checked : target.value
    const name = target.name
    console.info(name, '=', value)
    // doesn't get current state.name and state.length. This just makes sure
    // that there is something in the field besides a single character.
    const isLocked = this.state.name.length < 2 || this.state.email.length < 3
    this.setState({
      [name]: value,
      isLocked: isLocked,
    })
  }

  render() {
    console.info('d: rendering')
    console.info('d: state', this.state)
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
              isLoaded={this.state.isUserLoaded}
              isLocked={this.state.isLocked}
              name={this.state.name}
              onChange={this.onChange}
              users={this.state.allUsers}
              value={this.state.value}
            />
            <UserRoles
              className="py-1"
              isLoading={!this.state.isRolesLoaded}
              isLocked={this.state.isLocked}
              roles={this.state.allRoles}
              onClick={this.setSelected}
              roleSelected={this.state.roleSelected}
            />
            <UserPermissions
              className="py-1"
              isLoading={!this.state.isPermissionsLoaded}
              isLocked={this.state.isLocked}
              permissions={this.state.allPermissions}
            />
          </CardBody>
        </Card>
      </Container>
    )
  }
}

export default WorkOrder

if (document.getElementById('dashboard')) {
  console.log('got dashboard')
  ReactDOM.render(<Dashboard />, document.getElementById('dashboard'))
}
