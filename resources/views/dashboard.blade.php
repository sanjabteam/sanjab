@extends('sanjab::master')

@section('title', 'dashboard')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="sanjab_app">
                <material-card title="test" description="tttt">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-warning">
                                <th>
                                    ID
                                </th>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Country
                                </th>
                                <th>
                                    City
                                </th>
                                <th>
                                    Salary
                                </th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        1
                                    </td>
                                    <td>
                                        Dakota Rice
                                    </td>
                                    <td>
                                        Niger
                                    </td>
                                    <td>
                                        Oud-Turnhout
                                    </td>
                                    <td
                                        class="text-primary">
                                        $36,738
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        2
                                    </td>
                                    <td>
                                        Minerva Hooper
                                    </td>
                                    <td>
                                        Curaçao
                                    </td>
                                    <td>
                                        Sinaai-Waas
                                    </td>
                                    <td
                                        class="text-primary">
                                        $23,789
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        3
                                    </td>
                                    <td>
                                        Sage Rodriguez
                                    </td>
                                    <td>
                                        Netherlands
                                    </td>
                                    <td>
                                        Baileux
                                    </td>
                                    <td
                                        class="text-primary">
                                        $56,142
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        4
                                    </td>
                                    <td>
                                        Philip Chaney
                                    </td>
                                    <td>
                                        Korea, South
                                    </td>
                                    <td>
                                        Overland Park
                                    </td>
                                    <td
                                        class="text-primary">
                                        $38,735
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        5
                                    </td>
                                    <td>
                                        Doris Greene
                                    </td>
                                    <td>
                                        Malawi
                                    </td>
                                    <td>
                                        Feldkirchen in
                                        Kärnten
                                    </td>
                                    <td
                                        class="text-primary">
                                        $63,542
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        6
                                    </td>
                                    <td>
                                        Mason Porter
                                    </td>
                                    <td>
                                        Chile
                                    </td>
                                    <td>
                                        Gloucester
                                    </td>
                                    <td
                                        class="text-primary">
                                        $78,615
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <form>
                        <div class="row">
                          <div class="col-md-5">
                            <div class="form-group bmd-form-group float-label-form-group">
                              <label class="bmd-label-floating">Company (disabled)</label>
                              <input type="text" class="form-control" disabled>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group bmd-form-group float-label-form-group">
                              <label class="bmd-label-floating">Username</label>
                              <input type="text" class="form-control">
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group float-label-form-group">
                              <label class="bmd-label-floating">Email address</label>
                              <input type="email" class="form-control">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group float-label-form-group">
                              <label class="bmd-label-floating">Fist Name</label>
                              <input type="text" class="form-control">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group float-label-form-group">
                              <label class="bmd-label-floating">Last Name</label>
                              <input type="text" class="form-control">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group float-label-form-group">
                              <label class="bmd-label-floating">Adress</label>
                              <input type="text" class="form-control">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group float-label-form-group">
                              <label class="form-group float-label-form-group">City</label>
                              <input type="text" class="form-control">
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group float-label-form-group">
                              <label class="bmd-label-floating">Country</label>
                              <input type="text" class="form-control">
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group float-label-form-group">
                              <label class="bmd-label-floating">Postal Code</label>
                              <input type="text" class="form-control">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group float-label-form-group">
                              <label class="bmd-label-floating">About Me</label>
                              <div class="form-group float-label-form-group">
                                <textarea class="form-control" rows="5"></textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">Update Profile</button>
                        <div class="clearfix"></div>
                      </form>

                </material-card>
            </div>
        </div>
    </div>
@endsection

