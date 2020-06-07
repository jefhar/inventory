import * as React from 'react'
import PropTypes from 'prop-types'
import { CardHeader, Col, Row } from 'reactstrap'

const FormHeader = ({ workOrderId }) => {
  return (
    <CardHeader>
      <Row>
        <Col tag="h1" className="text-center">
          Create New Work Order
        </Col>
        <Col xs={3} sm={3} md={2} className="shadow-sm">
          <Row className="justify-content-center">Work&nbsp;Order&nbsp;#</Row>
          <Row className="justify-content-center">{workOrderId}</Row>
        </Col>
      </Row>
    </CardHeader>
  )
}

FormHeader.propTypes = {
  workOrderId: PropTypes.string.isRequired,
}

export default FormHeader
