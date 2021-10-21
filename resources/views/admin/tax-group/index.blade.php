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
                            <div class="float-right"><a href="{{route('tax-group.create')}}" class="btn btn-primary btn-md">Add {{ucwords($title)}}</a></div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
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
@endsection
@section('plugins.Datatables', true)
@section('plugins.BootstrapSwitch', true)
@section('js')
<script>
    jQuery(document).ready(function(){
        $('#example').DataTable({
            "columns": [
                { "name": "id" },
                { "name": "name" },
                { "name": "status" },
                { "name": "action" }
            ],
            "columnDefs": [
            {
                "targets": [ 2 ],
                "orderable": false
            },
            {
                "targets": [ 3 ],
                "orderable": false
            },
            {
                "targets": [ 0 ],
                "visible": false
            },
        ],
            "processing": true,
            "serverSide": true,
            "ajax": dataTableRequest("{{ route('taxgrouplistajax') }}"),
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
                        data: {'table':'tax_groups','id':id,'status':status},
                        dataType: 'json',
                        error: function (jqXHR, textStatus, errorThrown) {
                            Toast.fire({
                                type: 'error',
                                title: JSON.parse(jqXHR.responseText).message
                            });
                        }
                    }).done(function(res){
                        if(res.code == 1){
                            Toast.fire({
                                type: 'success',
                                title: res.data,
                            });
                        }else{
                            Toast.fire({
                                type: 'error',
                                title: res.message
                            });
                            // console.error(res.message);
                        }
                        //window.location.reload()
                    });
                });
            }
        });        
    });
    function deleteItem(id){
            if(id){
                let data = confirm("Are you sure? you wants to delete this Tax Group?");
                if(data){
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax(
                    {
                        url: "{{ url('/admin/tax-group') }}/"+id,
                        type: 'DELETE',
                        data: {
                            "id": id,
                            "_token": token,
                        },
                        success: function (res){
                            if(res.code == 1){
                                $('#example').DataTable().ajax.reload(null, false);
                            }else{
                                Toast.fire({
                                type: 'error',
                                title: res.error
                            });
                            }
                        }
                    });
                }
            }
        }
</script>
@endsection