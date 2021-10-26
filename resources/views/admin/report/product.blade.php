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
                        @admin
                        <!-- <div class="float-right"><a href="{{route('product.create')}}" class="btn btn-primary btn-md">Add {{ucwords($title)}}</a></div> -->
                        @endadmin
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Image</th>
                                    <th>Inventory</th>
                                    <th>Status</th>                                    
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
<div class="modal fade" id="productModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
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
@section('plugins.Select2', true)
@section('plugins.Sweetalert2', true)
@section('plugins.BootstrapSwitch', true)
@section('js')
<script>
    jQuery(document).ready(function(){
        var table = $('#example').DataTable({
            buttons: [
                {
                    text: 'Reload',
                    action: function ( e, dt, node, config ) {
                        // dt.ajax.reload();
                    }
                }
            ],
            "columns": [
                { "name": "products.id" },
                { "name": "products.name" },
                { "name": "brands.name" },
                { "name": "image" },
                { "name": "inventory" },
                { "name": "products.status" }
            ],
            "columnDefs": [
            {
                "targets": [ 3 ],
                "orderable": false
            },
            {
                "targets": [ 5 ],
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
            "ajax": dataTableRequest("{{ route('productreportlistajax') }}"),                                                              
            "fnDrawCallback":function() {
                let is_admin = "{{((empty(auth()->user()->users_group_id)) || auth()->user()->users_group_id == 1)? true : false }}";
                $("input[data-bootstrap-switch]").each(function(){
                    $(this).bootstrapSwitch();
                });

                $('.change-status').on('switchChange.bootstrapSwitch', function (e, data) {
                    var status = ($(this).bootstrapSwitch('state'))?1:0;
                    var id = $(this).data('id');
                    $.ajax({
                        url:"{{route('status.update')}}",
                        method: "POST",
                        data: {'table':'products','id':id,'status':status},
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
    });

    $('body').on('click','.add-stock',function(e){
        getData("{{route('product.inventory')}}",{'product_id':$(this).data('product')},'productModal','productModal .modal-title').then(function(res){
            $('#productModal').modal('show');
        });
    });

    $('body').on('submit','#frmAddStock',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();        
        saveFormData("{{route('product.inventory.update')}}",$('#frmAddStock').serializeArray(),'productModal').then(function(res){
            $('#example').DataTable().ajax.reload(null, false);
        });

    });

    function getData(url,data,modal='',ele_class=''){
        var token = $("meta[name='csrf-token']").attr("content");
        return $.ajax({
            url: url,
            type: 'POST',
            data:{data,'_token':token},
            beforeSend : function(){
                $('#loader').show();
            },
            success: function(res) {
                if(modal !== ''){
                    $(`#${modal} .modal-body`).html(res.data.modal_content);
                }
                if(ele_class !== ''){
                    $(`#${ele_class}`).text(res.data.modal_title);
                }
                // $('.product-detail input').not('input[type=checkbox]').attr('disabled',true);
            },
            complete:function(){
                $('#loader').hide();
            }
        }); 
    }

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
                dataType: 'JSON',
                beforeSend: function(){
                    $('.btn-save').attr('disabled',true);
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
                    Toast.fire({
                        type: 'error',
                        title: JSON.parse(jqXHR.responseText).message
                    });
                    $.each(err,function(key,val){
                        var input_field = key.split('.');
                        if($(`[name=${input_field[0]}]`).length > 0){
                            $(`[name=${input_field[0]}]`).closest('.form-group').after(`<div class="alert alert-danger mt-2">${val}</div>`);                                        
                        }
                    });
                }
            });
    }
    function deleteUser(id){
            if(id){
                let data = confirm("Are you sure? you wants to delete this Product?");
                if(data){
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax(
                    {
                        url: "{{ url('/admin/product/') }}/"+id,
                        type: 'DELETE',
                        data: {
                            "id": id,
                            "_token": token,
                        },
                        success: function (){
                            window.location.reload();
                        }
                    });
                }
            }
        }
</script>
@endsection