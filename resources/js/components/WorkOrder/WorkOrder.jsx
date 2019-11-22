import React, { Component } from "react";
import ReactDOM from "react-dom";
import Create from "./Create";
import { Container } from "reactstrap";

class WorkOrder extends Component {
  constructor(props) {
    super(props);
    console.log("constructing.");
  }

  render() {
    console.log("rendering.");
    return (
      <Container>
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
