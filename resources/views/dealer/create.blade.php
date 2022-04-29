@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">{{$action_title}} Dealer</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">     
        @if(!empty($user))
            <form method="POST" action="{{ route('dealer.update',$user->id) }}" enctype="multipart/form-data" >
            @method('PUT')
        @else
            <form method="POST" action="{{ route('dealer.index') }}" enctype="multipart/form-data" >
        @endif
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($user))?$user->id:'' }}">
        <input type="hidden" name="user_group" value="2">
        <input type="hidden" name="created_by" value="{{auth()->user()->id}}">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Name <span class="required_field">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name',(!empty($user))?$user->name:'') }}" placeholder="Full Name" tabindex="1">
                    @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Email <span class="required_field">*</span></label>
                    <input type="email" class="form-control" name="email" value="{{ old('email',(!empty($user))?$user->email:'') }}" placeholder="Email" autocomplete="false" tabindex="2">
                    @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Password <span class="required_field">*</span></label>
                    <input type="password" name="password" value="{{old('password')}}" class="form-control" placeholder="Password" tabindex="3">
                    @error('password')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mobile <span class="required_field">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="countryCode"></span>
                        </div>
                        <input type="text" name="mobile" value="{{old('mobile', (!empty($user))?$user->mobile:'') }}" class="form-control" placeholder="Mobile" tabindex="4">
                    </div>
                    @error('mobile')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Address <span class="required_field">*</span></label>
                    <textarea class="form-control" name="address" rows="1" placeholder="Address" tabindex="5">{{ old('address',(!empty($user))?$user->address:'') }}</textarea>
                    @error('address')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                        <!-- text input -->
                <div class="form-group">
                    <label>Country <span class="required_field">*</span></label>
                    <select class="form-control country" id='country' name='country_id' tabindex="5">
                        <option value="">Select</option>
                        
                        @if(!empty($allowedcountry))
                         @php
                         $selectcountry=''
                         @endphp
                            @foreach($allowedcountry as $acountry)
                            @if(!empty($user) && $user->country_id)
                               @php 
                               
                                $selectcountry=($user->country_id == $acountry->id)? 'selected' : ''
                                
                                @endphp
                            @endif
                            @if(old('country_id'))
                                @php 
                               
                                $selectcountry=(old('country_id') == $acountry->id)? 'selected' : ''
                                
                                @endphp
                            @endif
                            

                                <option value="{{$acountry->id}}"  {{$selectcountry}}>{{$acountry->name}}</option>
                               
                                @endforeach
                        @endif
                       
                    </select>
                    <input type="hidden" id="country_id" value="{{old('country_id')}}">
                    @error('country_id')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>State <span class="required_field">*</span></label>
                    <select class="form-control select2 state" id='state' name='state' tabindex="6">
                        <option value="">Select</option>
                        @if(!empty($user) && $user->state_id)
                            <option value="{{$user->state_id}}" selected>{{$user->state->name}}</option>
                        @endif
                        @if(old('state'))     
                            @php 
                                $data = $select2Old->getSelect2Data('states',old('state'))
                            @endphp                   
                            <option value="{{$data->id}}" selected>{{$data->name}}</option>
                        @endif
                    </select>
                    <input type="hidden" id="state_id" value="{{old('state')}}">
                    <label><span class="text-red"><strong>Note:</strong> Please select the country before select the state.</span></label>
                    @error('state')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>City <span class="required_field">*</span></label>
                    <select class="form-control select2 city" id='city' name='city' tabindex="7">
                        <option value="">Select</option>
                        @if(!empty($user) && !empty($user->city_id))
                            <option value="{{$user->city_id}}" selected>{{$user->city->name}}</option>
                        @endif
                        @if(old('city'))     
                            @php 
                                $data = $select2Old->getSelect2Data('cities',old('city'),' state_id='. old('state'))
                            @endphp          
                            @if (!empty($data))
                                <option value="{{$data->id}}" selected>{{$data->name}}</option>
                            @endif         
                        @endif
                    </select>
                    <label><span class="text-red"><strong>Note:</strong> Please select the state before select the city.</span></label>
                    <input type="hidden" id="city_id" value="{{old('city')}}">
                    @error('city')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Zip code <span class="required_field">*</span></label>
                    <input type="text" name="zipcode" value="{{ old('zipcode',(!empty($user))?$user->zipcode:'')}}" class="form-control" placeholder="Enter zipcode" tabindex="8">
                    @error('zipcode')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- file input -->
                <div class="form-group">
                    <label>Logo</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input restrictFileUpload" id="logo" value='' name="logo" tabindex="4">
                            <label class="custom-file-label" for="logo">Choose file</label>
                        </div>
                    </div>
                    @if(!empty($user->logo))
                    <div class="user_logo">
                        <img class='img logo mt-3 mb-4' src="{{ url($user->logo) }}" width=100 />
                    </div>
                    @endif                   
                </div>
            </div>            
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>GSTIN</label>
                    <input type="text" class="form-control" name="gstin" value="{{ old('gstin',(!empty($user))?$user->gstin:'')}}" placeholder="GSTIN" tabindex="9">
                    @error('gstin')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label class="col-12">Status</label>
                    <input type="checkbox" name="status" id="status" class="form-control status" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" {{((!isset($user)) || (!empty($user->status)))? "checked" :"" }} tabindex="10">
                    @error('status')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>  
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group summernote-container">
                    <label>Footer Text</label>
                    <textarea name="footer_text" class="form-control summernote" tabindex="5">{{ old('footer_text',(!empty($user))?$user->footer_text:'') }}</textarea>                    
                </div>
            </div>         
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" tabindex="11">Submit</button>
            </div>
        </div>
    </form>
    </div>
    <!-- /.card-body -->
