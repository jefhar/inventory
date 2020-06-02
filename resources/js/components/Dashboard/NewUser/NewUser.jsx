import * as React from 'react'
import PropTypes from 'prop-types'
import { Col, Form, FormGroup, Input, Label, Row } from 'reactstrap'

class NewUser extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      email: '',
      isLocked: true,
      name: '',
    }
    this.checkInputs = this.checkInputs.bind(this)
  }

  render() {
    if (!this.props.show) {
      return null
    }
    const className = `is-${this.state.isLocked ? 'in' : ''}valid`
    return (
      <>
        <p className="lead">Create a New User:</p>
        <Form>
          <Row form>
            <Col sm={6}>
              <FormGroup>
                <Label for="name">Name </Label>
                <Input
                  className={className}
                  id="name"
                  name="name"
                  onChange={this.checkInputs}
                  placeholder="User's Name"
                  type="text"
                  value={this.state.name}
                />
              </FormGroup>
            </Col>

            <Col sm={6}>
              <FormGroup>
                <Label for="email">Email </Label>
                <Input
                  className={className}
                  id="email"
                  name="email"
                  onChange={this.checkInputs}
                  placeholder="Email"
                  type="email"
                  value={this.state.email}
                />
              </FormGroup>
            </Col>
          </Row>
        </Form>
        <p>{JSON.stringify(this.state.isLocked)}</p>
      </>
    )
  }

  checkInputs(event) {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value
    const name = target.name
    this.setState({
      [name]: value,
    })
    this.setState((state) => {
      console.info('name length', state.name.length > 1)
      console.info('email length', state.email.length > 1)
      console.info('boolean', state.name.length < 3 || state.email.length < 3)
      return { isLocked: state.name.length < 3 || state.email.length < 3 }
    })
  }
}

NewUser.propTypes = {
  show: PropTypes.bool,
}

export default NewUser
