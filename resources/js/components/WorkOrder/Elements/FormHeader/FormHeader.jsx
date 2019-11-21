import React from "react";
import { CardHeader, Col, Row } from "reactstrap";

function FormHeader(props) {
  return (
    <CardHeader tag="row">
      <Row>
        <Col tag="h1" className="text-center">
          Create Work Order
        </Col>
        <Col xs={2} sm={3} md={2}>
          <Row>Work Order #</Row>
          <Row>{props.workOrderId}</Row>
        </Col>
      </Row>
    </CardHeader>
  );
}

export default FormHeader;