</div>
@endsection
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
@section('plugins.summernote', true)
@section('js')
<script>
    function phonecode(){
    console.log("Triger has been executed");
    if($('#country').val() != ''){
        $.ajax({
            url: "{{route('get_value')}}",
            type: 'POST',
            data: {
                table: 'countries',
                fields: 'phonecode',
                where: `id=${$('#country').val()}`
            },
            success: function(res) {
                $.each(JSON.parse(res), function(key, val) {
                    $(`#countryCode`).html(val.phonecode);
                })
            } 
        });
    }
    else{
        $(`#countryCode`).html('');
    }
}
$("document").ready(function(){
    setTimeout(function(){
        phonecode();
    }, 500);
    
});
jQuery(document).ready(function(){

    $('.state').select2();
    $('.country').on('change', function() {
        $(`.state`).empty().append(new Option()).trigger('change');
            $('.state').select2({
                placeholder: "Select",
                allowClear: true,
            });
            $(`.city`).empty().append(new Option()).trigger('change');
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
            });
        select2state();      
    });
    $('.summernote').summernote({
            height: 200,
            toolbar: [
                ["history", ["undo", "redo"]],
                ["style", ["style"]],
                ["font", ["bold", "italic", "underline", "fontname", "strikethrough", "superscript", "subscript", "clear"]],
                ["color", ["forecolor", "backcolor", "color"]],
                ["paragraph", ["ul", "ol", "paragraph", "height"]],
                ["table", ["table"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["codeview"]],
            ],

    });
    if ($('#country').val() || $('#state').val()) {
        select2state();
    }

    function select2state(){
        if ($('.country').val()) {
            
            $('.state').select2({
                placeholder: "Select",
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: "{{route('get_value')}}",
                    type: 'POST',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            table: 'states',
                            page: params.page,
                            where: `country_id=${$('#country').val()}`
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });
        }else {
            $(`.state`).empty().append(new Option()).trigger('change');
            $('.state').select2({
                placeholder: "Select",
                allowClear: true,
            });
        }
    }

    // $('.state').select2({
    //     placeholder: "Select",
    //     allowClear: true,
    //     minimumInputLength: 2,
    //     ajax: {
    //         url: "{{route('get_value')}}",
    //         type: 'POST',
    //         dataType: 'json',
    //         data: function (params) {
    //             return {
    //                 q: params.term, // search term
    //                 table:'states',
    //                 where: "country_id={{$country}}"
    //             };
    //         },
    //         processResults: function (data) {
    //             return {
    //                 results: data
    //             };
    //         }
    //     }
    // }).on('select2:clear',function(){
    //     select2city();
    // });
    $('.city').select2();
    $('.state').on('change', function() {
        $(`.city`).empty().append(new Option()).trigger('change');
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
            });
        select2city();
    });
    if ($('#state').val() || $('#city').val()) {
        select2city();
    }

    function select2city(){
        if ($('.state').val()) {
           
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: "{{route('get_value')}}",
                    type: 'POST',
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
        }else {
            $(`.city`).empty().append(new Option()).trigger('change');
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
            });
        }
    }

    function getStateCity(table,where,eleClass) {
        if($('#state_id').val() || $('#city_id').val()){
            $.ajax({
                url: "{{route('get_value')}}",
                type: 'POST',
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
