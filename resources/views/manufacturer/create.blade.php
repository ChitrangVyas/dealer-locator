@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">{{$action_title}} Manufacturer</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">     
        @if(!empty($manufacturer))
            <form method="POST" action="{{ route('manufacturer.update',$manufacturer->id) }}" enctype="multipart/form-data">
            @method('PUT')
        @else
            <form method="POST" action="{{ route('manufacturer.index') }}" enctype="multipart/form-data">
        @endif
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($manufacturer))?$manufacturer->id:'' }}">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name',(!empty($manufacturer))?$manufacturer->name:'') }}" placeholder="Full Name">
                    @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="{{old('email',(!empty($manufacturer))?$manufacturer->email:'') }}" placeholder="Email" autocomplete="false">
                    @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" name="mobile" value="{{old('mobile', (!empty($manufacturer))?$manufacturer->mobile:'') }}" class="form-control" placeholder="Mobile">
                    @error('mobile')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- file input -->
                <div class="form-group">
                    <label>Logo</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="logo" name="logo">
                        <label class="custom-file-label" for="logo">Choose file</label>
                      </div>
                    </div>
                    @if(!empty($manufacturer->logo))
                    <div class="">
                        <img class='img logo mt-3 mb-4' src="{{ url($manufacturer->logo) }}" width=100 />
                    </div>
                    @endif
                    @error('logo')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" name="address" rows="1" placeholder="Address">{{ old('address',(!empty($manufacturer))?$manufacturer->address:'') }}</textarea>
                    @error('address')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>State</label>
                    <select class="form-control select2 state" id='state' name='state_id'>
                        <option value="">Select</option>
                        @if(!empty($manufacturer) && $manufacturer->state_id)
                            <option value="{{$manufacturer->state_id}}" selected>{{$manufacturer->state->name}}</option>
                        @endif
                    </select>
                    <input type="hidden" id="state_id" value="{{old('state_id')}}">
                    @error('state_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>City</label>
                    <select class="form-control select2 city" id='city' name='city_id'>
                        <option value="">Select</option>
                        @if(!empty($manufacturer) && !empty($manufacturer->city_id))
                            <option value="{{$manufacturer->city_id}}" selected>{{$manufacturer->city->name}}</option>
                        @endif
                    </select>
                    <input type="hidden" id="city_id" value="{{old('city_id')}}">
                    @error('city_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Zipcode</label>
                    <input type="text" name="zipcode" value="{{ old('zipcode',(!empty($manufacturer))?$manufacturer->zipcode:'')}}" class="form-control" placeholder="Enter zipcode">
                    @error('zipcode')
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
@endsection
@section('plugins.Select2', true)
@section('js')
<script>
jQuery(document).ready(function(){
    $('.state').select2({
        placeholder: "Select",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "{{route('get_value')}}",
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term, // search term
                    table:'states',
                    where: 'country_id=101'
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    });
    $('.city').select2();
    $('.state').on('change', function(){
        if($('.state').val()){
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: "{{route('get_value')}}",
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            table:'cities',
                            page: params.page,
                            where: `state_id=${$('#state').val()}`
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });
        }else{
            $('.city').select2({});
        }
    });

    if ($('#state_id').val() || $('#city_id').val()) {
        getStateCity('states',` id=${$('#state_id').val()}`,'state');
        getStateCity('cities', ` id=${$('#city_id').val()}`,'city');
    }

    function getStateCity(table,where,eleClass) {
        if($('#state_id').val() || $('#city_id').val()){
            $.ajax({
                url: "{{route('get_value')}}",
                type: 'GET',
                data: {
                    table: table,
                    where: where,
                },
                success: function(res) {
                    $.each(JSON.parse(res), function(key, val) {
                        var newOption = new Option(val.text, val.id, true, true);
                        $(`.${eleClass}`).append(newOption).trigger('change');
                    })
                }
            });
        }
    }
});
</script>
@endsection
