import React from "react";
import {
  Card,
  CardBody,
  CardHeader,
  Col,
  Form,
  FormGroup,
  Input,
  InputGroup,
  Label,
  Row
} from "reactstrap";

import CompanyName from "../Elements/CompanyName";
import FormHeader from "../Elements/FormHeader";

class Create extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isLoading: false,
      first_name: "",
      last_name: ""
    };
  }

  handleChange = selected => {
    console.log("onChange");
    if (selected[0]) {
      axios
        .post("/workorders", {
          company_name: selected[0].company_name,
          first_name: selected[0].first_name,
          last_name: selected[0].last_name
        })
        .then(response => {
          const { headers, status } = response;
          console.log("then");
          console.log(headers);
          console.log(status);
          console.log(response);
        })
        .catch(error => {
          console.log("error");
          console.log(error);
        });
      console.log("id:" + selected[0].client_id);
      console.log("first:" + selected[0].first_name);
      this.setState({
        first_name: selected[0].first_name,
        last_name: selected[0].last_name
      });
    } else {
      this.setState({
        first_name: "",
        last_name: ""
      });
    }
  };
  handleBlur = event => {
    console.log("onBlur");
    console.log(event.target.name);
  };
  handleSearch = query => {
    console.log("handleSearch");
    this.setState({ isLoading: true });
    axios
      .get(`/ajaxsearch/company_name?q=${query}`)
      .then(response => {
        this.setState({
          isLoading: false,
          options: response.data
        });
      })
      .catch(error => {
        console.debug(error);
      });
  };
  handleNameChange = event => {
    console.log("handleNameChange");
    console.log(event);
    console.log(event.target.name);
    const target = event.target;
    const value = target.type === "checkbox" ? target.checked : target.value;
    const name = target.name;
    this.setState({
      [name]: value
    });
  };

  render() {
    return (
      <Row className="shadow-sm">
        <Card className="col-md">
          <FormHeader workOrderId="-----" />
          <CardBody>
            <Form>
              <FormGroup row={true}>
                <Label className="col-form-label" for="company_name">
                  Client Name
                </Label>
                <InputGroup>
                  <CompanyName
                    className="form-control form-control-sm"
                    id="company_name"
                    isLoading={this.state.isLoading}
                    handleChange={this.handleChange}
                    labelKey="company_name"
                    name="company_name"
                    newSelectionPrefix="New Client:"
                    onSearch={this.handleSearch}
                    onBlur={this.handleBlur}
                    options={this.state.options}
                    placeholder="Client's company name"
                    required="required"
                  />
                </InputGroup>
              </FormGroup>
              <FormGroup row={true}>
                <Label
                  className="col-sm-2 d-md-none col-form-label"
                  for="first_name"
                >
                  Name:
                </Label>
                <Col>
                  <Input
                    className="form-control form-control-sm"
                    id="first_name"
                    name="first_name"
                    onChange={this.handleNameChange}
                    placeholder="Joe"
                    type="text"
                    value={this.state.first_name}
                    onBlur={this.handleBlur}
                  />
                </Col>
                <Col>
                  <Input
                    className="form-control form-control-sm"
                    id="last_name"
                    name="last_name"
                    onChange={this.handleNameChange}
                    placeholder="Smith"
                    type="text"
                    value={this.state.last_name}
                    onBlur={this.handleBlur}
                  />
                </Col>
              </FormGroup>
            </Form>
          </CardBody>
        </Card>
      </Row>
    );
  }

  eventuallyRender() {
    return (
      <Row>
        <Card>
          <CardHeader>
            <div className="col">Create New Work Order</div>
            <div className="col text-muted">Invoice Number</div>
          </CardHeader>
          <CardBody>
            <FormGroup row={true}>
              <CompanyName
                className="form-control form-control-sm"
                id="company_name"
                isLoading={this.state.isLoading}
                handleChange={this.handleChange}
                labelKey="company_name"
                name="company_name"
                newSelectionPrefix="New Client:"
                onSearch={this.handleSearch}
                onBlur={this.handleBlur}
                options={this.state.options}
                placeholder="Client's company name"
                required="required"
              />
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-2 d-md-none col-form-label"
                for="first_name"
              >
                Name:
              </Label>
              <div className="col">
                <Input
                  className="form-control form-control-sm"
                  id="first_name"
                  name="first_name"
                  onChange={this.handleNameChange}
                  placeholder="Joe"
                  type="text"
                  value={this.state.first_name}
                  onBlur={this.handleBlur}
                />
              </div>
              <div className="col">
                <Input
                  className="form-control form-control-sm"
                  id="last_name"
                  name="last_name"
                  onChange={this.handleNameChange}
                  placeholder="Smith"
                  type="text"
                  value={this.state.last_name}
                  onBlur={this.handleBlur}
                />
              </div>
            </FormGroup>
          </CardBody>
        </Card>
      </Row>
    );
  }
}

export default Create;
