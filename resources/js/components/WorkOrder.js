import React from "react";
import ReactDOM from "react-dom";
import {
  Card,
  CardBody,
  CardHeader,
  Container,
  FormGroup,
  Input,
  InputGroup,
  InputGroupAddon,
  InputGroupText,
  Label,
  Row
} from "reactstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCloudDownloadAlt } from "@fortawesome/free-solid-svg-icons";
import ClientCompanyName from "./WorkOrder/ClientCompanyName";

const axios = require("axios").default;

class WorkOrder extends React.Component {
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
      <Container>
        <Row>
          <h1 className="text-center">Create Work Order</h1>
        </Row>
        <Row className="shadow-sm">
          <Card className="col-md">
            <CardHeader>
              <h2>Client Information</h2>
            </CardHeader>
            <CardBody>
              <FormGroup row={true}>
                <InputGroup>
                  <ClientCompanyName
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
                  <InputGroupAddon addonType="append">
                    <InputGroupText id="checkClientExists">
                      <FontAwesomeIcon
                        className="text-muted"
                        icon={faCloudDownloadAlt}
                      />
                    </InputGroupText>
                  </InputGroupAddon>
                </InputGroup>
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
      </Container>
    );
  }
}

/*
function FullWorkOrder () {

  return (
    <Container>
      <Row>
        <h1 className="text-center">Create Work Order</h1>
      </Row>
      <Row className="shadow-sm">
        <Card className="col-md">
          <CardHeader>
            <h2>Client Information</h2>
          </CardHeader>
          <CardBody>
            <ClientCompanyName/>
            <FormGroup row={true}>
              <InputGroup>
                <Input
                  type="text"
                  disabled={true}
                  className="form-control form-control-sm"
                  id="client.company_name"
                  name="client.company_name"
                  placeholder="Client's company name"
                />
                <InputGroupAddon addonType="append">
                  <InputGroupText id="checkClientExists">
                    <FontAwesomeIcon
                      className="text-muted"
                      icon={faCloudDownloadAlt}
                    />
                  </InputGroupText>
                </InputGroupAddon>
              </InputGroup>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-3 col-md-4 col-lg-3 col-xl-2 col-form-label"
                for="client.address.primary"
              >
                Address:
              </Label>
              <div className="col-sm-9 col-md-8 col-lg-9 col-xl-10">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="client.address.primary"
                  name="client.address.primary"
                  placeholder="Street Address"
                />
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-3 col-md-4 col-lg-3 col-xl-2 col-form-label"
                for="client.address.secondary"
              >
                Suite:
              </Label>
              <div className="col-sm-9 col-md-8 col-lg-9 col-xl-10">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="client.address.secondary"
                  name="client.address.secondary"
                  placeholder="Suite/Building/Apartment"
                />
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-3 col-md-4 col-lg-3 col-xl-2 col-form-label"
                for="client.address.city"
              >
                City:
              </Label>
              <div className="col-sm-9 col-md-8 col-lg-9 col-xl-10">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="client.address.city"
                  name="client.address.city"
                  placeholder="City"
                />
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-2 col-md-4 col-lg-2 col-form-label"
                for="client.address.state"
              >
                State:
              </Label>
              <div className="col-sm-4 col-md-8 col-lg-4">
                <Input
                  type="select"
                  className="form-control form-control-sm"
                  id="client.address.state"
                  name="client.address.state"
                >
                  <option value="CA">California</option>
                  <option value="NV">Nevada</option>
                  <option value="WV">West Virginia</option>
                </Input>
              </div>
              <Label
                className="col-sm-3 col-md-4 col-lg-3 col-form-label"
                for="client.address.zip"
              >
                Zip Code:
              </Label>
              <div className="col-sm-3 col-md-8 col-lg-3">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="client.address.zip"
                  name="client.address.zip"
                  placeholder="ZipCode"
                  maxlength="5"
                />
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-2 d-md-none col-form-label"
                for="client.person.first_name"
              >
                Name:
              </Label>
              <div className="col">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="client.person.first_name"
                  name="client.person.first_name"
                  placeholder="Joe"
                />
              </div>
              <div className="col">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="client.person.last_name"
                  name="client.person.last_name"
                  placeholder="Smith"
                />
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-2 col-md-3 col-form-label"
                for="client.person.email"
              >
                Email:
              </Label>
              <div className="col-sm-10 col-md-9">
                <InputGroup size="sm">
                  <InputGroupAddon addonType="prepend">
                    <InputGroupText className="text-muted">
                      @
                    </InputGroupText>
                  </InputGroupAddon>
                  <Input
                    type="text"
                    className="form-control form-control-sm"
                    id="client.person.email"
                    name="client.person.email"
                    placeholder="email@address"
                  />
                </InputGroup>
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-2 col-md-3 col-form-label"
                for="client.person.phone"
              >
                Phone:
              </Label>
              <div className="col-sm-9 col-md-7 col-lg-8">
                <InputGroup size="sm">
                  <InputGroupAddon addonType="prepend">
                    <InputGroupText>
                      <FontAwesomeIcon
                        icon={faPhone}
                        className="text-muted"
                      />
                    </InputGroupText>
                  </InputGroupAddon>
                  <Input
                    type="text"
                    className="form-control form-control-sm"
                    id="client.person.phone"
                    name="client.person.phone"
                    placeholder="Phone"
                  />
                </InputGroup>
              </div>
              <div
                className="col-sm-1"
                id="copyClientAddressToWorkOrder"
              >
                <FontAwesomeIcon icon={faShareSquare}/>
              </div>
            </FormGroup>
          </CardBody>
        </Card>
        <Card className="col-md">
          <CardHeader>
            <h2>Location Information</h2>
          </CardHeader>
          <CardBody>
            <FormGroup row={true}>
              <Input
                type="text"
                className="form-control form-control-sm"
                id="client.company_name.duplicate"
                name="client.company_name.duplicate"
                plaintext={true}
                value="Client's company name copied from LHP."
              />
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-3 col-md-4 col-lg-3 col-xl-2 col-form-label"
                for="address.primary"
              >
                Address:
              </Label>
              <div className="col-sm-9 col-md-8 col-lg-9 col-xl-10">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="address.primary"
                  name="address.primary"
                  placeholder="Street Address"
                />
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-3 col-md-4 col-lg-3 col-xl-2 col-form-label"
                for="address.secondary"
              >
                Suite:
              </Label>
              <div className="col-sm-9 col-md-8 col-lg-9 col-xl-10">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="address.secondary"
                  name="address.secondary"
                  placeholder="Suite/Building/Apartment"
                />
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-3 col-md-4 col-lg-3 col-xl-2 col-form-label"
                for="address.city"
              >
                City:
              </Label>
              <div className="col-sm-9 col-md-8 col-lg-9 col-xl-10">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="address.city"
                  name="address.city"
                  placeholder="City"
                />
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-2 col-md-4 col-lg-2 col-form-label"
                for="address.state"
              >
                State:
              </Label>
              <div className="col-sm-4 col-md-8 col-lg-4">
                <Input
                  type="select"
                  className="form-control form-control-sm"
                  id="address.state"
                  name="address.state"
                >
                  <option value="CA">California</option>
                  <option value="NV">Nevada</option>
                  <option value="WV">West Virginia</option>
                </Input>
              </div>
              <Label
                className="col-sm-3 col-md-4 col-lg-3 col-form-label"
                for="address.zip"
              >
                ZipCode:
              </Label>
              <div className="col-sm-3 col-md-8 col-lg-3">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="address.zip"
                  name="address.zip"
                  placeholder="ZipCode"
                  maxlength="5"
                />
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-2 d-md-none col-form-label"
                for="client.person.first_name"
              >
                Name:
              </Label>
              <div className="col">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="address.person.first_name"
                  name="address.person.first_name"
                  placeholder="Joe"
                />
              </div>
              <div className="col">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  id="address.person.last_name"
                  name="address.person.last_name"
                  placeholder="Smith"
                />
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-2 col-md-3 col-form-label"
                for="address.person.email"
              >
                Email:
              </Label>
              <div className="col-sm-10 col-md-9">
                <InputGroup size="sm">
                  <InputGroupAddon addonType="prepend">
                    <InputGroupText className="text-muted">
                      @
                    </InputGroupText>
                  </InputGroupAddon>
                  <Input
                    type="text"
                    className="form-control form-control-sm"
                    id="address.person.email"
                    name="address.person.email"
                    placeholder="email@address"
                  />
                </InputGroup>
              </div>
            </FormGroup>
            <FormGroup row={true}>
              <Label
                className="col-sm-2 col-md-3 col-form-label"
                for="address.person.phone"
              >
                Phone:
              </Label>
              <div className="col-sm-10 col-md-9">
                <InputGroup size="sm">
                  <InputGroupAddon addonType="prepend">
                    <InputGroupText>
                      <FontAwesomeIcon
                        icon={faPhone}
                        className="text-muted"
                      />
                    </InputGroupText>
                  </InputGroupAddon>
                  <Input
                    type="text"
                    className="form-control form-control-sm"
                    id="address.person.phone"
                    name="address.person.phone"
                    placeholder="Phone"
                  />
                </InputGroup>
              </div>
            </FormGroup>
          </CardBody>
        </Card>
      </Row>
      <Row className="shadow-sm mt-3">
        <Card className="col-md">
          <CardHeader>
            <h2>Intake Information</h2>
          </CardHeader>
          <CardBody className="row">
            <Input
              type="textarea"
              className="form-control form-control-sm"
              name="intake"
              id="intake"
              rows="4"
            />
          </CardBody>
        </Card>
      </Row>
      <Row className="shadow-sm mt-3">
        <Card className="col-md">
          <CardHeader>
            <h2>Product Information</h2>
          </CardHeader>
          <CardBody className="row">
            Quick Add SKU [===] table
          </CardBody>
        </Card>
      </Row>
    </Container>
  )
}
*/

export default WorkOrder;

if (document.getElementById("workorder")) {
  ReactDOM.render(<WorkOrder />, document.getElementById("workorder"));
}
