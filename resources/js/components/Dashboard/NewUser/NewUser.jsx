import * as React from 'react'
import PropTypes from 'prop-types'
import {
  Col,
  Form,
  FormFeedback,
  FormGroup,
  Input,
  Label,
  Row,
} from 'reactstrap'

class NewUser extends React.Component {
  constructor(props) {
    super(props)
    this.state = {}
  }

  render() {
    if (!this.props.show) {
      return null
    }

    return (
      <>
        <p className="lead">Create a New User:</p>
        <Form>
          <Row form>
            <Col sm={6}>
              <FormGroup>
                <Label for="name">Name </Label>
                <Input
                  id="name"
                  invalid={this.props.isLocked}
                  name="name"
                  onChange={this.props.onChange}
                  placeholder="User's Name"
                  type="text"
                  valid={!this.props.isLocked}
                  value={this.props.name}
                />
                <FormFeedback>Name and/or Email too short.</FormFeedback>
              </FormGroup>
            </Col>

            <Col sm={6}>
              <FormGroup>
                <Label for="email">Email </Label>
                <Input
                  id="email"
                  invalid={this.props.isLocked}
                  name="email"
                  onChange={this.props.onChange}
                  placeholder="Email"
                  type="email"
                  valid={!this.props.isLocked}
                  value={this.props.email}
                />
                <FormFeedback>Name and/or Email too short.</FormFeedback>
              </FormGroup>
            </Col>
          </Row>
        </Form>
        <p>{JSON.stringify(this.props.isLocked)}</p>
      </>
    )
  }
}

NewUser.propTypes = {
  email: PropTypes.string,
  isLocked: PropTypes.bool,
  name: PropTypes.string,
  onChange: PropTypes.func,
  show: PropTypes.bool,
}

export default NewUser
