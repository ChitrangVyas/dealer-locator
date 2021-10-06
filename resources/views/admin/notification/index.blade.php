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
                        <!-- <div class="card-header">
                            <h2 class="card-title">{{ucwords($title)}}</h3>
                        </div> -->
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Message</th>
                                        <th>Created At</th>
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
@section('js')
<script>
    jQuery(document).ready(function(){
        $('#example').DataTable({
            "columns": [
                { "name": "id" },
                { "name": "message" },
                { "name": "created_at" },
            ],
            "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false
            },
        ],
        order: [[0, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax": dataTableRequest("{{ route('notificationlistajax') }}"),
        });        
    });
</script>
@endsection