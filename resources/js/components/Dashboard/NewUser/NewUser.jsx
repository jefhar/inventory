import * as React from 'react'
import PropTypes from 'prop-types'
import { Col, Form, FormGroup, Input, Label, Row } from 'reactstrap'

class NewUser extends React.Component {
  constructor(props) {
    super(props)
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
                <Label for="email">Email </Label>
                <Input type="email" id="email" placeholder="Email" />
              </FormGroup>
            </Col>

            <Col sm={6}>
              <FormGroup>
                <Label for="name">Name </Label>
                <Input type="text" id="name" placeholder="User's Name" />
              </FormGroup>
            </Col>
          </Row>
        </Form>
      </>
    )
  }
}

NewUser.propTypes = {
  show: PropTypes.bool,
}

export default NewUser
