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

const propTypes = {
  email: PropTypes.string,
  invalidName: PropTypes.bool,
  isValidEmail: PropTypes.bool,
  name: PropTypes.string,
  onChange: PropTypes.func,
  show: PropTypes.bool,
}

function NewUser(props) {
  // console.info('NewUser Props:', props)
  if (!props.show) {
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
                invalid={props.name.length < 3}
                name="name"
                onChange={props.onChange}
                placeholder="User's Name"
                type="text"
                valid={props.name.length >= 3}
                value={props.name}
              />
              <FormFeedback>Name is too short.</FormFeedback>
            </FormGroup>
          </Col>

          <Col sm={6}>
            <FormGroup>
              <Label for="email">Email </Label>
              <Input
                id="email"
                invalid={!props.isValidEmail}
                name="email"
                onChange={props.onChange}
                placeholder="Email"
                type="email"
                valid={props.isValidEmail}
                value={props.email}
              />
              <FormFeedback>Not a valid formed email.</FormFeedback>
            </FormGroup>
          </Col>
        </Row>
      </Form>
      <p>{JSON.stringify(props)}</p>
    </>
  )
}

NewUser.propTypes = propTypes

export default NewUser
