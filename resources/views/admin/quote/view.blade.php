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
            <strong>{{ucwords($order->addedBy->name)}}</strong><br>
            @if(!empty($order->addedBy->city))
            {{ucwords($order->addedBy->address)}},<br>
            {{ucwords($order->addedBy->city->name)}},<br>
            {{ucwords($order->addedBy->state->name)}} - {{$order->addedBy->zipcode}}<br>
            @endif
            @if(!empty($order->addedBy->mobile))
            Phone: {{$order->addedBy->mobile}}<br>
            @endif
            Email: {{$order->addedBy->email}}
        </address>
    </div>
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
        To
        <address>
            <strong>{{ucwords($order->customer->name)}}</strong><br>
            {{ucwords($order->customer->address)}},<br>
            {{ucwords($order->customer->city->name)}},<br>
            {{ucwords($order->customer->state->name)}} - {{$order->customer->zipcode}}<br>
            Phone: {{$order->customer->mobile}}<br>
            Email: {{$order->customer->email}}
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
                    <th style="width: 70%">Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderProduct as $orderProduct)
                <tr>
                    <td>{{(!empty($orderProduct->product_name))?$orderProduct->product_name:$orderProduct->product->name}}</td>
                    <td>{{ number_format($orderProduct->amount,2)}}</td>
                    <td>{{$orderProduct->qty}}</td>
                    <td>{{ number_format($orderProduct->getsubTotal(),2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2"></td>
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
    </form>
</div>
@endif