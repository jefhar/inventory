@extends('layouts.app')

@section('content')
  <div id="workorders_create"></div>
@endsection

@section('future_workorder_content')
  <form class="container">
    <div class="row">
      <div class="card">
        <h2 class="card-header">Product Information</h2>
        <div class="card-body">
          <div class="form-group row">
            <div class="input-group">
              <label for="workorder.row" class="col-sm-2">Quick Add Sku:</label>
              <div class="col-sm-4 input-group">
                <input type="text" class="form-control form-control-sm" id="workorder.row"
                       name="workorder.row"
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
      </div>
    </div>
  </form>
@endsection
