<div class="row">
    <div class="col-12">
        <h4>
            <!-- <i class="fas fa-globe"></i> {{config('app.name')}} -->
            <small class="float-right">Date: {{date('d/m/Y',strtotime($order->created_at))}}</small>
        </h4>
    </div>
    <!-- /.col -->
</div>
<!-- info row -->
<div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
        From
        <address>
            @if(!empty($order->addedBy))
                <strong>{{ucwords($order->addedBy->name)}}</strong><br>
                @if(!empty($order->addedBy->city))
                {{ucwords($order->addedBy->address)}},<br>
                {{ucwords($order->addedBy->city->name)}},<br>
                {{ucwords($order->addedBy->state->name)}} - {{$order->addedBy->zipcode}}<br>
                @endif
                @if(!empty($order->addedBy->mobile))
                Phone: {{$order->addedBy->mobile}}<br>
                @endif
                Email: {{$order->addedBy->email}}<br>
                GSTIN: {{ ((!empty($to_user->gstin))?$to_user->gstin:' N/A ') }}
            @else
                <strong>{{ucwords($setting['name'])}}</strong><br>
                {{ucwords($setting['address'])}},<br>
                {{ucwords($setting['city_name'])}},<br>
                {{ucwords($setting['state_name'])}} - {{$setting['zipcode']}}<br>
                Phone: {{$setting['mobile']}}<br>
                Email: {{$setting['email']}}<br>
                GSTIN: {{ ((!empty($setting['gstin']))?$setting['gstin']:' N/A ') }}
            @endif
        </address>
    </div>
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
        To
        <address>
        @if(!empty($order->customer))
            <strong>{{ucwords($order->customer->name)}}</strong><br>
            {{ucwords($order->customer->address)}},<br>
            {{ucwords($order->customer->city->name)}},<br>
            {{ucwords($order->customer->state->name)}} - {{$order->customer->zipcode}}<br>
            Phone: {{$order->customer->mobile}}<br>
            Email: {{$order->customer->email}}<br>
            GSTIN: {{ ((!empty($to_user->gstin))?$to_user->gstin:' N/A ') }}
        @else
            <strong>{{ucwords($setting['name'])}}</strong><br>
            {{ucwords($setting['address'])}},<br>
            {{ucwords($setting['city_name'])}},<br>
            {{ucwords($setting['state_name'])}} - {{$setting['zipcode']}}<br>
            Phone: {{$setting['mobile']}}<br>
            Email: {{$setting['email']}}<br>
            GSTIN: {{ ((!empty($setting['gstin']))?$setting['gstin']:' N/A ') }}

        @endif
        </address>
    </div>
    @if ($order->type == 'sale')
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
        <b>Invoice #{{$order->order_number}}</b><br>
        <br>
    </div>
    <!-- /.col -->
    @endif
