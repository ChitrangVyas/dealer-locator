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
                            <div class="float-right"><a href="{{route('retailer.create')}}" class="btn btn-primary btn-md">Add Retailer</a></div>
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
                                        <th>Satus</th>
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
            "ajax": dataTableRequest("{{ route('retailerlistajax') }}"),
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
    });
    function deleteUser(id){
            if(id){
                let data = confirm("Are you sure? you wants to delete this user?");
                if(data){
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax(
                    {
                        url: "{{ url('/admin/retailer/') }}/"+id,
                        type: 'DELETE',
                        data: {
                            "id": id,
                            "_token": token,
                        },
                        success: function (){
                            window.location.reload;
                        }
                    });
                }
            }
        }
</script>
@endsection