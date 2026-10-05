@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">Settings</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">     
        <form method="POST" action="{{ route('settings.index') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($settings) && !empty($settings->id))?$settings->id:'' }}">
        <div class="row">
            <div class="col-sm-12 col-md-12 border-bottom mt-2 mb-2">
                    <div class="col-6">
                        <h4 class="text-muted">Site Settings</h4>
                    </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group col-md-6">
                    <label>Site Name</label>
                    <input type="text" class="form-control" name="site_name" value="{{ old('site_name',(!empty($settings->json_data['site_name']))?$settings->json_data['site_name']:'')}}" placeholder="Site Name" tabindex="1">
                    @error('site_name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <!-- file input -->
                <div class="form-group col-sm-12 col-md-10">
                <label>Logo</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input restrictFileUpload" id="logo" name="logo" tabindex="2">
                        <label class="custom-file-label" for="logo">Choose file</label>
                    </div>
                </div>
                @error('logo')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                </div>
                @if(!empty($settings->json_data['logo']))
                <div class="col-sm-12 col-md-6">
                    <img class='img logo mt-3 mb-4' src="{{ url($settings->json_data['logo']) }}" width=100 />
                </div>
                @endif
            </div>
            <div class="col-sm-12 col-md-6">
                <!-- file input -->
                <div class="form-group col-sm-12 col-md-12">
                <label>Icon Logo</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input restrictFileUpload" id="icon_logo" name="icon_logo" tabindex="2">
                        <label class="custom-file-label" for="icon_logo">Choose file</label>
                    </div>
                </div>
                @error('icon_logo')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                </div>
                @if(!empty($settings->json_data['icon_logo']))
                <div class="col-sm-12 col-md-6">
                    <img class='img logo mt-3 mb-4 border' src="{{ url($settings->json_data['icon_logo']) }}" width=100 />
                </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 border-bottom mt-2 mb-2">
                    <div class="col-6">
                        <h4 class="text-muted">Profile Settings</h4>
                    </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name',(!empty($settings->json_data['name']))?$settings->json_data['name']:'')}}" placeholder="Name" tabindex="6">
                    @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Email </label>
                    <input type="text" class="form-control" name="email" value="{{ old('email',(!empty($settings) && !empty($settings->json_data['email']))?$settings->json_data['email']:'') }}" placeholder="Email" autocomplete="false" tabindex="7"> 
                    @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" name="mobile" value="{{ old('mobile',(!empty($settings) && !empty($settings->json_data['mobile']))?$settings->json_data['mobile']:'') }}" class="form-control" placeholder="Mobile" tabindex="8">
                    @error('mobile')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" name="address" rows="1" placeholder="Address" tabindex="9">{{ old('address',(!empty($settings) && !empty($settings->json_data['address']))?$settings->json_data['address']:'') }}</textarea>
                    @error('address')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Country</label>
                    <select class="form-control select2 country" id='country' name='country' tabindex="10">
                        <option value="">Select</option>
                        @if(!empty($settings) && (!empty($settings->json_data['country'])))
                            @php 
                                $selectedCountry = $select2Old->getSelect2Data('countries',$settings->json_data['country'])
                            @endphp
                            <option value="{{$settings->json_data['country']}}" selected>{{$selectedCountry->name}}</option>
                        @endif
                        @if(old('country'))     
                            @php 
                                $data = $select2Old->getSelect2Data('countries',old('country'))
                            @endphp                   
                            <option value="{{$data->id}}" selected>{{$data->name}}</option>
                        @endif
                    </select>
                    @error('country')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>State</label>
                    <select class="form-control select2 state" id='state' name='state' tabindex="11">
                        <option value="">Select</option>
                        @if(!empty($settings) && (!empty($settings->json_data['state'])))
                            @php 
                                $state = $select2Old->getSelect2Data('states',$settings->json_data['state'])
                            @endphp
                            <option value="{{$settings->json_data['state']}}" selected>{{$state->name}}</option>
                        @endif
                        @if(old('state'))     
                            @php 
                                $data = $select2Old->getSelect2Data('states',old('state'))
                            @endphp                   
                            <option value="{{$data->id}}" selected>{{$data->name}}</option>
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
                    <select class="form-control select2 city" id='city' name='city' tabindex="12">
                        <option value="">Select</option>
                        @if(!empty($settings) && (!empty($settings->json_data['city'])))
                            @php 
                                $city = $select2Old->getSelect2Data('cities',$settings->json_data['city'])
                            @endphp
                            <option value="{{$settings->json_data['city']}}" selected>{{$city->name}}</option>
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
                    @error('city')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Zip code</label>
                    <input type="text" name="zipcode" value="{{ old('zipcode',(!empty($settings) && !empty($settings->json_data['zipcode']))?$settings->json_data['zipcode']:'') }}" class="form-control" placeholder="Enter zipcode" tabindex="13">
                    @error('zipcode')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>GSTIN</label>
                    <input type="text" class="form-control" name="gstin" value="{{ old('gstin',(!empty($settings) && (!empty($settings->json_data['gstin'])))?$settings->json_data['gstin']:'')}}" placeholder="GSTIN" tabindex="14">
                    @error('gstin')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 border-bottom mt-2 mb-2">
                    <div class="col-6">
                        <h4 class="text-muted">Google Map Settings</h4>
                    </div>
            </div>
            <div class="col-sm-12 col-md-4 mail_more_info">
                <!-- text input -->
                <div class="form-group">
                    <label>Google Map API Key</label>
                    <input type="text" class="form-control" name="google_map_api_key" value="{{ old('google_map_api_key',(!empty($settings->json_data['google_map_api_key']))?$settings->json_data['google_map_api_key']:'')}}" placeholder="Google Map API" tabindex="13">
                    @error('google_map_api_key')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4 mail_more_info">
                <!-- text input -->
                <div class="form-group">
                    <label>Google Map Radius</label>
                    <input type="text" class="form-control" id="google_map_radius" name="google_map_radius" value="{{ old('google_map_radius',(!empty($settings->json_data['google_map_radius']))?$settings->json_data['google_map_radius']:'')}}" placeholder="Google Map Radius" tabindex="14">
                    @error('google_map_radius')
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
    $("#mail_mailer").change(function(){
        if($(this).val()=="sendmail"){
            $(".mail_more_info").hide();
        }else{
            $(".mail_more_info").show();
        }        
    });
    $("#mail_mailer").trigger('change');

    //timezone
    $('.timezone').select2({
        placeholder: "Select Timezone",
        allowClear: true,
    });
    $('.country').select2({
        placeholder: "Select Country",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "{{route('get_value')}}",
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term, // search term
                    table:'countries',
                    where: ''
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    }); 
    $('.allowedcountry').select2({
        placeholder: "Select Country",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "{{route('get_value')}}",
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term, // search term
                    table:'countries',
                    where: ''
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    });
    select2state();
    $('.city').select2();

    $('.country').on('change',function(){
        select2state();
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
    function select2state(){
        let country =   "{{$country}}";
        if($('#country').val() != "{{$country}}"){
            country = $('#country').val();
            $('.state').val('').trigger('change');
            $('.city').val('').trigger('change');
        }

        $('.state').select2({
            placeholder: "Select",
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: "{{route('get_value')}}",
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        table:'states',
                        where: "country_id = " + country
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

    function select2city(){
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
