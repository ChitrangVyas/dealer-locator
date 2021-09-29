@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">Change Password</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">     
        @if(!empty($user))
            <form method="POST" action="{{ route('dealer.update',$user->id) }}">
            @method('PUT')
        @endif
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($user))?$user->id:'' }}">        
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Old Password <span class="required_field">*</span></label>
                    <input type="text" class="form-control" name="old_password" value="" placeholder="Old Passsword" tabindex="1">
                    @error('old_password')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>New Password <span class="required_field">*</span></label>
                    <input type="old_password" class="form-control" name="old_password" value="" placeholder="New Password" autocomplete="false" tabindex="2">
                    @error('old_password')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Confirm Password <span class="required_field">*</span></label>
                    <input type="password" name="confirm_password" value="" class="form-control" placeholder="Password" tabindex="3">
                    @error('confirm_password')
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
@section('js')

@endsection
