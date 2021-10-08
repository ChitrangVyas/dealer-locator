@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">{{$action_title}} {{$title}}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">     
        @if(!empty($user))
            <form method="POST" action="{{ route('retailer.update',$user->id) }}">
            @method('PUT')
        @else
            <form method="POST" action="{{ route('retailer.index') }}">
        @endif
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($user))?$user->id:'' }}">
        <input type="hidden" name="user_group" value="3">
        <input type="hidden" name="created_by" value="{{auth()->user()->id}}">
        <div class="row">
        @admin()
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Dealer <span class="required_field">*</span></label>
                    <select class="form-control select2 dealer" id='dealer' name='dealer' tabindex="1">
                        <option value="">Select Dealer</option>
                        @if(!empty($user) && !empty($user->parent_id))
                            <option value="{{$user->parent_id}}" selected>{{$user->user->name}}</option>
                        @endif
                        @if(old('dealer'))     
                            @php 
                                $data = $select2Old->getSelect2Data('users',old('dealer'))
                            @endphp                   
                            <option value="{{$data->id}}" selected>{{$data->name}}</option>
                        @endif
                    </select>
                    @error('dealer')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endadmin
        @if(auth()->user()->users_group_id == 2)
        <input type="hidden" name="dealer" value="{{auth()->user()->id}}">
        @endif
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group required">
                    <label>Name <span class="required_field">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name',(!empty($user))?$user->name:'') }}" placeholder="Full Name" tabindex="2">
                    @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Email <span class="required_field">*</span></label>
                    <input type="email" class="form-control" name="email" value="{{old('email', (!empty($user))?$user->email:'') }}" placeholder="Email" autocomplete="false" tabindex="3">
                    @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Password <span class="required_field">*</span></label>
                    <input type="password" name="password" value="{{old('password')}}" class="form-control" placeholder="Password" tabindex="4">
                    @error('password')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mobile <span class="required_field">*</span></label>
                    <input type="text" name="mobile" value="{{ old('mobile',(!empty($user))?$user->mobile:'') }}" class="form-control" placeholder="Mobile" tabindex="5">
                    @error('mobile')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Address <span class="required_field">*</span></label>
                    <textarea class="form-control" name="address" rows="1" placeholder="Address" tabindex="6">{{ old('address',(!empty($user))?$user->address:'') }}</textarea>
                    @error('address')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>State <span class="required_field">*</span></label>
                    <select class="form-control select2 state" id='state' name='state' tabindex="7">
                        <option value="">Select</option>
                        @if(!empty($user) && $user->state_id)
                            <option value="{{$user->state_id}}" selected>{{$user->state->name}}</option>
                        @endif
                        @if(old('state'))     
                            @php 
                                $data = $select2Old->getSelect2Data('states',old('state'))
                            @endphp                   
                            <option value="{{$data->id}}" selected>{{$data->name}}</option>
                        @endif
                    </select>
                    <input type="hidden" id="state_id" value="{{old('state')}}">
                    @error('state')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>City <span class="required_field">*</span></label>
                    <select class="form-control select2 city" id='city' name='city' tabindex="8">
                        <option value="">Select</option>
                        @if(!empty($user) && !empty($user->city_id))
                            <option value="{{$user->city_id}}" selected>{{$user->city->name}}</option>
                        @endif
                        @if(old('city'))     
                            @php 
                                $data = $select2Old->getSelect2Data('cities',old('city'),' state_id='. old('state'))
                            @endphp          
                            @if (!empty($data))
                                <option value="{{$data->id}}" selected>{{$data->name}}</option>
                            @endif         
                        @endif
                    </select>
                    <label><span class="text-red"><strong>Note:</strong> Please select the state before select the city.</span></label>
                    <input type="hidden" id="city_id" value="{{old('city')}}">
                    @error('city')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Zip code <span class="required_field">*</span></label>
                    <input type="text" name="zipcode" value="{{ old('zipcode',(!empty($user))?$user->zipcode:'') }}" class="form-control" placeholder="Enter zipcode" tabindex="9">
                    @error('zipcode')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <!-- text input -->
                <div class="form-group">
                    <label class="col-12">Status</label>
                    <input type="checkbox" name="status" id="status" class="form-control status" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" {{((!isset($user)) || (!empty($user->status)))? "checked" :"" }} tabindex="2">
                    @error('status')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" tabindex="10">Submit</button>
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
    $('.dealer').select2({
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
                    table:'users',
                    where: 'users_group_id=2'
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    });
    $('.state').select2({
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
                    table:'states',
                    where: "country_id={{$country}}"
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    }).on('select2:clear',function(){
        select2city();
    });
    $('.city').select2({});
    $('.state').on('change', function() {
        select2city();
    });
    if ($('#state').val() || $('#city').val()) {
        select2city();
    }

    function select2city(){
        if ($('.state').val()) {
            $('.city').select2({
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
                            table: 'cities',
                            page: params.page,
                            where: `state_id=${$('#state').val()}`
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
            $(`.city`).empty().append(new Option()).trigger('change');
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
            });
        }
    }

    function getStateCity(table,where,eleClass) {
        if($('#state_id').val() || $('#city_id').val()){
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
