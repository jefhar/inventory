import * as React from 'react'
import ReactDOM from 'react-dom'
import UserRoles from './UserRoles'
import WorkOrder from '../WorkOrder'
import UserPermissions from './UserPermissions'
import UserChooser from './UserChooser'

class Dashboard extends React.Component {
  constructor(props) {
    super(props)
    console.log('constructing.')
  }

  render() {
    console.log('rendering.')
    return (
      <>
        <div className="container">
          <div className="card">
            <div className="card-header">
              <h1>
                User Dashboard <i className="fas fa-users"></i>
              </h1>
            </div>
            <div className="card-body">
              <UserRoles />
              <UserPermissions />
              <UserChooser />
            </div>
          </div>
        </div>
      </>
    )
  }
}

export default WorkOrder

if (document.getElementById('dashboard')) {
  console.log('got dashboard')
  ReactDOM.render(<Dashboard />, document.getElementById('dashboard'))
}
