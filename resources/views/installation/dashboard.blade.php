@extends('layouts.adminlte')
@section('title')
{{$title}}
@endsection
@section('content')
<div class="row">
    <div class="col-sm-12 invoice-col">
        <b>Invoice #{{$order->order_number}}</b><br>
    </div>
    <div class="col-sm-12 invoice-col">
        Customer
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
</div>
<div class="row">
    <div class="col-sm-12 col-md-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 60%;">Product</th>
                </tr>
            </thead>
            <tbody>
            <form id="uploadImage" class="col-12" method="POST" action="{{route('installation.image.upload')}}" enctype="multipart/form-data">
            @csrf
                @foreach ($order->orderProduct as $orderProduct)
                <tr>
                    <td style="width: 60%;">{{(!empty($orderProduct->product_name))?$orderProduct->product_name:$orderProduct->product->name}}</td>
                    <td>
                        <!-- <span class="btn btn-success btn-sm fileinput-button dz-clickable" data-order-product="{{$orderProduct->product->id}}">
                            <i class="fas fa-upload"></i> Upload
                        </span> -->
                        <div class="input-group col-12">
                            <div class="custom-file">
                                <input type="hidden" name="images[]" id="uploadedImage" value="" />
                                <input type="file" class="custom-file-input order-image-{{$orderProduct->product->id}}"  name="image" id="exampleInputFile" />
                                <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                            </div>
                            <div class="input-group-append">
                                <span class="input-group-text btn fileinput-button" data-order-product="{{$orderProduct->product->id}}" id="imageUpload">Upload</span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        @if(!empty($order->orderImage))
                        <div class="row">
                            @foreach ($order->orderImage as $orderImage)
                            @if(!empty($orderImage->product) && ($orderImage->product->id == $orderProduct->product->id))
                            <div class="col-md-4 mt-3 mb-2 img-{{$orderImage->id}}">
                                <div class="d-flex justify-content-center remove">
                                    <img src="{{url($orderImage->image)}}" class="img" alt="installation image" width="40%">
                                </div>
                                <div class="d-flex justify-content-center mt-2">
                                    <button type="button" class="btn btn-danger btn-sm delete" data-order-image="{{$orderImage->id}}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </form>
            </tbody>
        </table>
    </div>
</div>
@stop
@section('plugins.dropzone', true)
@section('js')
<script>
    jQuery(document).ready(function() {

        /* var myDropzone = new Dropzone(document.body, {
        url: "/target-url",
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        previewTemplate: '',
        disablePreviews:true,
        autoQueue: false,
        clickable: ".fileinput-button", 
        });

        myDropzone.on("addedfile", function(file) {
            console.log($(this).find('.dz-clickable'));
        }); */

        $('body').on('click','.fileinput-button',function(){
            let url = "{{route('installation.image.upload')}}";
            let formData = new FormData($('#uploadImage')[0]);
            let order_product = $(this).data('order-product');
            let file = $('input[type=file]')[0].files[0];
            formData.append('product_id',$(this).data('order-product'));
            formData.append('file', file, file.name);
            saveFormData(url,formData).then(function(res){
                
            });
        });
        
        $('body').on('click','.delete',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            let data = confirm("Are you sure? you wants to delete this image?");

            if(data){
                // $(`.img-${$(this).data('order-image')}`).remove();
                let order_image = $(this).data('order-image');
                removeImage("{{route('installation.image.delete')}}",{order_image_id:$(this).data('order-image')}).then(function(res){
                    console.log(res.code);
                    if(res.code == "1"){
                        $(`.img-${order_image}`).remove();
                    }
                });
            }
        });
    });

    function removeImage(url,formData,modal=''){
        return $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                beforeSend: function(){
                    $('#overlay').show();
                    $('.btn-save').attr('disabled',true);
                },
                complete: function(){
                    $('#overlay').hide();
                },
                success: function (data) {
                    if(data.code == 1){
                        Toast.fire({
                            type: 'success',
                            title: data.message
                        });
                        if(modal !== ''){
                            $(`#${modal}`).modal('hide');
                        }
                    }else{
                        Toast.fire({
                            type: 'error',
                            title: data.message
                        })
                    }
                },
                completed:function(){
                    $('#overlay').hide();
                    $('.btn-save').attr('disabled',false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.btn-save').attr('disabled',false);
                    var err = JSON.parse(jqXHR.responseText).errors;
                    $.each(err,function(key,val){
                        if($.isArray(val)){
                            $.each(val,function(k,message){
                                Toast.fire({
                                    type: 'error',
                                    title: message
                                });        
                            });
                        }
                        
                    });
                }
            });
    }

    function saveFormData(url,formData,modal=''){
            const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                        });
            return $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                contentType: false,
                processData: false,
                beforeSend: function(){
                    $('#overlay').show();
                    $('.btn-save').attr('disabled',true);
                },
                complete: function(){
                    $('#overlay').hide();
                },
                success: function (data) {
                    if(data.code == 1){
                        Toast.fire({
                            type: 'success',
                            title: data.message
                        });
                        if(modal !== ''){
                            $(`#${modal}`).modal('hide');
                        }
                    }else{
                        Toast.fire({
                            type: 'error',
                            title: data.message
                        })
                    }
                },
                completed:function(){
                    $('#overlay').hide();
                    $('.btn-save').attr('disabled',false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.btn-save').attr('disabled',false);
                    var err = JSON.parse(jqXHR.responseText).errors;
                    $.each(err,function(key,val){
                        if($.isArray(val)){
                            $.each(val,function(k,message){
                                Toast.fire({
                                    type: 'error',
                                    title: message
                                });        
                            });
                        }
                        
                    });
                }
            });
    }
</script>
@endsection