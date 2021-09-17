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
                            <div class="float-right"><a href="{{route('category.create')}}" class="btn btn-primary btn-md">Add {{ucwords($title)}}</a></div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Parent Category</th>
                                        <th>Images</th>
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
@section('js')
<script>
    jQuery(document).ready(function(){
        $('#example').DataTable({
            "columns": [
                { "name": "id" },
                { "name": "name" },
                { "name": "image" },
                { "name": "parent_category" },
                { "name": "action" }
            ],
            "columnDefs": [
                {
                "targets": [ 3 ],
                "orderable": false
            },
            {
                "targets": [ 4 ],
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
            "ajax": dataTableRequest("{{ route('categorylistajax') }}"),
        });        
    });
    function deleteUser(id){
            if(id){
                let data = confirm("Are you sure? you wants to delete this category?");
                if(data){
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax(
                    {
                        url: "{{ url('/admin/category/') }}/"+id,
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