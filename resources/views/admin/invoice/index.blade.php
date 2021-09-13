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
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Order Number</th>
                                    <!-- <th>Product</th> -->
                                    <!-- <th>Qty</th> -->
                                    <th>Total</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
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
<div class="modal fade" id="orderModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="cartTitle"></h4>
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
<!-- modal -->
<div class="modal fade" id="invoiceModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle"></h4>
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
@section('plugins.tempusdominus', true)
@section('plugins.summernote', true)
@section('plugins.Sweetalert2', true)
@section('js')
<script>
    jQuery(document).ready(function() {
        $('#example').DataTable({
            "columns": [{
                    "name": "orders.id"
                },
                {
                    "name": "orders.order_number"
                },
                {
                    "name": "orders.total"
                },
                {
                    "name": "d.name"
                },
                {
                    "name": "c.name"
                },
                {
                    "name": "status"
                },
                {
                    "name": "orders.created_at"
                },
                {
                    "name": "action"
                }
            ],
            "columnDefs": [
                {
                    "targets": [5],
                    "orderable": false
                },
                {
                    "targets": [7],
                    "orderable": false
                },
                {
                    "targets": [0],
                    "visible": false
                },
            ],
            order: [[0, 'desc']],
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('list.invoice') }}",
        });
        $('.datetimepicker-input').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            date: moment()
        });
        $('.summernote').summernote({
            height: 150
        });

        $(window).keydown(function(event){
            if(event.keyCode == 13) {
            event.preventDefault();
            return false;
            }
        });
        $('body').on('click','.view-order',function(){
            var view_url = $(this).data('url');
            $(`#orderModal .modal-body`).html("");
            getOrderData(view_url,{'order_id':$(this).data('order')}).then(function(res){
                $('#order_status').select2({
                    width:"20%",
                });
            });
        });
        
        $('body').on('change','#order_status',function(){
            saveFormData("{{route('invoice.status.update')}}",$('#invoice').serializeArray(),'').then(function(res){
                $('#example').DataTable().ajax.reload(null, false);
            });
        });

        function getOrderData(url,data){
            var token = $("meta[name='csrf-token']").attr("content");
            return $.ajax({
                url: url,
                type: 'POST',
                data:{data,'_token':token},
                beforeSend : function(){
                    $('#loader').show();
                },
                success: function(res) {
                    $(`#orderModal .modal-body`).html(res.data.cart_content);
                    $('#cartTitle').text(res.data.cart_title);
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
    });

    
</script>
@endsection