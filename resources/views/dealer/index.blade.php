@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">{{ucwords($title)}}</h3>
                            <div class="float-right"><a href="{{route('dealer.create')}}" class="btn btn-primary btn-md">Add Dealer</a></div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th data-name="">City</th>
                                        <th>State</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- modal -->
<div class="modal fade overflow-auto" id="productModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="cartTitle">Products</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection
@section('plugins.Datatables', true)
@section('plugins.BootstrapSwitch', true)
@section('plugins.tempusdominus', true)
@section('js')
<script>
    jQuery(document).ready(function(){
        $('#example').DataTable({
            "columns": [
                { "name": "id" },
                { "name": "name" },
                { "name": "email" },
                { "name": "mobile" },
                { "name": "states.name" },
                { "name": "cities.name" },
                { "name": "status" },
                { "name": "action" }
            ],
            "columnDefs": [
            {
                "targets": [ 6 ],
                "orderable": false
            },
            {
                "targets":[ 3 ],
                "className": "text-center",
            },
            {
                "targets": [ 7 ],
                "orderable": false
            },
            {
                "targets": [ 0 ],
                "visible": false
            },
        ],
            order: [[0, 'desc']],
            "processing": true,
            "serverSide": true,
            "ajax": dataTableRequest("{{ route('dealerlistajax') }}"),
            "fnDrawCallback": function() {
                $("input[data-bootstrap-switch]").each(function(){
                    $(this).bootstrapSwitch();
                });

                $('.change-status').on('switchChange.bootstrapSwitch', function (e, data) {
                    var status = ($(this).bootstrapSwitch('state'))?1:0;
                    var id = $(this).data('id');
                    $.ajax({
                        url:"{{route('status.update')}}",
                        method: "POST",
                        data: {'table':'users','id':id,'status':status},
                        dataType: 'json',
                    }).done(function(res){
                        if(res.code == 1){
                            // toastr.success(res.message);
                        }else{
                            // console.error(res.message);
                        }
                        //window.location.reload()
                    });
                });
            }
        });        
        $('body').on('click','.view-product',function(){
            var url = $(this).data('url');
            var data ={'user_id':$(this).data('product')};
            var token = $("meta[name='csrf-token']").attr("content");
            $.ajax({
            url: url,
            type: 'POST',
            data:{data,'_token':token},
            dataType: 'JSON',
            beforeSend : function(){
                $('#loader').show();
            },
            success: function(res) {
                if(res.code == 1){
                    $(`#productModal .modal-body`).html(res.data.cart_content);
                    $(`#productModal`).modal('show');
                }else{
                        Toast.fire({
                            type: 'error',
                            title: res.message
                        });
                        setTimeout(function(){
                            $(`#productModal`).modal('hide');
                        },1000);  
                    }
                // $('.product-detail input').not('input[type=checkbox]').attr('disabled',true);
            },
            complete:function(){
                $('#loader').hide();
            },
            error:function(xhr, ajaxOptions, thrownError){
                if(xhr.status==404) {
                    alert("Either From user or a To user for this, seems deleted, You can't edit it.");
                }
            }
            });
        });
    });
    $('body').on('blur','input[id^="discount_percentage_"]', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        var product_id = $(this).data('productid');
        var price = $("#price_"+product_id).val();
        var discount = $("#discount_percentage_"+product_id).val();
        if(discount != ""){
            var final_amount = price - ((price*discount)/100);
            $("#final_amount_"+product_id).val(final_amount);
        }
    });
    
    $('body').on('submit','#discountProducts',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            let url = "{{ route('dealer.addProductdiscount') }}";
            let formData  = $( this ).serialize();   
            saveFormData(url,formData).then(function(res){
                if(res.code == 1){
                  $(`#productModal`).modal('hide');
                }
                $('#example').DataTable().ajax.reload(null, false);
            });
        });
    function saveFormData(url,formData,modal=''){
            const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                        });
            return $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                beforeSend: function(){
                    $('#overlay').show();
                    $('.btn-save').attr('disabled',true);
                },
                complete: function(){
                    $('#overlay').hide();
                },
                success: function (data) {
                    if(data.code == 1){
                        Toast.fire({
                            type: 'success',
                            title: data.message
                        });
                        if(modal !== ''){
                            $(`#${modal}`).modal('hide');
                        }
                    }else{
                        Toast.fire({
                            type: 'error',
                            title: data.message
                        })
                    }
                },
                completed:function(){
                    $('.btn-save').attr('disabled',false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.btn-save').attr('disabled',false);
                    var err = JSON.parse(jqXHR.responseText).errors;
                    $.each(err,function(key,val){                
                        $(`[name=${key}]`).closest('.form-group').after(`<div class="alert alert-danger mt-2">${val}</div>`);                                        
                    });
                    Toast.fire({
                        type: 'error',
                        title: JSON.parse(jqXHR.responseText).message
                    });
                }
            });
    }
    function deleteUser(id){
            if(id){
                let data = confirm("Are you sure? you wants to delete this user?");
                if(data){
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax(
                    {
                        url: "{{ url('/admin/dealer/') }}/"+id,
                        type: 'DELETE',
                        data: {
                            "id": id,
                            "_token": token,
                        },
                        success: function (){
                            $('#example').DataTable().ajax.reload(null, false);
                        }
                    });
                }
            }
        }
</script>
@endsection