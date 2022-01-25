<div class="row">
    <div class="col-12 col-sm-12">
        <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-view" data-tab="view" data-toggle="pill" href="#inquiry" role="tab" aria-controls="inquiry" aria-selected="true" >Inquiry</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-add" data-tab="add" data-toggle="pill" href="#inquiry_details" role="tab" aria-controls="inquiry_details" aria-selected="false" >Inquiry Details</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="tab-content">
                    <div class="tab-pane fade show active" id="inquiry" data-tab="view" role="tabpanel" aria-labelledby="tab-view">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td><strong>Customer Name</strong></td>
                                    <td>{{$inquiry->customer->name}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer Email</strong></td>
                                    <td>{{$inquiry->customer->email}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer Phone</strong></td>
                                    <td>{{$inquiry->customer->mobile}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Source</strong></td>
                                    <td>{{!(empty($inquiry->inquirySource)) ? $inquiry->inquirySource->source : 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Purpose / Comments</strong></td>
                                    <td>{{(!empty($inquiry->comments))?$inquiry->comments:'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dealer</strong></td>
                                    <td>{{(!empty($inquiry->dealer))?$inquiry->dealer->name:'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Retailer</strong></td>
                                    <td>{{(!empty($inquiry->retailer))?$inquiry->retailer->name:'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created By</strong></td>
                                    <td>{{(!empty($inquiry->createdBy))?$inquiry->createdBy->name:'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>{{(!empty($inquiry->getInquirystatusName()))?$inquiry->getInquirystatusName():'N/A'}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Product</strong></td>
                                    <td>{{$inquiry_products}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created At</strong></td>
                                    <td>{{$inquiry->created_at}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated At</strong></td>
                                    <td>{{$inquiry->updated_at}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="inquiry_details" data-tab="add" role="tabpanel" aria-labelledby="tab-add">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Domestic</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Private Bunglows</strong></td>
                                            <td>@if($inquiry->application_type == 'Private Bunglows') Y @endif()</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Appartments</strong></td>
                                            <td>@if($inquiry->application_type == 'Appartments') Y @endif()</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Common Society</strong></td>
                                            <td>@if($inquiry->application_type == 'Common Society') Y @endif()</td>
                                        </tr>
                                        <tr>
                                            <td><strong>RO System</strong></td>
                                            <td>@if($inquiry->application_type == 'RO System') Y @endif()</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Others</strong></td>
                                            <td>@if($inquiry->application_type == 'Others') Y @endif()</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Industrial</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Cooling Tower</strong></td>
                                            <td>@if($inquiry->application_type == 'Cooling Tower') Y @endif()</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Boiler</strong></td>
                                            <td>@if($inquiry->application_type == 'Boiler') Y @endif()</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Common Utility</strong></td>
                                            <td>@if($inquiry->application_type == 'Common Utility') Y @endif()</td>
                                        </tr>
                                        <tr>
                                            <td><strong>RO System</strong></td>
                                            <td>@if($inquiry->application_type == 'RO System') Y @endif()</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Others</strong></td>
                                            <td>@if($inquiry->application_type == 'Others') Y @endif()</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Pumps Design & Pipe Size</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Pump Capacity</strong></td>
                                            <td>HP-{{$inquiry->pump_capacity}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Pressure Pump</strong><br/>
                                            {{$inquiry->pressure_pump}}
                                            </td>
                                            <td><strong>Submersible Pump</strong><br/>{{$inquiry->submersible_pump}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Water Flow L/H</strong></td>
                                            <td>{{$inquiry->water_flow}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Other (Gravity) </strong></td>
                                            <td>{{$inquiry->other_gravity}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Pipe Diameters</strong></td>
                                            <td>{{$inquiry->pipe_diameters}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Water Parameters</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>TDS</strong></td>
                                            <td>{{$inquiry->tds}} ppm</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Hardness</strong></td>
                                            <td>{{$inquiry->hardness}} ppm</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><strong>Water Type</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bore well Water </strong></td>
                                            <td>{{$inquiry->bore_well_water}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Soft Water</strong></td>
                                            <td>{{$inquiry->soft_water}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Others</strong></td>
                                            <td>{{$inquiry->others_water_type}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
