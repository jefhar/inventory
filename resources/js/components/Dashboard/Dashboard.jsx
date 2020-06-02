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
      isPermissionsLoading: false,
      isRolesLoaded: false,
      isRolesLocked: true,
      isUserLoaded: false,
    }
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
    console.info('allRoles', allRoles)

    this.setState({
      allRoles: allRoles,
      isRolesLoaded: true,
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
      isPermissionsLoading: false,
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

  render() {
    console.info('d: rendering')
    console.info('d: isRolesLoaded', this.state.isRolesLoaded)
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
              users={this.state.allUsers}
            />
            <UserRoles
              className="py-1"
              isLoading={!this.state.isRolesLoaded}
              roles={this.state.allRoles}
              isLocked={this.state.isRolesLocked}
            />
            <UserPermissions
              className="py-1"
              isLoaded={this.state.isPermissionsLoaded}
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
