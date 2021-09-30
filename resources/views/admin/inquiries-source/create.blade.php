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
        @if(!empty($inquiries_source))
        <form method="POST" action="{{ route('inquiries-source.update',$inquiries_source->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @else
            <form method="POST" action="{{ route('inquiries-source.index') }}" enctype="multipart/form-data">
                @endif
                @csrf
                <input type="hidden" name="id" value="{{ (!empty($inquiries_source))?$inquiries_source->id:'' }}">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Source <span class="required_field">*</span></label>
                            <input type="text" class="form-control" name="source"
                                value="{{ (!empty($inquiries_source))?$inquiries_source->source:'' }}" placeholder="source" tabindex="1">
                            @error('source')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>       
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label class="col-12">Status</label>
                            <input type="checkbox" name="status" id="status" class="form-control status" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" {{((!isset($inquiries_source)) || (!empty($inquiries_source->status)))? "checked" :"" }} tabindex="2">
                            @error('status')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary" tabindex="3">Submit</button>
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
    $(document).ready(function(){
        $("input[data-bootstrap-switch]").each(function(){
            // $(this).bootstrapSwitch('state', $(this).prop('checked'));
            $(this).bootstrapSwitch();
        });
    });
</script>
@endsection
