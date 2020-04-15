import * as React from 'react'
import ReactDOM from 'react-dom'
import WorkOrderCreate from './WorkOrderCreate'
import { Container } from 'reactstrap'

class WorkOrder extends React.Component {
  constructor(props) {
    super(props);
    console.log("constructing.");
  }

  render() {
    console.log("rendering.");
    return (
      <Container>
        <WorkOrderCreate />
      </Container>
    );
  }
}

export default WorkOrder;

if (document.getElementById("workorder")) {
  console.log("got workorder");
  ReactDOM.render(<WorkOrder />, document.getElementById("workorder"));
}
