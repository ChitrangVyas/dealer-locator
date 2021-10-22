<div class="card card-primary">
    <div class="card-body">     
        @if(!empty($user))
            <form method="POST" id="userForm">
            @method('PUT')
            <input type="hidden" name="updated_by" value="{{auth()->user()->id}}">
        @endif
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($user))?$user->id:'' }}">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label>Name <span class="required_field">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ (!empty($user))?$user->name:'' }}" placeholder="Full Name" tabindex="21">
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label>Mobile <span class="required_field">*</span></label>
                    <input type="text" name="mobile" value="{{ (!empty($user))?$user->mobile:'' }}" class="form-control" placeholder="Mobile" tabindex="23">
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label>Address <span class="required_field">*</span></label>
                    <textarea class="form-control" name="address" rows="1" placeholder="Address" tabindex="24">{{ (!empty($user))?$user->address:'' }}</textarea>
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label>GSTIN</label>
                    <input type="text" class="form-control" name="gstin" value="{{ old('gstin',(!empty($user))?$user->gstin:'')}}" placeholder="GSTIN" tabindex="28">
                    @error('gstin')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" tabindex="29">Submit</button>
            </div>
        </div>
    </form>
    </div>
    <!-- /.card-body -->
</div>
<script>
jQuery(document).ready(function(){
    $('.required_field').addClass('text-danger');
    $('body').on('submit','#userForm',function(e){
        e.preventDefault();
        e.stopImmediatePropagation;
        $("#userForm .alert").remove();      
        $.ajax({
            url: "{{route('profile.update')}}",
            type: 'POST',
            data:$('#userForm').serialize(),
            success: function(res) {
                if(res != '' && res.code == "1"){
                    Toast.fire({
                        type: 'success',
                        title: res.data,
                    });
                    $('#profileModal').modal('hide');
                }else{
                    Toast.fire({
                        type: 'error',
                        title: res.data,
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                Toast.fire({
                    type: 'error',
                    title: JSON.parse(jqXHR.responseText).message
                });
                var err = JSON.parse(jqXHR.responseText).errors;
                $.each(err,function(key,val){                
                    $(`[name=${key}]`).closest('.form-group .form-control').after(`<div class="alert alert-danger mt-2">${val}</div>`);                                        
                });
            }
        });
    });
    $("body").on('focusout',"input,select,textarea",function(e){      
      if($(this).val()!=""){
          console.log('somethings');    
        // $(this).parent().find(".alert").hide();        
        $(this).parent().find('.alert').hide();
        $(this).parent().parent('.form-group').find('.alert').hide();
      }else{   
        $(this).parent('.form-group').find('.alert').show();
        $(this).parent().parent('.form-group').find('.alert').show();
        // $(this).parent().find(".alert").show();
      }
    });
});
</script>