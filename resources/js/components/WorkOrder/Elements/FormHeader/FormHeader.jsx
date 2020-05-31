import * as React from 'react'
import { CardHeader, Col, Row } from 'reactstrap'

function FormHeader(props) {
  return (
    <CardHeader>
      <Row>
        <Col tag="h1" className="text-center">
          Create Work Order
        </Col>
        <Col xs={3} sm={3} md={2} className="shadow-sm">
          <Row className="justify-content-center">Work&nbsp;Order&nbsp;#</Row>
          <Row className="justify-content-center">{props.workOrderId}</Row>
        </Col>
      </Row>
    </CardHeader>
  )
}

export default FormHeader
