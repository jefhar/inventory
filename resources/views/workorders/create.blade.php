@extends('layouts.app')

@section('content')
    <form class="container">
        <div class="row">
            <h1 class="text-center">Create Work Order</h1>
        </div>
        <div class="row">
            <div class="col-sm card">
                <h2 class="card-header">Client Information</h2>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="input-group">
                            <input type="text" class="form-control" id="client.company_name" name="client.company_name"
                                   placeholder="Client's company name"/>
                            <div class="input-group-append">
                            <span class="input-group-text" id="checkClientExists">
                                <i class="text-muted fas fa-cloud-download-alt"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="client.address.primary">Address:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="client.address.primary"
                                   name="client.address.primary"
                                   placeholder="Street Address"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="client.address.primary">Suite:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="client.address.secondary"
                                   name="client.address.secondary"
                                   placeholder="Suite/Building/Apartment"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="client.address.city">City:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="client.address.city" name="client.address.city"
                                   placeholder="City"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="client.address.state">State:</label>
                        <div class="col-sm-4">
                            <select class="form-control" id="client.address.state" name="client.address.state">
                                <option value="CA">California</option>
                                <option value="NV">Nevada</option>
                                <option value="WV">West Virginia</option>
                            </select>
                        </div>
                        <label class="col-sm-2  col-form-label" for="client.address.zip">ZipCode:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="client.address.zip" name="client.address.zip"
                                   placeholder="ZipCode" maxlength="5"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="client.person.first_name">First Name:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="client.person.first_name"
                                   name="client.person.first_name"
                                   placeholder="First Name"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="client.person.last_name">Last Name:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="client.person.last_name"
                                   name="client.person.last_name"
                                   placeholder="Last Name"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="client.person.email">Email:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="client.person.email" name="client.person.email"
                                   placeholder="Email Address"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="client.person.phone_number">Phone Number:</label>
                        <div class="col-sm-7">
                            <input type="tel" class="form-control" id="client.person.phone_number"
                                   name="client.person.phone_number"
                                   placeholder="Phone Number"/>
                        </div>
                        <div class="col-sm-1">
                            <i class="fas fa-clone"></i>
                            <i class="fas fa-share-square"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm card">
                <h2 class="card-header">Location Information</h2>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="input-group">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="Client's Company Name: Copied from Client field via React."
                                   placeholder="Client's company name"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="address.primary">Address:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="address.primary"
                                   name="address.primary"
                                   placeholder="Street Address"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="address.primary">Suite:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="address.secondary"
                                   name="address.secondary"
                                   placeholder="Suite/Building/Apartment"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="address.city">City:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="address.city" name="address.city"
                                   placeholder="City"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="address.state">State:</label>
                        <div class="col-sm-4">
                            <select class="form-control" id="address.state" name="address.state">
                                <option value="CA">California</option>
                                <option value="NV">Nevada</option>
                                <option value="WV">West Virginia</option>
                            </select>
                        </div>
                        <label class="col-sm-2  col-form-label" for="address.zip">ZipCode:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="address.zip" name="address.zip"
                                   placeholder="ZipCode" maxlength="5"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="person.first_name">First Name:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="person.first_name"
                                   name="person.first_name"
                                   placeholder="First Name"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="person.last_name">Last Name:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="person.last_name"
                                   name="person.last_name"
                                   placeholder="Last Name"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="person.email">Email:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="person.email" name="person.email"
                                   placeholder="Email Address"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="person.phone_number">Phone Number:</label>
                        <div class="col-sm-8">
                            <input type="tel" class="form-control" id="person.phone_number"
                                   name="person.phone_number"
                                   placeholder="Phone Number"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label for="intake"><h5>Intake:</h5></label><br>
            <textarea class="form-control" name="intake" id="intake" rows="4"></textarea>
        </div>

        <div class="row">
            <div class="col-sm card">
                <h2 class="card-header">Product Information</h2>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="input-group">
                            <label for="workorder.row" class="col-sm-2">Quick Add Sku:</label>
                            <div class="col-sm-4 input-group">
                                <input type="text" class="form-control" id="workorder.row" name="workorder.row"
                                       placeholder="10000"/>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="quickAddSku">
                                        <i class="text-primary fas fa-bolt"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-dark table-striped table-hover">
                        <thead class="thead-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Quantity</th>
                            <th>Name</th>
                            <th>Price $</th>
                            <th>Total $</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>0001</td>
                            <td>800</td>
                            <td>Shredding Magnetic Media</td>
                            <td align="right">6.00</td>
                            <td align="right">4800.00</td>
                        </tr>
                        <tr>
                            <td>
                                0002
                            </td>
                            <td>1</td>
                            <td>Certificate of Destruction</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                        </tr>
                        <tr>
                            <td>10023</td>
                            <td>12</td>
                            <td>Remstar Memory 8GB DDR-3 PC174</td>
                            <td align="right">12.00</td>
                            <td align="right">144.00</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-success">Add New WorkOrder</button>
    </form>
    </form>
@endsection
