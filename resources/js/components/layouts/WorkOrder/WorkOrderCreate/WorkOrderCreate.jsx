import * as React from 'react'
import CompanyClientName from '../Elements/CompanyClientName'
import FormHeader from '../Elements/FormHeader'
import PropTypes from 'prop-types'
import ReactDOM from 'react-dom'
import { Card, CardBody, Container, Row } from 'reactstrap'

const propTypes = {
  draft: PropTypes.string.isRequired,
  handleResponse: PropTypes.func,
  postPath: PropTypes.string.isRequired,
}

const handleResponse = (response) => {
  console.info('WOC handleResponse')
  // Create a post_post so workorders and carts can have their own post-post
  // functions
  if (response.data.workorder_id) {
    window.location = '/workorders/' + response.data.workorder_id + '/edit'
  }
}

class WorkOrderCreate extends React.Component {
  constructor(props) {
    super(props)
    console.log('WOC constructor')
    this.state = {
      company_name: '',
      first_name: '',
      invalid_company_name: false,
      isLoading: false,
      last_name: '',
      login: true,
    }
  }

  render() {
    console.log('rendering')
    return (
      <Container>
        <Row className="shadow-sm">
          <Card className="col-md">
            <FormHeader workOrderId="-----" />
            <CardBody>
              <CompanyClientName
                draft={this.props.draft}
                handleResponse={this.props.handleResponse}
                postPath={this.props.postPath}
              />
            </CardBody>
          </Card>
        </Row>
      </Container>
    )
  }
}

WorkOrderCreate.propTypes = propTypes

export default WorkOrderCreate

if (document.getElementById('workorder_create')) {
  console.log('got workorder_create')
  ReactDOM.render(
    <WorkOrderCreate
      draft="Work Order"
      handleResponse={handleResponse}
      postPath="/workorders/"
    />,
    document.getElementById('workorder_create')
  )
}
