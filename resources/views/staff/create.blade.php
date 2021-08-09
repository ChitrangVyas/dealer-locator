@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">Staff User</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">     
        @if(!empty($user))
            <form method="POST" action="{{ route('staff.update',$user->id) }}">
            @method('PUT')
        @else
            <form method="POST" action="{{ route('staff.index') }}">
        @endif
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($user))?$user->id:'' }}">
        <input type="hidden" name="user_group" value="1">
        <input type="hidden" name="created_by" value="{{auth()->user()->id}}">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ (!empty($user))?$user->name:'' }}" placeholder="Full Name">
                    @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="{{ (!empty($user))?$user->email:'' }}" placeholder="Email" autocomplete="false">
                    @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" value="{{ (!empty($user))?$user->password:'' }}" class="form-control" placeholder="Password">
                    @error('password')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" name="mobile" value="{{ (!empty($user))?$user->mobile:'' }}" class="form-control" placeholder="Mobile">
                    @error('mobile')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" name="address" rows="1" placeholder="Address">{{ (!empty($user))?$user->address:'' }}</textarea>
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
                    @error('city')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Zipcode</label>
                    <input type="text" name="zipcode" value="{{ (!empty($user))?$user->zipcode:'' }}" class="form-control" placeholder="Enter zipcode">
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
    var base_url = "{{url('/admin')}}";
    $('.state').select2({
        minimumInputLength: 2,
        ajax: {
            url: `${base_url}/get-value`,
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
                    url: `${base_url}/get-value`,
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
});
</script>
@endsection
