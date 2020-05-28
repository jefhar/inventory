import * as React from 'react'
import PropTypes from 'prop-types'
import {
  Alert,
  Button,
  Col,
  Form,
  FormFeedback,
  FormGroup,
  Input,
  Label,
  Row,
} from 'reactstrap'
import CompanyName from '../CompanyName'

class CompanyClientName extends React.Component {
  constructor(props) {
    super(props)
    console.log('CCN constructor')
    this.state = {
      companyName: '',
      firstName: '',
      isCompanyNameInvalid: false,
      isLoading: false,
      lastName: '',
      login: true,
    }
  }

  /**
   * Handles when form field blurs.
   * @param event
   */
  handleBlur(event) {
    let companyName = this.state.companyName || ''
    if (event.target.name === 'company_name') {
      companyName = event.target.defaultValue
      this.setState({
        invalid_company_name: false,
      })
    }
    this.setState({ companyName: companyName })
  }

  handleButtonClick() {
    console.info('handleButtonClick')
    const productId = document.getElementById('productId')
    const { companyName, lastName, firstName } = this.state
    const data = {
      company_name: companyName,
      first_name: firstName,
      last_name: lastName,
    }
    if (productId) {
      data.productId = productId.dataset.productId
    }
    axios
      .post(this.props.postPath, data)
      .then((response) => this.props.handleResponse(response))
      .then(() => {
        this.setState({
          companyName: '',
          firstName: '',
          lastName: '',
        })
      })
      .catch((error) => {
        console.info('in catch(error =>')
        console.debug('error:', error)
        if (!error.data) {
          return
        }
        if (error.response) {
          if (error.response.status === 422) {
            console.debug('error.response:', error.response)
            console.log(
              'error.response.data.errors:',
              error.response.data.errors
            )
            if (error.response.data.errors.companyName) {
              this.setState({
                isCompanyNameInvalid: true,
              })
            }
          }
          if (error.response.status === 401) {
            this.setState({ login: false })
          }
        } else if (error.request) {
          console.log('error.request:', error.request)
        } else {
          // Something happened in setting up the request that triggered an
          // Error
          console.log('error.message:', error.message)
        }
        console.log('error.config:', error.config)
      })
  }

  /**
   * @param selected The array of item objects. selected[0] is the
   * chosen object.
   */
  handleChange(selected) {
    if (selected.length < 1) {
      this.setState({
        companyName: '',
        firstName: '',
        isCompanyNameInvalid: false,
        lastName: '',
      })
    } else {
      this.setState({
        companyName: selected[0].company_name,
        firstName: selected[0].first_name || this.state.firstName,
        isCompanyNameInvalid: false,
        lastName: selected[0].last_name || this.state.lastName,
      })
    }
  }

  /**
   * Handles changing of Name fields
   * @param event
   */
  handleNameChange(event) {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value
    const name = target.name
    this.setState({
      [name]: value,
    })
  }

  /**
   * Handles api search
   * @param query
   */
  handleSearch(query) {
    this.setState({ isLoading: true, companyName: query })
    axios
      .get(`/ajaxsearch/company_name?q=${query}`)
      .then((response) => {
        this.setState({
          isLoading: false,
          options: response.data,
          companyName: query,
        })
      })
      .catch((error) => {
        console.debug('error:', error)
      })
  }

  render() {
    return (
      <Form>
        <Row form>
          <Col>
            <FormGroup>
              <Label for="companyName">Client Company Name</Label>
              <CompanyName
                className={
                  (this.state.isCompanyNameInvalid
                    ? 'is-invalid'
                    : 'is-valid') + ' form-control form-control-sm'
                }
                id="companyName"
                isLoading={this.state.isLoading}
                handleChange={this.handleChange}
                labelKey="companyName"
                name="companyName"
                newSelectionPrefix="New Client:"
                onSearch={this.handleSearch}
                onBlur={this.handleBlur}
                options={this.state.options}
                placeholder="Client's company name"
                invalid={this.state.isCompanyNameInvalid}
                selectHintOnEnter={true}
              />
              <FormFeedback valid={!this.state.isCompanyNameInvalid}>
                Company Name is required.
              </FormFeedback>
              <Alert color="warning" isOpen={this.state.isCompanyNameInvalid}>
                The Company Name field is required.
              </Alert>
            </FormGroup>
          </Col>
        </Row>
        <Row form>
          <Col xs={12} md={6}>
            <FormGroup>
              <Label for="firstName">First Name</Label>
              <Input
                className="form-control form-control-sm"
                id="firstName"
                name="firstName"
                onChange={this.handleNameChange}
                placeholder="First Name"
                type="text"
                value={this.state.firstName}
                onBlur={this.handleBlur}
              />
            </FormGroup>
          </Col>
          <Col xs={12} md={6}>
            <FormGroup>
              <Label for="lastName">Last Name</Label>
              <Input
                className="form-control form-control-sm"
                id="lastName"
                name="lastName"
                onChange={this.handleNameChange}
                placeholder="Last Name"
                type="text"
                value={this.state.lastName}
                onBlur={this.handleBlur}
              />
            </FormGroup>
          </Col>
        </Row>
        <Row form>
          <Col xs={{ size: 6, offset: 3 }}>
            <FormGroup row>
              <Button
                block
                outline
                color="success"
                onClick={this.handleButtonClick}
              >
                Create New {this.props.draft}
              </Button>
            </FormGroup>
          </Col>
        </Row>
        <FormGroup row>
          <Alert className="row" color="danger" isOpen={!this.state.login}>
            <h3 className="alert-heading">User Not Logged In</h3>
            <div>
              You are not logged in to the application. Please{' '}
              <a className="alert-link" href="/login">
                login
              </a>{' '}
              and try again.
            </div>
          </Alert>
        </FormGroup>
      </Form>
    )
  }
}

CompanyClientName.propTypes = {
  handleResponse: PropTypes.func.isRequired,
  postPath: PropTypes.string.isRequired,
  draft: PropTypes.string.isRequired,
}
export default CompanyClientName
