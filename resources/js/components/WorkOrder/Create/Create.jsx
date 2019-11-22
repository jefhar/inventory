import React from "react";
import {
  Alert,
  Button,
  Card,
  CardBody,
  Col,
  Form,
  FormFeedback,
  FormGroup,
  Input,
  Label,
  Row
} from "reactstrap";

import CompanyName from "../Elements/CompanyName";
import FormHeader from "../Elements/FormHeader";

class Create extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      company_name: "",
      first_name: "",
      invalid_company_name: false,
      isLoading: false,
      last_name: "",
      login: true
    };
  }

  /**
   * @param selected The array of item objects. selected[0] is the
   * chosen object.
   */
  handleChange = selected => {
    this.setState({
      company_name: selected[0].company_name,
      first_name: selected[0].first_name || this.state.first_name,
      invalid_company_name: false,
      last_name: selected[0].last_name || this.state.last_name
    });
  };

  handleButtonClick = () => {
    axios
      .post("/workorders", {
        company_name: this.state.company_name,
        first_name: this.state.first_name,
        last_name: this.state.last_name
      })
      .then(response => {
        if (response.data.workorder_id) {
          window.location =
            "/workorders/" + response.data.workorder_id + "/edit";
        }
      })
      .catch(error => {
        console.debug(error);
        if (error.response) {
          if (error.response.status === 422) {
            console.log(error.response.data.errors);
            if (error.response.data.errors.company_name) {
              this.setState({
                invalid_company_name: true
              });
            }
          }
          if (error.response.status === 401) {
            this.setState({ login: false });
          }
        } else if (error.request) {
          console.log(error.request);
        } else {
          // Something happened in setting up the request that triggered an Error
          console.log("Error", error.message);
        }
        console.log(error.config);
      });
  };

  /**
   * Handles when form field blurs.
   * @param event
   */
  handleBlur = event => {
    let company_name = this.state.company_name || "";
    if (event.target.name === "company_name") {
      company_name = event.target.defaultValue;
      this.setState({
        invalid_company_name: false
      });
    }
    this.setState({
      company_name: company_name,
      first_name: this.state.first_name,
      last_name: this.state.last_name
    });
  };

  /**
   * Handles api search
   * @param query
   */
  handleSearch = query => {
    this.setState({ isLoading: true, company_name: query });
    axios
      .get(`/ajaxsearch/company_name?q=${query}`)
      .then(response => {
        this.setState({
          isLoading: false,
          options: response.data,
          company_name: query
        });
      })
      .catch(error => {
        console.debug(error);
      });
  };

  /**
   * Handles changing of Name fields
   * @param event
   */
  handleNameChange = event => {
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
              <FormGroup>
                <Label className="col-form-label" for="company_name">
                  Client Name
                </Label>
                <CompanyName
                  className={
                    (this.state.invalid_company_name
                      ? "is-invalid"
                      : "is-valid") + " form-control form-control-sm"
                  }
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
                  invalid={this.state.invalid_company_name}
                  selectHintOnEnter={true}
                />
                <FormFeedback valid={!this.state.invalid_company_name}>
                  Company Name is required.
                </FormFeedback>
                <Alert color="info" isOpen={this.state.invalid_company_name}>
                  The Company Name field is required.
                </Alert>
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
              <FormGroup row={true}>
                <Button
                  block
                  outline
                  color="success"
                  onClick={this.handleButtonClick}
                >
                  Create New Work Order
                </Button>
              </FormGroup>
              <FormGroup>
                <Alert
                  className="row"
                  color="danger"
                  isOpen={!this.state.login}
                >
                  <h3 className="alert-heading">User Not Logged In</h3>
                  <div>
                    You are not logged in to the application. Please{" "}
                    <a className="alert-link" href="/login">
                      login
                    </a>{" "}
                    and try again.
                  </div>
                </Alert>
              </FormGroup>
            </Form>
          </CardBody>
        </Card>
      </Row>
    );
  }
}

export default Create;
