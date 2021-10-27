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
                        <div class="row">
                            <div class="col-12">
                                <form action="{{route('inquiryreportlistajax')}}" class="d-inline" id="exportFile" method="post">
                                    @csrf
                                    <input type="hidden" name="search[value]" id="dtSearch" value="" />
                                    <input type="hidden" name="export" id="dtExport" value="" />
                                    <input type="text" class="d-inline form-control form-control-sm col-2 datetimepicker-input from_date" id="from_date" name="from_date" data-toggle="datetimepicker" data-target="#from_date" autocomplete="off" value="" placeholder="From" required/>
                                    <input type="text" class="d-inline form-control form-control-sm col-2 datetimepicker-input to_date" id="to_date" name="to_date" data-toggle="datetimepicker" data-target="#to_date" autocomplete="off" value="" placeholder="To" required/>
                                    <button type="button" class="d-inline btn btn-primary btn-sm form-control-sm col-1" id="filter" value="">Filter</button>
                                </form>
                                <div class="d-inline">
                                    <button type="button" class="btn btn-primary btn-sm form-control-sm col-1 float-right" id="export" value="">Export</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Source</th>
                                    <th>Dealer</th>
                                    <th>Retailer</th>
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
@endsection
@section('plugins.Datatables', true)
@section('plugins.tempusdominus', true)
@section('js')
<script>
    jQuery(document).ready(function() {
        var table =  $('#example').DataTable({
            "bAutoWidth": false,
            "columns": [{
                    "name": "inquiries.id"
                },
                {
                    "name": "product"
                },
                {
                    "name": "source"
                },
                {
                    "name": "c.name"
                },
                {
                    "name": "c.email"
                },
                {
                    "name": "c.phone"
                },
                {
                    "name": "d.name"
                },
                {
                    "name": "r.name"
                },
                {
                    "name": "status",sWidth: '10%'
                },
            ],
            "columnDefs": [{
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
            "ajax":{
                "url": "{{route('inquiryreportlistajax')}}",
                "type": "POST",
                "data": function ( d ) {
                    d._token = $("meta[name='csrf-token']").attr("content");
                    d.from_date = $("#from_date").val();
                    d.to_date = $("#to_date").val();
                    d.inquiry_source = $("#inquiry_source").val();
                    d.dealer = $("#dealer").val();
                }
            },
            "fnDrawCallback":function() {
                let is_admin = "{{((empty(auth()->user()->users_group_id)) || auth()->user()->users_group_id == 1 ||auth()->user()->users_group_id == 2 )? true : false }}";
                let is_dealer = "{{auth()->user()->users_group_id == 2}}";
                if(is_admin){
                    table.column(5).visible();
                }else{
                    table.column(5).visible(false);
                }
                if(is_dealer){
                    table.column(4).visible(false);
                }else{
                    table.column(4).visible();
                }
                
            }
        });
        $('.datetimepicker-input').datetimepicker({
            format: 'YYYY-MM-DD',
        });

        $(window).keydown(function(event){
            if(event.keyCode == 13) {
            event.preventDefault();
            return false;
            }
        });

        $('body').on('click',"#filter",function(){
            $('#example').DataTable().ajax.reload();
        });

        $('body').on('click',"#export",function(){
            $('#dtExport').val('export');
            $('#dtSearch').val(table.search());
            $("#exportFile").submit();
        });
    });
</script>
@endsection