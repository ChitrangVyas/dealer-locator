@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">{{$title}} User</h3>
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
        <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Dealer</label>
                    <select class="form-control select2 dealer" id='dealer' name='dealer'>
                        <option value="">Select Dealer</option>
                        @if(!empty($user) && !empty($user->parent_id))
                            <option value="{{$user->parent_id}}" selected>{{$user->name}}</option>
                        @endif
                    </select>
                    @error('dealer')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name',(!empty($user))?$user->name:'') }}" placeholder="Full Name">
                    @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="{{old('email', (!empty($user))?$user->email:'') }}" placeholder="Email" autocomplete="false">
                    @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" value="{{old('password')}}" class="form-control" placeholder="Password">
                    @error('password')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" name="mobile" value="{{ old('mobile',(!empty($user))?$user->mobile:'') }}" class="form-control" placeholder="Mobile">
                    @error('mobile')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" name="address" rows="1" placeholder="Address">{{ old('address',(!empty($user))?$user->address:'') }}</textarea>
                    @error('address')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>State</label>
                    <select class="form-control select2 state" id='state' name='state'>
                        <option value="">Select</option>
                        @if(!empty($user) && $user->state_id)
                            <option value="{{$user->state_id}}" selected>{{$user->state->name}}</option>
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
                    <label>City</label>
                    <select class="form-control select2 city" id='city' name='city'>
                        <option value="">Select</option>
                        @if(!empty($user) && !empty($user->city_id))
                            <option value="{{$user->city_id}}" selected>{{$user->city->name}}</option>
                        @endif
                    </select>
                    <input type="hidden" id="city_id" value="{{old('city')}}">
                    @error('city')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Zipcode</label>
                    <input type="text" name="zipcode" value="{{ old('zipcode',(!empty($user))?$user->zipcode:'') }}" class="form-control" placeholder="Enter zipcode">
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
    $('.dealer').select2({
        minimumInputLength: 2,
        ajax: {
            url: "{{route(get_value)}}",
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
        minimumInputLength: 2,
        ajax: {
            url: "{{route(get_value)}}",
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
                minimumInputLength: 2,
                ajax: {
                    url: "{{route(get_value)}}",
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
