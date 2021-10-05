@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">{{$action_title}} {{ ucwords($title) }}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">     
        @if(!empty($inquiry))
            <form method="POST" action="{{ route('inquiry.update',$inquiry->id) }}">
            @method('PUT')
            <input type="hidden" name="updated_by" value="{{auth()->user()->id}}">
        @else
            <form method="POST" action="{{ route('inquiry.index') }}">
            <input type="hidden" name="created_by" value="{{auth()->user()->id}}">
        @endif
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($inquiry))?$inquiry->id:'' }}">
        

        <div class="row">
        <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Inquiry Source <span class="required_field">*</span></label>
                    <select class="form-control select2 inquiry_source" id='inquiry_source' name='inquiry_source' tabindex="1">
                        <option value="">Select</option>
                        @foreach ($inquiries_source as $inquiry_source)
                            <option value="{{$inquiry_source->id}}" {{ (!empty($inquiry) && $inquiry->inquiries_source_id == $inquiry_source->id) ? 'selected' : ''}}> {{$inquiry_source->source}} </option>
                        @endforeach
                    </select>
                    @error('inquiry_source')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                        <label>Customer <span class="required_field">*</span></label>
                        <div class="input-group input-group-sm">
                        <select class="form-control select2 customer" id='customer' name='customer' tabindex="2">
                            <option value="">Select</option>
                            @if(!empty($inquiry) && !empty($inquiry->customer_id))
                                <option value="{{$inquiry->customer_id}}" selected>{{$inquiry->customer->name.' - ' .$inquiry->customer->zipcode.' - '. $inquiry->customer->city->name  }}</option>
                            @endif
                        </select>
                        <span class="input-group-append">
                            <!-- <button type="button" id="addCustomer" class="btn btn-primary btn-flat addCustomer">Add</button> -->
                            <button id="addCustomer" class="btn btn-primary btn-flat addCustomer" data-toggle='modal' data-target='#modal-lg'>Add</button>
                        </span>
                    </div>
                    @error('customer')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Product</label>
                    <select class="form-control select2 product" multiple="multiple" id='product' name='product[]' tabindex="3">
                        <option value="">Select</option>
                        @if(!empty($inquiry) && $inquiry->product)
                            @foreach ($inquiry->product as $product)
                                <option value="{{$product->id}}" selected>{{$product->name}}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('product')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @admin()
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Dealer</label>
                    <select class="form-control select2 dealer" id='dealer' name='dealer' tabindex="4">
                        <option value="">Select</option>
                        @if(!empty($inquiry) && !empty($inquiry->dealer_id))
                            <option value="{{$inquiry->dealer_id}}" selected>{{$inquiry->dealer->name.' - ' .$inquiry->dealer->zipcode.' - '. $inquiry->dealer->city->name}}</option>
                        @endif
                    </select>
                    @error('dealer')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @endadmin
            @dealer()
            @if(auth()->user()->users_group_id == 2)
                <input type="hidden" id='parent_user' value="{{ auth()->user()->id }}">
            @endif()
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Retailer</label>
                    <select class="form-control select2 retailer" id='retailer' name='retailer' tabindex="5">
                        <option value="">Select</option>
                        @if(!empty($inquiry) && !empty($inquiry->retailer_id))
                            <option value="{{$inquiry->retailer_id}}" selected>{{ $inquiry->retailer->name.' - ' .$inquiry->retailer->zipcode.' - '. $inquiry->retailer->city->name }}</option>
                            <input type="hidden" id="selected_retailer" value="{{$inquiry->retailer_id}}">
                        @endif

                    </select>
                    @admin()
                        <label><span class="text-red"><strong>Note:</strong> Please select the dealer before select the retailer.</span></label>
                    @endadmin
                    @error('retailer')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @enddealer
            @admin()
                <div class="col-sm-12 col-md-12">
                    <!-- text input -->
                    <div class="form-group">
                    <input type="checkbox" class="mb-4 send_mail_customer" name="send_mail_to_customer" id="send_mail_customer" />
                    <label>Send dealer/retailer details to customer?</label>
                    </div>
                </div>
            @endadmin
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" tabindex="6">Submit</button>
            </div>
        </div>
    </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- modal -->
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Customer</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <div class="card card-primary card-outline">
                            <div class="card-body" id="customerBody">
                                
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection
@section('plugins.Select2', true)
@section('js')
<script>
jQuery(document).ready(function(){
    $('#inquiry_source').select2();
    $('.dealer').select2({
        placeholder: "Select",
        minimumInputLength: 2,
        allowClear: true,
        ajax: {
            url: "{{route('get_value')}}",
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term, // search term
                    table:'users',
                    fields: "users.id, CONCAT(`users`.`name`, ' - ', `zipcode`,' - ',`cities`.`name`) as text",
                    where:`(users_group_id = 2) AND deleted_at IS NULL `,
                    whereOr: ` OR zipcode like '%${params.term}%' OR cities.name like '%${params.term}%' OR states.name like '%${params.term}%'`,
                    joins:[{'join_table':'cities','to_column':'city_id','from_column':'cities.id','type':'left'},{'join_table':'states','to_column':'users.state_id','from_column':'states.id','type':'left'}]
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    });
    $('.customer').select2({
        placeholder: "Select",
        minimumInputLength: 2,
        allowClear: true,
        ajax: {
            url: "{{route('get_value')}}",
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term, // search term
                    table:'users',
                    fields: "users.id, CONCAT(`users`.`name`, ' - ', `zipcode`,' - ',`cities`.`name`) as text",
                    // where: ` OR zipcode like '%${params.term}%' OR cities.name like '%${params.term}%' AND (users_group_id=4 OR users_group_id=5)`,
                    whereOr: ` OR (zipcode like '%${params.term}%' OR cities.name like '%${params.term}%')`,
                    whereAnd: `AND (users_group_id=4 OR users_group_id=5) AND created_by=`+"{{auth()->user()->id}} AND deleted_at IS NULL",
                    joins:[{'join_table':'cities','to_column':'city_id','from_column':'cities.id','type':'left'}]
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    });
    $('.product').select2({
        placeholder: "Select",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "{{route('get_value')}}",
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term, // search term
                    table:'products',
                    where: 'deleted_at is NULL'
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    });
    $('.retailer').select2({
        placeholder: "Select",
        allowClear: true,
    });
    $('.dealer').on('change',function(){
        getRetailers($(this).val())
    });

    $('#addCustomer').on('click',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        // var data = $('#saveInquiryLog').serialize();        
        $.ajax({
            url: "{{ route('customerCreateAjax') }}",
            type: 'GET',            
            success: function (data) {                
                $("#customerBody").html(data);
                $("#modal-lg").modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("Eror");
                    /* Toast.fire({
                        type: 'error',
                        title: JSON.parse(jqXHR.responseText).message
                    }); */
            }
        });
    });

    if($('#parent_user').val()){
        getRetailers($('#parent_user').val());
    }
    if($('#dealer').val()){
        getRetailers($('#dealer').val());
    }
    
    // ratiler
    function getRetailers(param){
        
       return $.ajax({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') },
            url: "{{ route('get_value') }}",
            type: 'POST',          
            data: {
                    // q: param, // search term
                    table:'users',
                    fields: "users.id, CONCAT(`users`.`name`, ' - ', `zipcode`,' - ',`cities`.`name`) as text",
                    where:` (users_group_id = 3) AND (parent_id = ${param}) AND deleted_at IS NULL`,
                    joins:[{'join_table':'cities','to_column':'city_id','from_column':'cities.id','type':'left'}]
            },            
            success: function (data) { 
                data = JSON.parse(data);
                var newOption = new Option();
                let selected_reatiler = $('#retailer').val();
                $('#retailer').empty();
                var selected_option = '';
                newOption = new Option('Select','' , false, false);
                $('#retailer').append(newOption).trigger('change');
                if($('#parent_user').val()){
                    selected_option = $('#selected_retailer').val();
                }else if($('#dealer').val()){
                    selected_option = selected_reatiler;
                }
                var selected = false;
                $.each(data,function(key,val){
                    selected = (selected_option == val.id)? true : false ;
                    newOption = new Option(val.text, val.id, selected, selected);
                    $('#retailer').append(newOption);
                 });
                 $('#retailer').select2().trigger('change');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var err = JSON.parse(jqXHR.responseText).errors;
                $.each(err,function(key,val){                
                    $(`[name=${key}]`).closest('.form-group').after(`<div class="alert alert-danger mt-2">${val}</div>`);                                        
                });
                    /* Toast.fire({
                        type: 'error',
                        title: JSON.parse(jqXHR.responseText).message
                    }); */
            }
        });
    }
});
</script>
@endsection
