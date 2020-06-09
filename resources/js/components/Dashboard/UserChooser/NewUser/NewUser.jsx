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

function NewUser(props) {
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
                invalid={props.isLocked}
                name="name"
                onChange={props.onChange}
                placeholder="User's Name"
                type="text"
                valid={!props.isLocked}
                value={props.name}
              />
              <FormFeedback>Name and/or Email too short.</FormFeedback>
            </FormGroup>
          </Col>

          <Col sm={6}>
            <FormGroup>
              <Label for="email">Email </Label>
              <Input
                id="email"
                invalid={props.isLocked}
                name="email"
                onChange={props.onChange}
                placeholder="Email"
                type="email"
                valid={!props.isLocked}
                value={props.email}
              />
              <FormFeedback>Name and/or Email too short.</FormFeedback>
            </FormGroup>
          </Col>
        </Row>
      </Form>
      <p>{JSON.stringify(props.isLocked)}</p>
    </>
  )
}

NewUser.propTypes = {
  email: PropTypes.string,
  isLocked: PropTypes.bool,
  name: PropTypes.string,
  onChange: PropTypes.func,
  show: PropTypes.bool,
}

export default NewUser
