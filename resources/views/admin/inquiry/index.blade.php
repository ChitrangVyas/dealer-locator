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
                                        <a class="nav-link active" id="tab-email" data-tab="email" data-toggle="pill" href="#nav-tab-email" role="tab" aria-controls="nav-tab-email" aria-selected="true" onclick="activeTab(this)">Email</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-calls" data-tab="calls" data-toggle="pill" href="#nav-tab-calls" role="tab" aria-controls="nav-tab-calls" aria-selected="false" onclick="activeTab(this)">Calls</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-meetings" data-tab="meetings" data-toggle="pill" href="#nav-tab-meetings" role="tab" aria-controls="nav-tab-meetings" aria-selected="false" onclick="activeTab(this)">Meetings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-notes" data-tab="notes" data-toggle="pill" href="#nav-tab-notes" role="tab" aria-controls="nav-tab-notes" aria-selected="false" onclick="activeTab(this)">Notes</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="tab-content">
                                    <div class="tab-pane fade show active" id="nav-tab-email" role="tabpanel" aria-labelledby="tab-email">
                                      @php $fname = "email"; @endphp  @include('layouts.partials.log-form')
                                    </div>
                                    <div class="tab-pane fade" id="nav-tab-calls" role="tabpanel" aria-labelledby="tab-calls">
                                    @php $fname = "calls"; @endphp @include('layouts.partials.log-form')
                                    </div>
                                    <div class="tab-pane fade" id="nav-tab-meetings" role="tabpanel" aria-labelledby="tab-meetings">
                                    @php $fname = "meetings"; @endphp @include('layouts.partials.log-form')
                                    </div>
                                    <div class="tab-pane fade" id="nav-tab-notes" role="tabpanel" aria-labelledby="tab-notes">
                                    @php $fname = "notes"; @endphp @include('layouts.partials.log-form')
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
        $('.datetimepicker').datetimepicker({
            icons: {
                    time: "fas fa-clock-o",
                    date: "fas fa-calendar",
            },
            format:"YYYY/MM/DD HH:mm:00",
        })
        $('.summernote').summernote({
            height: 150
        });
        $('body').on('click','#addLog',function(){
            let inquiry_id = $(this).data('inquiry');
            $('#inquiry_id').val(inquiry_id);
            let selectedTab = $('.nav-tabs .active').data('tab');
            $('#type').val(selectedTab);
        });
        $('#saveInquiryLog').on('submit', function(e){
            e.preventDefault();
            const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                        });
            var data = $('#saveInquiryLog').serialize();
            var url = "{{ route('inquiry-log.store') }}";
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'JSON',
                success: function (data) {
                    console.log(data);
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
                    console.log(jqXHR.responseText);
                        Toast.fire({
                            type: 'error',
                            title: JSON.parse(jqXHR.responseText).message
                        });
                }
            });
        });
    });

    function activeTab(ele){
        let selectedTab = $(ele).data('tab');
        $('#type').val(selectedTab);
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