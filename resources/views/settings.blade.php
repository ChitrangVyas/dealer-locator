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
                <div class="form-group col-sm-12 col-md-10">
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
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group col-md-6">
                    <label>Timezone</label>
                    <select class="form-control select2 timezone" id='timezone' name='timezone' tabindex="3">
                        <option value="">Select</option>
                        @foreach ($timezone_list as $timezone)
                            <option value="{{$timezone['zone']}}" {{(!empty($settings) && !empty($settings->json_data['timezone']) && ($timezone['zone']==$settings->json_data['timezone']))?'selected':''}} >{{$timezone['zone']}}</option>
                        @endforeach
                    </select>
                    @error('timezone')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
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
                    <input type="text" class="form-control" name="name" value="{{ old('name',(!empty($settings->json_data['name']))?$settings->json_data['name']:'')}}" placeholder="Name" tabindex="4">
                    @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Email </label>
                    <input type="text" class="form-control" name="email" value="{{ old('email',(!empty($settings) && !empty($settings->json_data['email']))?$settings->json_data['email']:'') }}" placeholder="Email" autocomplete="false" tabindex="5"> 
                    @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" name="mobile" value="{{ old('mobile',(!empty($settings) && !empty($settings->json_data['mobile']))?$settings->json_data['mobile']:'') }}" class="form-control" placeholder="Mobile" tabindex="6">
                    @error('mobile')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" name="address" rows="1" placeholder="Address" tabindex="7">{{ old('address',(!empty($settings) && !empty($settings->json_data['address']))?$settings->json_data['address']:'') }}</textarea>
                    @error('address')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Country</label>
                    <select class="form-control select2 country" id='country' name='country' tabindex="8">
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
                    <select class="form-control select2 state" id='state' name='state' tabindex="9">
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
                    <select class="form-control select2 city" id='city' name='city' tabindex="10">
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
                    <input type="text" name="zipcode" value="{{ old('zipcode',(!empty($settings) && !empty($settings->json_data['zipcode']))?$settings->json_data['zipcode']:'') }}" class="form-control" placeholder="Enter zipcode" tabindex="11">
                    @error('zipcode')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>GSTIN</label>
                    <input type="text" class="form-control" name="gstin" value="{{ old('gstin',(!empty($settings) && (!empty($settings->json_data['gstin'])))?$settings->json_data['gstin']:'')}}" placeholder="GSTIN" tabindex="8">
                    @error('gstin')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Allowed countries to sale</label>
                    <select class="form-control select2 allowedcountry" id='allowed_countries' name='allowed_countries[]' tabindex="8" multiple="multiple">
                        <option value="">Select</option>
                        @if(!empty($settings) && (!empty($settings->json_data['allowed_countries'])))
                        @foreach($settings->json_data['allowed_countries'] as $allowedcountrys)     
                        @php 
                                $selectedCountry = $select2Old->getSelect2Data('countries',$allowedcountrys)
                            @endphp
                            <option value="{{$allowedcountrys}}" selected>{{$selectedCountry->name}}</option>
                            @endforeach
                            @endif
                        @if(old('allowed_countries'))
                            @foreach(old('allowed_countries') as $oldcountrys)     
                            @php 
                                $data = $select2Old->getSelect2Data('countries',$oldcountrys)
                            @endphp
                                               
                            <option value="{{$data->id}}" selected>{{$data->name}}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('allowed_countries')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label>Terms and Conditions for sale</label>
                    <textarea class="form-control" name="terms_condition_sale" placeholder="Terms and Conditions for Sale">{{ old('terms_condition_sale',(!empty($settings) && (!empty($settings->json_data['terms_condition_sale'])))?$settings->json_data['terms_condition_sale']:'')}}</textarea>
                   
                    @error('terms_condition_sale')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label>Terms and Conditions for PO</label>
                    <textarea class="form-control" name="terms_condition_po" placeholder="Terms and Conditions for PO">{{ old('terms_condition_po',(!empty($settings) && (!empty($settings->json_data['terms_condition_po'])))?$settings->json_data['terms_condition_po']:'')}}</textarea>
                    @error('terms_condition_po')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label>Terms and Conditions for Quote</label>
                    <textarea class="form-control" name="terms_condition_quote" placeholder="Terms and Conditions for Quote">{{ old('terms_condition_quote',(!empty($settings) && (!empty($settings->json_data['terms_condition_quote'])))?$settings->json_data['terms_condition_quote']:'')}}</textarea>
                    @error('terms_condition_quote')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 border-bottom mt-2 mb-2">
                    <div class="col-6">
                        <h4 class="text-muted">SMTP Settings</h4>
                    </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mailer</label>
                    <select name="mail_mailer" id="mail_mailer" class="form-control" tabindex="12">
                        <option value="sendmail" {{ (old('mail_mailer') == 'sendmail'||(!empty($settings->json_data['mail_mailer']) && $settings->json_data['mail_mailer'] == 'mail')) ?'selected':''}}>Default</option>
                        <option value="smtp" {{ (old('mail_mailer') == 'smtp'||(!empty($settings->json_data['mail_mailer']) && $settings->json_data['mail_mailer'] == 'smtp')) ?'selected':''}}>SMTP</option>
                        <option value="pop" {{ (old('mail_mailer') == 'pop' ||(!empty($settings->json_data['mail_mailer']) && $settings->json_data['mail_mailer']== 'pop')) ?'selected':''}}>POP</option>
                    </select>
                    @error('mail_mailer')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>User Name</label>
                    <input type="text" class="form-control" name="mail_username" value="{{ old('mail_username',(!empty($settings->json_data['mail_username']))?$settings->json_data['mail_username']:'')}}" placeholder="User Name" tabindex="13">
                    @error('mail_username')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="mail_password" name="mail_password" value="{{ old('mail_password',(!empty($settings->json_data['mail_password']))?$settings->json_data['mail_password']:'')}}" placeholder="Password" tabindex="14">
                    <span toggle="#mail_password" class="fa fa-fw fa-eye eye-icon toggle-password"></span>
                    @error('mail_password')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mail Host</label>
                    <input type="text" class="form-control" name="mail_host" value="{{ old('mail_host',(!empty($settings->json_data['mail_host']))?$settings->json_data['mail_host']:'')}}" placeholder="Mail Host" tabindex="15">
                    @error('mail_server')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mail Port</label>
                    <input type="text" class="form-control" name="mail_port" value="{{ old('mail_port',(!empty($settings->json_data['mail_port']))?$settings->json_data['mail_port']:'')}}" placeholder="Mail Port" tabindex="16">
                    @error('mail_port')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mail Encryption</label>
                    <!-- <input type="text" class="form-control" name="mail_encryption" value="{{ old('mail_encryption',(!empty($settings->json_data['mail_encryption']))?$settings->json_data['mail_encryption']:'')}}" placeholder="Mail Encryption" tabindex="2"> -->
                    <select name="mail_encryption" id="mail_encryption" class="form-control" tabindex="17">
                        <option value="null" {{ (old('mail_encryption') == 'null'||(!empty($settings->json_data['mail_encryption']) && $settings->json_data['mail_encryption'] == 'null')) ?'selected':''}}>Default</option>
                        <option value="ssl" {{ (old('mail_encryption') == 'ssl'||(!empty($settings->json_data['mail_encryption']) && $settings->json_data['mail_encryption'] == 'ssl')) ?'selected':''}}>SSL</option>
                        <option value="tls" {{ (old('mail_encryption') == 'tls' ||(!empty($settings->json_data['mail_encryption']) && $settings->json_data['mail_encryption'] == 'tls')) ?'selected':''}}>TLS</option>
                    </select>
                    @error('mail_encryption')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mail From Email</label>
                    <input type="text" class="form-control" name="mail_from_email" value="{{ old('mail_from_email',(!empty($settings->json_data['mail_from_email']))?$settings->json_data['mail_from_email']:'')}}" placeholder="Mail From Name" tabindex="18">
                    @error('mail_from_email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mail From Name</label>
                    <input type="text" class="form-control" name="mail_from_name" value="{{ old('mail_from_name',(!empty($settings->json_data['mail_from_name']))?$settings->json_data['mail_from_name']:'')}}" placeholder="Mail From Name" tabindex="19">
                    @error('mail_from_name')
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