</div>
<div class="row">
    <div class="col-sm-12 col-md-12">
        <h5><strong>Order Detail</strong></h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 60%;">Product</th>
                    <th class="text-center">HSN/SAC</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderProduct as $orderProduct)
                <tr>
                    <td style="width: 60%;">{{(!empty($orderProduct->product_name))?$orderProduct->product_name:$orderProduct->product->name}}</td>
                    <td class="text-center">{{ (!empty($orderProduct->product->hsn_sac)) ? $orderProduct->product->hsn_sac : '-' }}</td>
                    <td>{{ number_format($orderProduct->amount,2)}}</td>
                    <td>{{$orderProduct->qty}}</td>
                    <td>{{ number_format($orderProduct->getsubTotal(),2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3"></td>
                    <td><strong>Sub Total</strong></td>
                    <td>{{ number_format($order->getSubTotal()['sub_total'],2) }}</td>
                </tr>
                @foreach ($order->getTotalTax() as $tax)
                <tr>
                    <td colspan="3"></td>
                    <td><strong>{{$tax->name}}</strong></td>
                    <td>{{ number_format($tax->total_tax,2) }}</td>
                </tr>    
                @endforeach
                <tr>
                    <td colspan="3"></td>
                    <td><strong>Total</strong></td>
                    <td>{{ number_format($order->total,2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@if($order->type == 'quote')
<div class="row">
    <form id="uploadPo" class="col-12" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="uploaded_by" value="{{auth()->user()->id}}">
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <fieldset class="border">
            <legend class="w-auto m-2">PO Recieved</legend>
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group clearfix">
                    <div class="icheck-primary d-inline">
                        <input type="checkbox" class="mr-3 product" name="is_po_recieved" {{((!(empty($order)) && (!empty($order->is_po_recieved)))) ? 'checked' : ''}}>
                        <label>Recieved</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label for="recieved_date">Recieved Date</label>
                    <input type="text" class="mr-3 product dateimepicker-input form-control col-md-6" name="recieved_date" id="datetimepicker" data-toggle="datetimepicker" data-target="#datetimepicker" value="{{((!(empty($order)) && (!empty($order->recieved_date)))) ? $order->recieved_date : ''}}" autocomplete="off" required />
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <div class="form-group col-md-6">
                    <label for="exampleInputFile">Image</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="hidden" name="images[]" id="uploadedImage" value="" />
                            <input type="file" class="custom-file-input" name="image" id="exampleInputFile" />
                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                        </div>
                        <div class="input-group-append">
                            <span class="input-group-text" id="imageUpload">Upload</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12" id="loader" style="display: none;">
                <div class="loading text-center"><i class="fas fa-circle-notch fa-spin fa-2x text-secondary"></i></div>
            </div>
            <div class="col-sm-12 col-md-12" id="uploadedPo">
            </div>
            <div class="col-sm-12 mt-2 mb-2">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </fieldset>
    </form>
</div>
@endif
@if($order->type == 'sale')
<div class="row">
    <form id="invoice" class="col-12" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="user_id" value="{{auth()->user()->id}}">
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <fieldset class="border">
            <legend class="w-auto m-2">Invoice Status</legend>
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group clearfix">
                    <label for="status">Status: </label>
                    <select name="status_id" class="form-control select2" id="order_status">
                        <option value="">Select</option>
                        <option value="1" {{ (!empty($order->getUserOrderStatus()) && (count($order->getUserOrderStatus()) > 0 ) && $order->getUserOrderStatus()[0]->status_id == 1)? 'selected' :'' }} >Pendding</option>
                        <option value="2" {{ (!empty($order->getUserOrderStatus()) && (count($order->getUserOrderStatus()) > 0 ) && $order->getUserOrderStatus()[0]->status_id == 2)? 'selected' :'' }}>Paid</option>
                    </select>
                </div>
            </div>
        </fieldset>
        <fieldset class="border">
            <legend class="w-auto m-2">Order Stage</legend>
                <div class="col-sm-12 col-md-12">
                    <table class="col-12 mb-3">
                        @if(($order->from_user != auth()->user()->id) && (!empty($order->from_user)))
                        <tr>
                            <td>
                                <label for="stage">Stage: </label>
                            </td>
                            <td>
                                <span>{{$order->getSalesStage(!empty($order->orderStage['stage'])?$order->orderStage['stage']:0)}}</span>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td>
                                <label for="stage">Stage: </label>
                            </td>
                            <td>
                                <select name="stage" class="form-control select2" id="order_stage">
                                    <option value="">Select</option>
                                    @foreach ($order->getOrderStage() as $key=>$order_stage)
                                        <option value="{{$key}}" {{(!empty($order->orderStage) && ($order->orderStage->stage == $key))?'selected':''}}>{{$order_stage}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @if(!empty($order->orderStage) && ($order->orderStage->stage == 2))
                        <tr>
                            <td>
                                <label for="link">Upload Link:</label>
                            </td>
                            <td>
                                <input type="text" class=" d-inline col-6 form-control col-4" id="link" value="{{route('installation.login',Crypt::encryptString($order->id))}}" readonly /><button type="button" class="d-inline btn btn-default btn-sm" onclick="copyToClipboard('#link')"><i class="far fa-copy"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="password">Password:</label>
                            </td>
                            <td>
                                <div class="col-sm-12 col-md-4 ml-0 pl-0">
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="password" name="password" value="{{(!empty($order->token))?$order->token:''}}" placeholder="Password">
                                        <span toggle="#password" class="fa fa-fw fa-eye eye-icon toggle-password"></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button type="button" id="btnUpdate" class="btn btn-primary btn-sm">Update Password</button>
                            </td>
                        </tr>
                        @endif
                        @endnotadmin
                    </table>
                </div>
        </fieldset>
    </form>
</div>
@endif
<script>
        if($('.toggle-password').length > 0){
        $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    }
</script>