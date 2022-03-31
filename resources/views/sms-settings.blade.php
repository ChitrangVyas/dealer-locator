@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">SMS Settings</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">     
        <form method="POST" action="{{ route('sms.settings.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($settings) && !empty($settings->id))?$settings->id:'' }}">
        <div class="row">
            <div class="col-sm-12 col-md-12 border-bottom mt-2 mb-2">
                    <div class="col-6">
                        <h4 class="text-muted">SMS Settings</h4>
                    </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <div class="form-group">
                    <div class="pt-2">
                        <input type="checkbox" class="input-control" id="is_enabled" value="1" name="is_enabled" tabindex="1" {{((!empty($settings)) && (!empty($settings->json_data['is_enabled'])))? "checked readonly" :"" }} >
                            <label>Enabled</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>User Id</label>
                    <input type="text" class="form-control" name="userId" value="{{ old('userId',(!empty($settings->json_data['userId']))?$settings->json_data['userId']:'')}}" placeholder="User Id" tabindex="2">
                    @error('userId')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="sms_password" name="sms_password" value="{{ old('sms_password',(!empty($settings->json_data['sms_password']))?$settings->json_data['sms_password']:'')}}" placeholder="Password" tabindex="3">
                    <span toggle="#sms_password" class="fa fa-fw fa-eye eye-icon toggle-password"></span>
                    @error('sms_password')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>           
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Sender ID</label>
                    <input type="text" class="form-control" name="sender_id" value="{{ old('sender_id',(!empty($settings->json_data['sender_id']))?$settings->json_data['sender_id']:'')}}" placeholder="Sender ID" tabindex="5">
                    @error('sender_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>SMS Type</label>
                    <input type="text" class="form-control" name="sms_type" value="{{ old('sms_type',(!empty($settings->json_data['sms_type']))?$settings->json_data['sms_type']:'')}}" placeholder="SMS Type" tabindex="6">
                    @error('sms_type')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Entity ID</label>
                    <input type="text" class="form-control" name="entity_id" value="{{ old('entity_id',(!empty($settings->json_data['entity_id']))?$settings->json_data['entity_id']:'')}}" placeholder="Entity ID" tabindex="7">
                    @error('entity_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Inquiry Assigned to Dealer/Retailer Template ID</label>
                    <input type="text" class="form-control" name="template_id[inquiry_assign]" value="{{ old('template_id[inquiry_assign]',(!empty($settings->json_data['template_id']['inquiry_assign']))?$settings->json_data['template_id']['inquiry_assign']:'')}}" placeholder="Template ID" tabindex="8">
                    @error('template_id["inquiry_assign"]')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Customer Inquiry Template ID</label>
                    <input type="text" class="form-control" name="template_id[inquiry_customer_assigned]" value="{{ old('template_id[inquiry_customer_assigned]',(!empty($settings->json_data['template_id']['inquiry_customer_assigned']))?$settings->json_data['template_id']['inquiry_customer_assigned']:'')}}" placeholder="Template ID" tabindex="8">
                    @error('template_id["inquiry_assign"]')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" tabindex="20">Submit</button>
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
});
</script>
@endsection
