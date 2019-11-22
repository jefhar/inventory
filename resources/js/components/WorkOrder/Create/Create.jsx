import React from "react";
import {
  Alert,
  Button,
  Card,
  CardBody,
  Col,
  Form,
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
      isLoading: false,
      last_name: "",
      invalid_company_name: false
    };
  }

  handleChange = selected => {
    console.log("onChange");
    // console.log(selected[0])
    this.setState({
      company_name: selected[0].company_name,
      first_name: selected[0].first_name || this.state.first_name,
      invalid_company_name: false,
      last_name: selected[0].last_name || this.state.last_name
    });
  };

  handleButtonClick = () => {
    console.log("tryPost:");
    /*axios.interceptors.response.use(
      function (response) {
        console.log('intercepted response')
        console.log(response.data)
        console.log(response.headers);
        if (response.data.workorder_id) {
          console.log(
            'redirect to /workorders/' + response.data.workorder_id + '/edit')
          window.location = '/workorders/' + response.data.workorder_id +
            '/edit'
        }
        return response
      },
      function (error) {
        console.log('intercepted error')
        console.log(error)
        return Promise.reject(error)
      }
    )*/
    axios
      .post("/workorders", {
        company_name: this.state.company_name,
        first_name: this.state.first_name,
        last_name: this.state.last_name
      })
      .then(response => {
        console.log(response.data);
        console.log(response.status);
        console.log(response.statusText);
        console.log(response.headers);
        console.log(response.config);
        if (response.data.workorder_id) {
          window.location =
            "/workorders/" + response.data.workorder_id + "/edit";
        }
      })
      .catch(error => {
        console.debug(error);
        if (error.response) {
          // The request was made and the server responded with a status code
          // that falls out of the range of 2xx
          // console.log(error.response.data)
          // console.log(error.response.status)
          // console.log(error.response.headers)
          if (error.response.status === 422) {
            console.log(error.response.data.errors);
            if (error.response.data.errors.company_name) {
              this.setState({
                invalid_company_name: true
              });
              console.log(
                "company_name error: " + error.response.data.errors.company_name
              );
            }
          }
        } else if (error.request) {
          // The request was made but no response was received
          // `error.request` is an instance of XMLHttpRequest in the browser and
          // an instance of http.ClientRequest in node.js
          console.log(error.request);
        } else {
          // Something happened in setting up the request that triggered an Error
          console.log("Error", error.message);
        }
        console.log(error.config);
      });
  };

  oldHandleChange = selected => {
    console.log("onOldChange");
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

  /**
   * Handles when form field blurs.
   * @param event
   */
  handleBlur = event => {
    console.log("handleBlur");
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
    console.log("handleSearch");
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
    // console.log('handleNameChange')
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

                <Alert color="danger" isOpen={this.state.invalid_company_name}>
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
