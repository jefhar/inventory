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
    // console.log('onChange')
    // console.log(selected[0])
    const postData = {
      company_name: selected[0].company_name,
      first_name: selected[0].first_name || this.state.first_name,
      last_name: selected[0].last_name || this.state.last_name
    };
    this.setState(postData);
    this.tryPost(postData);
  };

  tryPost = postData => {
    console.log("tryPost:");
    console.log(postData);
    axios.interceptors.response.use(
      function(response) {
        console.log("intercepted response");
        console.log(response);
        if (response.data.id) {
          console.log("redirect to /workorders/" + response.data.id + "/edit");
          window.location = "/workorders/" + response.data.id + "/edit";
        }
        return response;
      },
      function(error) {
        console.log("intercepted error");
        console.log(error);
        return Promise.reject(error);
      }
    );
    axios
      .post("/workorders", {
        company_name: postData.company_name,
        first_name: postData.first_name,
        last_name: postData.last_name
      })
      .then(response => {
        console.log("response");
        console.log(response.data);
        console.log(response.status);
        console.log(response.statusText);
        console.log(response.headers);
        console.log(response.config);
      })
      .catch(error => {
        console.debug(error);
        if (error.response) {
          // The request was made and the server responded with a status code
          // that falls out of the range of 2xx
          console.log(error.response.data);
          console.log(error.response.status);
          console.log(error.response.headers);
          if (error.response.status === 422) {
            // Something missing
            this.handle422();
          }
          if (error.response.status === 401) {
            // Unauthorized
            this.handle401();
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
    // console.log('handleBlur');
    let company_name = "";
    if (event.target.name === "company_name") {
      company_name = event.target.defaultValue;
    }

    this.tryPost({
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
    // console.log('handleSearch')
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

  handle422() {}

  handle401() {}
}

export default Create;
