<div class="row">
    <div class="col-12 mb-3">
        <a href="{{route('order.print',$order->id)}}" target="_blank" class="btn btn-default float-right"><i class="fas fa-print"></i> Print</a>
    </div>
</div>
@include('layouts.partials.invoice')
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
                        <option value="1" {{ (!empty($order->getUserOrderStatus()) && ($order->getUserOrderStatus())->status_id == 1)? 'selected' :'' }} >Pendding</option>
                        <option value="2" {{ (!empty($order->getUserOrderStatus()) && ($order->getUserOrderStatus())->status_id == 2)? 'selected' :'' }}>Paid</option>
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
                                <span><h5>{{$order->getSalesStage(!empty($order->orderStage['stage'])?$order->orderStage['stage']:0)}}</h5></span>
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
<div class="row">
    <form id="uploadPo" class="col-12" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="uploaded_by" value="{{auth()->user()->id}}">
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <fieldset class="border">
            <legend class="w-auto m-2">Uploaded Image</legend>
            @if(!empty($order->orderImage) && (count($order->orderImage) > 0))
                <div class="row">
                    @foreach ($order->orderImage as $orderImage)
                        @if(!empty($orderImage->product))
                        <div class="col-md-4 mt-3 mb-2 img-{{$orderImage->id}}">
                            <div class="d-flex justify-content-center remove">
                                <img src="{{url($orderImage->image)}}" class="img" alt="installation image" width="40%">
                            </div>
                            <div class="d-flex justify-content-center mt-2">
                                <button type="button" class="btn btn-danger btn-sm delete-installation-image" data-order-image="{{$orderImage->id}}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="row">
                    <div class="col-12 m-3 text-center">No Image Found</div>
                </div>
            @endif
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