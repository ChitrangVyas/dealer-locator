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
                                <form action="{{route('poreportlistajax')}}" class="d-inline" id="exportFile" method="post">
                                    @csrf
                                    <input type="hidden" name="search[value]" id="dtSearch" value="" />
                                    <input type="hidden" name="export" id="dtExport" value="" />
                                    <input type="text" class="d-inline form-control form-control-sm col-2 datetimepicker-input from_date" id="from_date" name="from_date" data-toggle="datetimepicker" data-target="#from_date" autocomplete="off" value="" placeholder="From" required/>
                                    <input type="text" class="d-inline form-control form-control-sm col-2 datetimepicker-input to_date" id="to_date" name="to_date" data-toggle="datetimepicker" data-target="#to_date" autocomplete="off" value="" placeholder="To" required/>
                                    @dealer()
                                    <select class="d-inline form-control form-control-sm col-2" name="sent_recieved" id="sent_recieved">
                                        <option value="">All</option>
                                        <option value="sent">Sent</option>
                                        <option value="received">Recieved</option>
                                    </select>
                                    @enddealer
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
                                    <th>Order Number</th>
                                    <th>Total</th>
                                    <th>To</th>
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
                    "name": "orders.created_at"
                },
            ],
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false
                },
            ],
            order: [[0, 'desc']],
            "processing": true,
            "serverSide": true,
            "ajax":{
                "url": "{{route('poreportlistajax')}}",
                "type": "POST",
                "data": function ( d ) {
                    d._token = $("meta[name='csrf-token']").attr("content");
                    d.from_date = $("#from_date").val();
                    d.to_date = $("#to_date").val();
                    d.sent_recieved = $("#sent_recieved").val();
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