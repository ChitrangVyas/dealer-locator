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
                    <label>Name <span class="required_field">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name',(!empty($manufacturer))?$manufacturer->name:'') }}" placeholder="Full Name" tabindex="1">
                    @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Email <span class="required_field">*</span></label>
                    <input type="email" class="form-control" name="email" value="{{old('email',(!empty($manufacturer))?$manufacturer->email:'') }}" placeholder="Email" autocomplete="false" tabindex="2">
                    @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mobile <span class="required_field">*</span></label>
                    <input type="text" name="mobile" value="{{old('mobile', (!empty($manufacturer))?$manufacturer->mobile:'') }}" class="form-control" placeholder="Mobile" tabindex="3">
                    @error('mobile')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- file input -->
                <div class="form-group">
                    <label>Logo <span class="required_field">*</span></label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="logo" name="logo" tabindex="4">
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
                    <textarea class="form-control" name="address" rows="1" placeholder="Address" tabindex="5">{{ old('address',(!empty($manufacturer))?$manufacturer->address:'') }}</textarea>
                    @error('address')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                        <!-- text input -->
                <div class="form-group">
                    <label>Country <span class="required_field">*</span></label>
                    <select class="form-control country" id='country' name='country_id' tabindex="5">
                        <option value="">Select</option>
                        
                        @if(!empty($allowedcountry))
                         @php
                         $selectcountry=''
                         @endphp
                            @foreach($allowedcountry as $acountry)
                            @if(!empty($manufacturer) && $manufacturer->country_id)
                               @php 
                               
                                $selectcountry=($manufacturer->country_id == $acountry->id)? 'selected' : ''
                                
                                @endphp
                            @endif
                            @if(old('country_id'))
                                @php 
                               
                                $selectcountry=(old('country_id') == $acountry->id)? 'selected' : ''
                                
                                @endphp
                            @endif
                            

                                <option value="{{$acountry->id}}"  {{$selectcountry}}>{{$acountry->name}}</option>
                               
                                @endforeach
                        @endif
                       
                    </select>
                    <input type="hidden" id="country_id" value="{{old('country_id')}}">
                    @error('country_id')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>State</label>
                    <select class="form-control select2 state" id='state' name='state_id' tabindex="6">
                        <option value="">Select</option>
                        @if(!empty($manufacturer) && $manufacturer->state_id)
                            <option value="{{$manufacturer->state_id}}" selected>{{$manufacturer->state->name}}</option>
                        @endif
                        @if(old('state_id'))     
                            @php 
                                $data = $select2Old->getSelect2Data('states',old('state_id'))
                            @endphp                   
                            <option value="{{$data->id}}" selected>{{$data->name}}</option>
                        @endif
                    </select>
                    <label><span class="text-red"><strong>Note:</strong> Please select the country before select the state.</span></label>
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
                    <select class="form-control select2 city" id='city' name='city_id' tabindex="7">
                        <option value="">Select</option>
                        @if(!empty($manufacturer) && !empty($manufacturer->city_id))
                            <option value="{{$manufacturer->city_id}}" selected>{{$manufacturer->city->name}}</option>
                        @endif
                        @if(old('city_id'))     
                            @php 
                                $data = $select2Old->getSelect2Data('cities',old('city_id'),' state_id='. old('state_id'))
                            @endphp          
                            @if (!empty($data))
                                <option value="{{$data->id}}" selected>{{$data->name}}</option>
                            @endif         
                        @endif
                    </select>
                    <label><span class="text-red"><strong>Note:</strong> Please select the state before select the city.</span></label>
                    <input type="hidden" id="city_id" value="{{old('city_id')}}">
                    @error('city_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Zip code</label>
                    <input type="text" name="zipcode" value="{{ old('zipcode',(!empty($manufacturer))?$manufacturer->zipcode:'')}}" class="form-control" placeholder="Enter zipcode" tabindex="8">
                    @error('zipcode')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <!-- text input -->
                <div class="form-group">
                    <label class="col-12">Status</label>
                    <input type="checkbox" name="status" id="status" class="form-control status" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" {{((!isset($manufacturer)) || (!empty($manufacturer->status)))? "checked" :"" }} tabindex="2">
                    @error('status')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" tabindex="9">Submit</button>
            </div>
        </div>
    </form>
    </div>
    <!-- /.card-body -->
</div>
@endsection
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
@section('js')
<script>
jQuery(document).ready(function(){

    $('.state').select2();
    $('.country').on('change', function() {
        $(`.state`).empty().append(new Option()).trigger('change');
            $('.state').select2({
                placeholder: "Select",
                allowClear: true,
            });
            $(`.city`).empty().append(new Option()).trigger('change');
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
            });
        select2state();
    });
    if ($('#country').val() || $('#state').val()) {
        select2state();
    }

    // $('.state').select2({
    //     placeholder: "Select",
    //     allowClear: true,
    //     minimumInputLength: 2,
    //     ajax: {
    //         url: "{{route('get_value')}}",
    //         type: 'POST',
    //         dataType: 'json',
    //         data: function (params) {
    //             return {
    //                 q: params.term, // search term
    //                 table:'states',
    //                 where: "country_id={{$country}}"
    //             };
    //         },
    //         processResults: function (data) {
    //             return {
    //                 results: data
    //             };
    //         }
    //     }
    // }).on('select2:clear',function(){
    //     checkState();
    // });

    function select2state(){
        if ($('.country').val()) {
            
            $('.state').select2({
                placeholder: "Select",
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: "{{route('get_value')}}",
                    type: 'POST',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            table: 'states',
                            page: params.page,
                            where: `country_id=${$('#country').val()}`
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });
        }else {
            $(`.state`).empty().append(new Option()).trigger('change');
            $('.state').select2({
                placeholder: "Select",
                allowClear: true,
            });
        }
    }

    $('.city').select2({
        placeholder: "Select",
        allowClear: true,
    });
    $('.state').on('change', function(){
        $(`.city`).empty().append(new Option()).trigger('change');
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
            });
        checkState();
        $(`.city`).val(null).trigger('change');
    });

    /* if ($('#state').val() || $('#city').val()) {
        getStateCity('states',` id=${$('#state').val()}`,'state');
        getStateCity('cities', ` id=${$('#city').val()}`,'city');
    } */
    function checkState(){
        if($('.state').val()){
            $('.city').select2({
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
            $(`.city`).empty().append(new Option()).trigger('change');
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
            });
        }
    }

    function getStateCity(table,where,eleClass) {
        if($('#state').val() || $('#city').val()){
            $.ajax({
                url: "{{route('get_value')}}",
                type: 'POST',
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
