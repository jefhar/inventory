import React from "react";
import ReactDOM from "react-dom";
import { Card, CardBody, Container, Row } from "reactstrap";
import FormHeader from "../Elements/FormHeader";
import CompanyClientName from "../Elements/CompanyClientName";

class WorkOrderCreate extends React.Component {
  constructor(props) {
    super(props);
    console.log("WOC constructor");
    this.state = {
      company_name: "",
      first_name: "",
      invalid_company_name: false,
      isLoading: false,
      last_name: "",
      login: true
    };
  }

  handleResponse = response =>
    {
      // Create a post_post so workorders and carts can have their own post-post
      // functions
      if (response.data.workorder_id) {
        window.location =
          "/workorders/" + response.data.workorder_id + "/edit";
      }
  };

  render() {
    console.log("rendering");
    return (
      <Container>
        <Row className="shadow-sm">
          <Card className="col-md">
            <FormHeader workOrderId="-----" />
            <CardBody>
              <CompanyClientName postPath="/workorders" draft="Work Order" />
            </CardBody>
          </Card>
        </Row>
      </Container>
    );
  }
}

export default WorkOrderCreate;

if (document.getElementById("workorders_create")) {
  console.log("got workorders_create");
  ReactDOM.render(
    <WorkOrderCreate handleResponse={this.handleResponse} postPath="/workorders/" draft="Work Order" />,
    document.getElementById("workorders_create")
  );
}
