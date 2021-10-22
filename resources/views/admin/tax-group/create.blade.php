@extends('layouts.adminlte')
@section('title')
{{$title}}
@endsection
@section('content')
<style>
   input[type="checkbox"][readonly] {
  pointer-events: none;
}
</style>
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">{{$action_title}} {{$title}}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        @if(!empty($taxGroup))
        <form method="POST" action="{{ route('tax-group.update',$taxGroup->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @else
            <form method="POST" action="{{ route('tax-group.index') }}" enctype="multipart/form-data">
                @endif
                @csrf
                <input type="hidden" name="id" value="{{ (!empty($taxGroup))?$taxGroup->id:'' }}">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Name <span class="required_field">*</span></label>
                            <input type="text" class="form-control" name="name"
                                value="{{ old('name',(!empty($taxGroup))?$taxGroup->name:'') }}" placeholder="Name" tabindex="2">
                            @error('name')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        @if(!empty($taxes))
                        <table class="table">
                            <tbody>
                                @foreach ($taxes as $tax)
                                <tr>
                                    <td width="15%">
                                        <div class="form-group">
                                            <div class="pt-2">
                                                    <label>{{$tax->name}} <span class="required_field">*</span></label>
                                            </div>
                                        </div>
                                    </td>
                                    <td align="left">
                                        <!-- text input -->
                                        <div class="form-group">
                                            <div class="d-flex inline align-items-center col-3">
                                                <input type="hidden" name="tax_id[]" value="{{$tax->id}}">
                                                @php
                                                    $taxPercentage = (!empty($taxGroup) && (!empty($tax->taxGroupItems))) ? $tax->getTaxPercentage($taxGroup->id) : '';
                                                @endphp
                                                <input type="text" class="form-control" name="percentage[{{$tax->id}}]"
                                                value="{{ old('percentage.'.$tax->id,(!empty($tax) && (!empty($tax->taxGroupItems) && isset($taxPercentage))) ?  $taxPercentage: '') }}" placeholder="Percentage" tabindex="4" />
                                                <span class=" float-left ml-2">%</span>
                                            </div>
                                            @error("percentage.$tax->id")
                                            <div class="alert alert-danger mt-2 col-3">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group">
                            <div class="pt-2">
                                <input type="checkbox" class="input-control" id="is_default" name="is_default" {{((!empty($taxGroup)) && (!empty($taxGroup->is_default)))? "checked readonly" :"" }} tabindex="5">
                                    <label>Make as a Default</label>
                            </div>
                            @if((!empty($taxGroup)) && (!empty($taxGroup->is_default)))
                            <label><span class="text-red"><strong>Note:</strong> System need one Tax group as default, So please go in any other group and check the checkbox for the "Make default" and save that so, This group will be removed from default.</span></label>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label class="col-12">Status</label>
                            <input type="checkbox" name="status" id="status" class="form-control status" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" {{((!isset($taxGroup)) || (!empty($taxGroup->status)))? "checked" :"" }} tabindex="6">
                            @error('status')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary" tabindex="7">Submit</button>
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
    jQuery(document).ready(function() {
        $('#is_default').on('change',function(){
            let data = confirm("Are you sure you wants to make it default? {{$default->name}} is already default a Tax Group.");
            let default_val = $('#is_default').is(":checked")
            if(data){
                $('#is_default').prop('checked',default_val);
            }else{
                $('#is_default').prop('checked',!default_val);
            }
        })
    });
</script>
@endsection