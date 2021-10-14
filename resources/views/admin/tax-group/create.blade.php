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
                                                <input type="checkbox" class="mr-2" name="tax_id[]"
                                                value="{{$tax->id}}"
                                                {{((!isset($tax)) || (!empty($taxGroup) && !empty($tax->getTaxItem($taxGroup->id))) )? "checked" :"" }} tabindex="3">
                                                    <label>{{$tax->name}}</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td align="left">
                                        <!-- text input -->
                                        <div class="form-group">
                                            <div class="d-flex inline align-items-center col-3">
                                                @php
                                                    $taxPercentage = (!empty($taxGroup) && (!empty($tax->taxGroupItems))) ? $tax->getTaxPercentage($taxGroup->id) : '';
                                                @endphp
                                                <input type="text" class="form-control" name="percentage[{{$tax->id}}]"
                                                value="{{ old('percentage[]',(!empty($tax) && (!empty($tax->taxGroupItems) && !empty($taxPercentage))) ?  $taxPercentage: '') }}" placeholder="Percentage" tabindex="4">
                                                <span class=" float-left ml-2">%</span>
                                            </div>
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
                                <input type="checkbox" class="input-control" name="is_default" {{((!empty($taxGroup)) && (!empty($taxGroup->is_default)))? "checked" :"" }} tabindex="5">
                                    <label>Make as a Default</label>
                            </div>
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

    });
</script>
@endsection