@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">{{ ucwords($title) }}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">     
        @if(!empty($inquiry))
            <form method="POST" action="{{ route('inquiry-log.update',$inquiry->id) }}">
            @method('PUT')
            <input type="hidden" name="updated_by" value="{{auth()->user()->id}}">
        @else
            <form method="POST" action="{{ route('inquiry-log.index') }}">
            <input type="hidden" name="created_by" value="{{auth()->user()->id}}">
        @endif
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($inquiry))?$inquiry->id:'' }}">
        

        <div class="row">
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Inquiry Type</label>
                    <select class="form-control select2 type" id='type' name='type' required>
                        <option value="">Select</option>
                            <option value="0"></option>
                            <option value="email">Email</option>
                            <option value="calls">Calls</option>
                            <option value="meetings">Meetings</option>
                            <option value="notes">Notes</option>
                    </select>
                    @error('type')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Reminder</label>
                    <input type="text" class="form-control datetimepicker-input reminder" id="datetimepicker" name="reminder" data-toggle="datetimepicker" data-target="#datetimepicker"/>
                    @error('reminder')
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
@section('js')
<script>
jQuery(document).ready(function(){
    var base_url = "{{url('/admin')}}";
    $('.type').select2();
    $('#datetimepicker').datetimepicker();
    /* $('.dealer').select2({
        minimumInputLength: 2,
        ajax: {
            url: `${base_url}/get-value`,
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
    }); */
});
</script>
@endsection
