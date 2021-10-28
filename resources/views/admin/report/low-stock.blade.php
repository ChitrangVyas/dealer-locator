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
                        <div class="float-right">
                            <button type="button" class="btn btn-primary btn-sm" id="export">Export</button>
                        </div>
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
<form id="ExportFile" action="{{route('lowstockreportlistajax')}}" method="post">
    @csrf
    <input type="hidden" name="search[value]" id="dtSearch" value="" />
    <input type="hidden" name="export" id="dtExport" value="" />
</form>
@endsection
@section('plugins.Datatables', true)
@section('js')
<script>
    jQuery(document).ready(function(){
        var table = $('#example').DataTable({
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
            "ajax": {
                "url":"{{ route('lowstockreportlistajax') }}",
                "type": "POST",
                "data": function ( d ) {
                    d._token = $("meta[name='csrf-token']").attr("content");
                    d.export = $('#export').val();
                }
            },                                                              
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
        $('body').on('click',"#export",function(){
            $('#dtExport').val('export');
            $('#dtSearch').val(table.search());
            $("#ExportFile").submit();
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
</script>
@endsection