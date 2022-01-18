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
                        <input type="checkbox" class="input-control" id="is_enabled" value="1" name="is_enabled" tabindex="5" {{((!empty($settings)) && (!empty($settings->json_data['is_enabled'])))? "checked readonly" :"" }} >
                            <label>Enabled</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>API Key</label>
                    <input type="text" class="form-control" name="api_key" value="{{ old('api_key',(!empty($settings->json_data['api_key']))?$settings->json_data['api_key']:'')}}" placeholder="API Key" tabindex="1">
                    @error('api_key')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Sender ID</label>
                    <input type="text" class="form-control" name="sender_id" value="{{ old('sender_id',(!empty($settings->json_data['sender_id']))?$settings->json_data['sender_id']:'')}}" placeholder="Sender ID" tabindex="2">
                    @error('sender_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>SMS Type</label>
                    <input type="text" class="form-control" name="sms_type" value="{{ old('sms_type',(!empty($settings->json_data['sms_type']))?$settings->json_data['sms_type']:'')}}" placeholder="SMS Type" tabindex="3">
                    @error('sms_type')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Entity ID</label>
                    <input type="text" class="form-control" name="entity_id" value="{{ old('entity_id',(!empty($settings->json_data['entity_id']))?$settings->json_data['entity_id']:'')}}" placeholder="Entity ID" tabindex="4">
                    @error('entity_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Template ID</label>
                    <input type="text" class="form-control" name="template_id" value="{{ old('template_id',(!empty($settings->json_data['template_id']))?$settings->json_data['template_id']:'')}}" placeholder="Template ID" tabindex="5">
                    @error('template_id')
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
    
});
</script>
@endsection
