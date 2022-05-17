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
                            <div class="col-10">
                                <form action="{{route('salesreportlistajax')}}" class="d-inline" id="exportFile" method="post">
                                    @csrf
                                    <input type="hidden" name="search[value]" id="dtSearch" value="" />
                                    <input type="hidden" name="export" id="dtExport" value="" />
                                    <div class="row">
                                        <div class="col-2">
                                            <label>Payment Status</label>
                                            <select name="status_id" class="form-control" id="order_status">
                                                <option value="">Select</option>
                                                <option value="1">Pending</option>
                                                <option value="2">Paid</option>
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <label>Order Status</label>
                                            <select name="stage" class="form-control" id="order_stage">
                                                <option value="">Select</option>
                                                <option value="0">Pending</option>
                                                <option value="1">Processing</option>
                                                <option value="2">Ready for Installation</option>
                                                <option value="3">Installed</option>
                                                <option value="4">Dispatched</option>
                                                <option value="5">Closed</option>
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <label>From Date</label>
                                            <input type="text" class="form-control form-control-sm  datetimepicker-input from_date" id="from_date" name="from_date" data-toggle="datetimepicker" data-target="#from_date" autocomplete="off" value="" placeholder="From" required/>
                                        </div>
                                        <div class="col-2">
                                            <label>To Date</label>
                                            <input type="text" class="form-control form-control-sm  datetimepicker-input to_date" id="to_date" name="to_date" data-toggle="datetimepicker" data-target="#to_date" autocomplete="off" value="" placeholder="To" required/>
                                        </div>
                                        <div class="col-2">
                                            <label></label>
                                            <button type="button" class=" btn btn-primary btn-md form-control-md " id="filter" value="">Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-2">
                                    <button type="button" class="btn btn-primary btn-md form-control-md  float-right" id="export" value="">Export</button>
                            </div>                            
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Order Number</th>
                                    <th>Total</th>
                                    <th>To</th>
                                    <th>Payment Status</th>
                                    <th>Order Status</th>
                                    <th>Created Date</th>
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
@section('plugins.Select2', true)
@section('plugins.tempusdominus', true)
@section('js')
<script>
    jQuery(document).ready(function() {
        var table = $('#example').DataTable({
            bAutoWidth:false,
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
                    "name": "c.name"
                },
                {
                    "name": "orders.status"
                },
                {
                    "name": "orders.status"
                },
                {
                    "name": "orders.created_at"
                },
            ],
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false
                },
                {
                    "targets": [5],
                    "orderable": false
                },
            ],
            order: [[0, 'desc']],
            "processing": true,
            "serverSide": true,
            "ajax":{
                "url": "{{route('salesreportlistajax')}}",
                "type": "POST",
                "data": function ( d ) {
                    d._token = $("meta[name='csrf-token']").attr("content");
                    d.from_date = $("#from_date").val();
                    d.to_date = $("#to_date").val();
                    d.order_stage = $("#order_stage").val();
                    d.order_status = $("#order_status").val();
                    d.dealer = $("#dealer").val();
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