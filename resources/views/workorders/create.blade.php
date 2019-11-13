@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 style="text-align: center;">Work Order Estimate</h1>
        <form class="workorder" style="-webkit-box-shadow: 4px 4px 4px 0px rgba(64,64,64,0.55);
            -moz-box-shadow: 4px 4px 4px 0px rgba(64,64,64,0.55);
            box-shadow: 4px 4px 4px 0px rgba(64,64,64,0.55);
            padding: 12px;">
            <input type="hidden" value="0" name="user.id"/>
            <div class="clientBox" style="display:flex; justify-content: space-between;">
                <div class="card customerBox">
                    <div class="row">
                        <label for="client.company_name">Company Name:
                            <input type="text" required id="client.company_name"/>
                        </label>
                    </div>
                    <div class="row">
                        <label for="client.first_name">First:
                            <input type="text" required id="client.first_name" size="16"/>
                        </label>
                        <label for="client.last_name">Last:
                            <input type="text" required id="client.last_name" size="16"/>
                        </label>
                    </div>
                    <div class="row">
                        <label for="client.email">email:
                            <input type="email" size="16" id="client.email"/>
                        </label>
                        <label for="client.phone">Phone:
                            <input type="tel" size="16" id="client.phone"/>
                        </label>
                    </div>
                    <div class="row">
                        <label for="client.address.primary">Address 1:
                            <input type="text" required id="client.address.primary"/>
                        </label>
                    </div>
                    <div class="row">
                        <label for="client.address.secondary">Address 2:
                            <input type="text" id="client.address.secondary"/>
                        </label>
                    </div>
                    <div class="row">
                        <label for="client.address.city">City:
                            <input type="text" required id="client.address.city" size="12"/>
                        </label>
                        <label for="client.address.state">State:
                            <select id="client.address.state">
                                <option value="CA">CA</option>
                                <option value="NE">NE</option>
                                <option value="TX">TX</option>
                            </select>
                        </label>
                        <label for="client.address.zip">ZipCode:
                            <input type="text" required id="client.address.zip" size="5" maxlength="5"/></label>
                    </div>
                </div>
                <button onclick="javascript:alert('I would copy the information on the left to the fields on the right.');">
                    Copy
                    Address =&gt;
                </button>
                <div class="card shipBox" style="border: 1px solid red;">

                    <div class="row">
                        <label for="workorder.first_name">First:
                            <input type="text" id="workorder.first_name"/>
                        </label>
                        <label for="workorder.last_name">Last:
                            <input type="text" id="workorder.last_name"/>
                        </label>
                    </div>
                    <div class="row">
                        <label for="workorder.email">email:
                            <input type="email" id="workorder.email"/>
                        </label>
                        <label for="workorder.phone">Phone:
                            <input type="tel" id="workorder.phone"/>
                        </label>
                    </div>
                    <div class="row">
                        <label for="workorder.address.primary">Address 1:
                            <input type="text" id="workorder.address.primary"/>
                        </label>
                    </div>
                    <div class="row">
                        <label for="workorder.address.secondary">Address 2:
                            <input type="text" id="workorder.address.secondary"/>
                        </label>
                    </div>
                    <div class="row">
                        <label for="workorder.address.city">City:
                            <input type="text" id="workorder.address.city"/>
                        </label>
                        <label for="workorder.address.state">State:
                            <select id="workorder.address.state">
                                <option value="CA">CA</option>
                                <option value="NE">NE</option>
                                <option value="TX">TX</option>
                            </select>
                        </label>
                        <label for="workorder.address.zip">Zip:
                            <input type="text" id="workorder.address.zip"/>
                        </label>
                    </div>

                </div>
            </div>
            <label>Intake:<br/>
                <textarea name="workorder.intake" cols="80" rows="4"></textarea>
            </label>
            <div>
                <hr>
                <label for="workorder.row">
                    Quick Add SKU:
                    <input type="text" id="workorder.row" onblur="javascript:(alert('pretending to add a row'));">
                </label>
                <table border="1">
                    <thead>
                    <tr>
                        <td>SKU</td>
                        <td>Quantity</td>
                        <td>Name</td>
                        <td>Price $</td>
                        <td>Total $</td>
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
            <button style="color: green; font-size: 16px; padding: 12px; margin: 8px;">Add New WorkOrder</button>
        </form>
    </div>
@endsection
