<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">{{ ($vendor)? 'Vendor' :$title}}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">     
        @if(!empty($user))
            <form method="POST" id="customerForm">
            @method('PUT')
            <input type="hidden" name="updated_by" value="{{auth()->user()->id}}">
        @else
            <form method="POST" id="customerForm">
            <input type="hidden" name="created_by" value="{{auth()->user()->id}}">
        @endif
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($user))?$user->id:'' }}">
        @if (!empty($vendor))
            <input type="hidden" name="users_group_id" value="5">
        @else
            <input type="hidden" name="users_group_id" value="{{(!empty($user) && $user->users_group_id==4) ? $user->users_group_id : '4'}}">
        @endif

        <div class="row">
            <!-- <div class="col-sm-12 col-md-12">
                <div class="form-group">
                    <div class="form-check d-inline">
                          <input class="form-check-input" type="radio" name="users_group_id"  {{ old('users_group_id',(!empty($user) && $user->users_group_id==4)) ? 'checked' : '' }}  value="4">
                          <label class="form-check-label">Customer</label>
                    </div>
                    <div class="form-check d-inline ml-3">
                          <input class="form-check-input" type="radio" name="users_group_id"  {{ (!empty($user) && $user->users_group_id==5) ? 'checked' : '' }}  value="5">
                          <label class="form-check-label">Vendor</label>
                    </div>
                    
                </div>
            </div> -->
            
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ (!empty($user))?$user->name:'' }}" placeholder="Full Name">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="{{ (!empty($user))?$user->email:'' }}" placeholder="Email" autocomplete="false">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" name="mobile" value="{{ (!empty($user))?$user->mobile:'' }}" class="form-control" placeholder="Mobile">
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" name="address" rows="1" placeholder="Address">{{ (!empty($user))?$user->address:'' }}</textarea>
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
                    </select>
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
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Zipcode</label>
                    <input type="text" name="zipcode" value="{{ (!empty($user))?$user->zipcode:'' }}" class="form-control" placeholder="Enter zipcode">
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

<script>
jQuery(document).ready(function(){
    $('.dealer').select2({
        placeholder: "Select",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "{{route('get_value')}}",
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
    });
    $('.state').select2({
        placeholder: "Select",
        allowClear: true,
        minimumInputLength: 2,
        width:"100%",
        ajax: {
            url: "{{route('get_value')}}",
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term, // search term
                    table:'states',
                    where: 'country_id=101'
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    });
    $('.city').select2({width:"100%"});
    $('.state').on('change', function(){
        if($('.state').val()){
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
                minimumInputLength: 2,
                width:"100%",
                ajax: {
                    url: "{{route('get_value')}}",
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            table:'cities',
                            page: params.page,
                            where: `state_id=${$('#state').val()}`
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });
        }else{
            $('.city').select2({
                placeholder: "Select",
                allowClear: true,
            });
        }
    });
    $('body').on('submit','#customerForm',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $("#customerForm .alert").remove();
        let contact = ($("input[name=users_group_id]").val() == 5) ? 'vendor' : 'customer';
        $("#modal-lg").modal("hide");
        createCustomer((contact=='customer')?"{{ route('customer.store') }}":"{{ route('vendor.store') }}").then(function(data){
            $("#modal-lg").modal("hide");                
            if(contact == 'customer'){
                var newOption = new Option(data.name+" - " + data.zipcode + " - " + data.city.name, data.id, true, true);
            }else{
                var newOption = new Option(data.name, data.id, true, true);
            }
            $(`#${contact}`).append(newOption).trigger('change');
        });
    })

    function createCustomer(url){
       return $.ajax({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') },
            url: url,
            type: 'POST',            
            data: $('#customerForm').serialize(),
            success: function (data) { 
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var err = JSON.parse(jqXHR.responseText).errors;
                $.each(err,function(key,val){                
                    $(`[name=${key}]`).closest('.form-group').after(`<div class="alert alert-danger mt-2">${val}</div>`);                                        
                });
                    /* Toast.fire({
                        type: 'error',
                        title: JSON.parse(jqXHR.responseText).message
                    }); */
            }
        });

    }
    

});
</script>