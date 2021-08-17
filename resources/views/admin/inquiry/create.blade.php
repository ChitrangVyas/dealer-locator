@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">{{ ucwords($title) }}</h3>
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
                    <label>Inquiry Source</label>
                    <select class="form-control select2 inquiry_source" id='inquiry_source' name='inquiry_source'>
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
                        <label>Customer</label>
                        <div class="input-group input-group-sm">
                        <select class="form-control select2 customer" id='customer' name='customer'>
                            <option value="">Select</option>
                            @if(!empty($inquiry) && !empty($inquiry->customer_id))
                                <option value="{{$inquiry->customer_id}}" selected>{{$inquiry->customer->name}}</option>
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
                    <select class="form-control select2 product" id='product' name='product'>
                        <option value="">Select</option>
                        @if(!empty($inquiry) && $inquiry->product_id)
                            <option value="{{$inquiry->product_id}}" selected>{{$inquiry->product->name}}</option>
                        @endif
                    </select>
                    @error('state')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Dealer</label>
                    <select class="form-control select2 dealer" id='dealer' name='dealer'>
                        <option value="">Select</option>
                        @if(!empty($inquiry) && !empty($inquiry->dealer_id))
                            <option value="{{$inquiry->dealer_id}}" selected>{{$inquiry->dealer->name}}</option>
                        @endif
                    </select>
                    @error('dealer')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary">Submit</button>
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
    var base_url = "{{url('/admin')}}";
    $('.dealer').select2({
        minimumInputLength: 2,
        ajax: {
            url: `${base_url}/get-value`,
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term, // search term
                    table:'users',
                    fields: "users.id, CONCAT(`users`.`name`, ' - ', `zipcode`,' - ',`cities`.`name`) as text",
                    whereOr: ` OR zipcode like '%${params.term}%' OR cities.name like '%${params.term}%' AND (users_group_id = 2)`,
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
    $('.customer').select2({
        minimumInputLength: 2,
        ajax: {
            url: `${base_url}/get-value`,
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term, // search term
                    table:'users',
                    fields: "users.id, CONCAT(`users`.`name`, ' - ', `zipcode`,' - ',`cities`.`name`) as text",
                    // where: ` OR zipcode like '%${params.term}%' OR cities.name like '%${params.term}%' AND (users_group_id=4 OR users_group_id=5)`,
                    whereOr: ` OR zipcode like '%${params.term}%' OR cities.name like '%${params.term}%' AND (users_group_id=4 OR users_group_id=5)`,
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
        minimumInputLength: 2,
        ajax: {
            url: `${base_url}/get-value`,
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
    $('#addCustomer').on('click',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        // var data = $('#saveInquiryLog').serialize();        
        console.log("Add Customer Click");
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
});
</script>
@endsection
