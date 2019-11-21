import React, { Component } from "react";
import ReactDOM from "react-dom";
import Create from "./Create";
import { Container, Row } from "reactstrap";

class WorkOrder extends Component {
  constructor(props) {
    super(props);
    console.log("constructing.");
  }

  render() {
    console.log("rendering.");
    return (
      <Container>
        <Row>
          <h1 className="text-center"></h1>
        </Row>
        <Create />
      </Container>
    );
  }
}

export default WorkOrder;

if (document.getElementById("workorder")) {
  console.log("got workorder");
  ReactDOM.render(<WorkOrder />, document.getElementById("workorder"));
}
