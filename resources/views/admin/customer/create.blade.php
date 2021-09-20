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
        <form method="POST" action="{{ route('customer.update',$user->id) }}">
            @method('PUT')
            <input type="hidden" name="updated_by" value="{{auth()->user()->id}}">
            @else
            <form method="POST" action="{{ route('customer.index') }}">
                <input type="hidden" name="created_by" value="{{auth()->user()->id}}">
                @endif
                @csrf
                <input type="hidden" name="id" value="{{ (!empty($user))?$user->id:'' }}">
                <input type="hidden" name="users_group_id" value="{{ $users_group_id }}">
                

                <div class="row">
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
                            <input type="email" class="form-control" name="email" value="{{ old('email',(!empty($user))?$user->email:'') }}" placeholder="Email" autocomplete="false">
                            @error('email')
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
                            <select class="form-control select2 state" id='state' name='state_id'>
                                <option value="">Select</option>
                                @if(!empty($user) && $user->state_id)
                                <option value="{{$user->state_id}}" selected>{{$user->state->name}}</option>
                                @endif
                                @if(old('state_id'))     
                                    @php 
                                        $data = $select2Old->getSelect2Data('states',old('state_id'))
                                    @endphp                   
                                    <option value="{{$data->id}}" selected>{{$data->name}}</option>
                                @endif
                            </select>
                            <input type="hidden" id="state_id" value="{{old('state_id')}}">
                            @error('state_id')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <!-- text input -->
                        <div class="form-group">
                            <label>City</label>
                            <select class="form-control select2 city" id='city' name='city_id'>
                                <option value="">Select</option>
                                @if(!empty($user) && !empty($user->city_id))
                                <option value="{{$user->city_id}}" selected>{{$user->city->name}}</option>
                                @endif
                                @if(old('city_id'))     
                                    @php 
                                        $data = $select2Old->getSelect2Data('cities',old('city_id'),' state_id='. old('state_id'))
                                    @endphp          
                                    @if (!empty($data))
                                        <option value="{{$data->id}}" selected>{{$data->name}}</option>
                                    @endif         
                                @endif
                            </select>
                            <input type="hidden" id="city_id" value="{{old('city_id')}}">
                            @error('city_id')
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
jQuery(document).ready(function() {
    $('.dealer').select2({
        placeholder: "Select",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "{{route('get_value')}}",
            dataType: 'json',
            data: function(params) {
                return {
                    q: params.term, // search term
                    table: 'users',
                    where: 'users_group_id=2'
                };
            },
            processResults: function(data) {
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
            dataType: 'json',
            data: function(params) {
                return {
                    q: params.term, // search term
                    table: 'states',
                    where: 'country_id=101'
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });
    $('.city').select2({
        placeholder: "Select",
        allowClear: true,
    });
    $('.state').on('change', function() {
        if ($('.state').val()) {
            select2city();
            $(`.city`).val('').trigger('change');
        }else {
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
            });
        }
    });
    if ($('#state').val() || $('#city').val()) {
        select2city();
    }

    function select2city(){
        $('.city').select2({
                placeholder: "Select",
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: "{{route('get_value')}}",
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