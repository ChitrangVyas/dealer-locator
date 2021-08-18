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
                            <div class="float-right"><a href="{{route('inquiry.create')}}" class="btn btn-primary btn-md">Add Inquiry</a></div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Customer</th>
                                    <th>Source</th>
                                    <th>Dealer</th>
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
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Inquiry Log</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <div class="card card-primary card-outline card-tabs">
                            <div class="card-header p-0 pt-1 border-bottom-0">
                                <ul class="nav nav-tabs" id="tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab-view" data-tab="view" data-toggle="pill" href="#nav-tab-view" role="tab" aria-controls="nav-tab-view" aria-selected="true" onclick="activeTab(this)">View Log</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-add" data-tab="add" data-toggle="pill" href="#nav-tab-add" role="tab" aria-controls="nav-tab-add" aria-selected="false" onclick="activeTab(this)">Add Log</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="tab-content">
                                    <div class="tab-pane fade show active" id="nav-tab-view" data-tab="view" role="tabpanel" aria-labelledby="tab-view">
                                    </div>
                                    <div class="tab-pane fade" id="nav-tab-add" data-tab="add" role="tabpanel" aria-labelledby="tab-add">
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection
@section('plugins.Datatables', true)
@section('plugins.tempusdominus', true)
@section('plugins.summernote', true)
@section('plugins.Sweetalert2', true)
@section('js')
<script>
    jQuery(document).ready(function() {
        $('#example').DataTable({
            "columns": [{
                    "name": "inquiries.id"
                },
                {
                    "name": "source"
                },
                {
                    "name": "c.name"
                },
                {
                    "name": "p.name"
                },
                {
                    "name": "d.name"
                },
                {
                    "name": "action"
                }
            ],
            "columnDefs": [{
                    "targets": [5],
                    "orderable": false
                },
                {
                    "targets": [0],
                    "visible": false
                },
            ],
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('inquirylistajax') }}",
        });
        $('.datetimepicker-input').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            date: moment()
        });
        $('.summernote').summernote({
            height: 150
        });
        $('body').on('click','#addLog',function(){
            var inquiry_id = $(this).data('inquiry');
            var view_url = $(this).data('view-url');
            var add_url = $(this).data('add-url');
            console.log("Inquiry id",inquiry_id);
            getTabData(view_url,'view');
            getTabData(add_url,'add').then(function(){
                $('#inquiry_id').val(inquiry_id);
            });
            $('#modal-lg').attr("data-inquiry",inquiry_id);
        });
        $('#saveInquiryLog').on('submit', function(e){
            e.preventDefault();
            const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                        });
            $.ajax({
                url: "{{ route('inquiry-log.store') }}",
                type: 'POST',
                data: $('#saveInquiryLog').serialize(),
                dataType: 'JSON',
                success: function (data) {
                    if(data.code == 1){
                        Toast.fire({
                            type: 'success',
                            title: data.message
                        });
                        $('#modal-lg').modal('hide');
                    }else{
                        Toast.fire({
                            type: 'error',
                            title: data.message
                        })
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
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
        });
        $("body").on("click",".is-reminder",function(){
            isReminderShow();
        });

        $("body").on('shown.bs.modal',function(){
            var selectedTab = $(".modal-body .tab-content").find(".active");
            activeTab(selectedTab);
        });

    });
    function isReminderShow(){
        if (!$("#saveInquiryLog .is-reminder").is(":checked")) {
                $('.reminder').fadeOut("500");
                $('.reminder').val('');
            }else{
                $('.reminder').show("500");
            }
    }
    function activeTab(ele){
        let selectedTab = $(ele).data('tab');
    }
    function getTabData(url,loadTo){
         return $.ajax({
            url: url,
            type: 'GET',
            success: function(res) {
                $(`#nav-tab-${loadTo}`).html(res);
                $('.datetimepicker-input').datetimepicker({
                    format: 'YYYY-MM-DD HH:mm',
                    date: moment()
                });
                $('.summernote').summernote({
                    height: 150
                });
                isReminderShow();
            }
        });
    }

    function deleteUser(id) {
        if (id) {
            let data = confirm("Are you sure? you wants to delete this inquiry?");
            if (data) {
                var token = $("meta[name='csrf-token']").attr("content");
                $.ajax({
                    url: "{{ url('/admin/inquiry') }}/" + id,
                    type: 'DELETE',
                    data: {
                        "id": id,
                        "_token": token,
                    },
                    success: function() {
                        window.location.reload;
                    }
                });
            }
        }
    }
</script>
@endsection