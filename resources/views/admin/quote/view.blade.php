<div class="row">
    <div class="col-sm-12 col-md-12 mb-3 border-bottom">
        <div class="">
            <h5 class="border-bottom"><strong>Customer Detail</strong></h5>
            <ul class="list-unstyled">
                <li><strong>{{ucwords($order->customer->name)}}</strong></li>
                <li><strong>{{ucwords($order->customer->address)}},</strong></li>
                <li><strong>{{ucwords($order->customer->city->name)}},</strong></li>
                <li><strong>{{ucwords($order->customer->state->name)}} - {{$order->customer->zipcode}}</strong></li>
                <li><strong>Mobile: {{$order->customer->mobile}}</strong></li>
                <li><strong>Email: {{$order->customer->email}}</strong></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-12">
    <h5><strong>Order Detail</strong></h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 70%">Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderProduct as $orderProduct)
                <tr>
                    <td>{{$orderProduct->product->name}}</td>
                    <td>{{$orderProduct->qty}}</td>
                    <td>{{ number_format($orderProduct->amount,2)}}</td>
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
                        <input type="checkbox" class="mr-3 product" name="is_po_recieved" checked>
                        <label>Recieved</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label for="recieved_date">Recieved Date</label>
                    <input type="text" class="mr-3 product dateimepicker-input form-control col-md-6" name="recieved_date" id="datetimepicker" data-toggle="datetimepicker" data-target="#datetimepicker" value="" autocomplete="off" required />
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
