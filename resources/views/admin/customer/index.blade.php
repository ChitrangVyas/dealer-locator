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
                            <div class="float-right"><a href="{{route('customer.create')}}" class="btn btn-primary btn-md">Add {{ucwords($title)}}</a></div>
                        </div> -->
                        <div class="card-header">
                            <div class="row">
                                <div class="col-8">
                                    @if(auth()->user()->users_group_id =='1' || auth()->user()->users_group_id == NULL || auth()->user()->users_group_id =='2')
                                    <form action="{{route('customerlistajax')}}" class="d-inline" id="filterForm" method="post">
                                        @csrf
                                        <div class="form-group row">
                                            <label class="col-1">Users</label>
                                            <div class="col-4">
                                                <div class="form-check">
                                                    <input class="form-check-input radio_user" type="radio" name="radio_user" id="radio_user1" value="Own" checked>
                                                    <label class="form-check-label" for="radio_user1">Own</label>
                                                </div>
                                                @if((auth()->user()->users_group_id =='1' ) || (auth()->user()->users_group_id == NULL))
                                                    <div class="form-check">
                                                        <input class="form-check-input radio_user" type="radio" name="radio_user" id="radio_user2" value="Dealers">
                                                        <label class="form-check-label" for="radio_user2">Dealers</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input radio_user" type="radio" name="radio_user" id="radio_user3" value="Retailers">
                                                        <label class="form-check-label" for="radio_user3">Retailers</label>
                                                    </div>
                                                @endif
                                                @if(auth()->user()->users_group_id =='2')
                                                    <div class="form-check">
                                                        <input class="form-check-input radio_user" type="radio" name="radio_user" id="radio_user3" value="DRetailers">
                                                        <label class="form-check-label" for="radio_user3">Retailers</label>
                                                    </div>
                                                @endif
                                                <!-- text input -->
                                                <div class="form-group" id="divDealerSelection">
                                                    <label>Dealer</label>
                                                    <select class="form-control select2 dealer" id='dealer' name='dealer' tabindex="4">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                                <!-- text input -->
                                                <div class="form-group" id="divRetailerSelection">
                                                    <label>Retailer</label>
                                                    <select class="form-control select2 retailer" id='retailer' name='retailer' tabindex="5">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-7">
                                                <button type="button" class="d-inline btn btn-primary btn-sm form-control-sm col-1" id="filter" value="">Filter</button>    
                                            </div>
                                        </div>
                                    </form>
                                    @endif
                                </div>
                                <div class="col-4">
                                    <div class="float-right">
                                        <a href="{{route('customer.create')}}" class="btn btn-primary btn-md">Add {{ucwords($title)}}</a>
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
                                        <th>Contact Type</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th data-name="">City</th>
                                        <th>State</th>
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
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
@section('js')
<script>
    $(window).on('load', function() {
        $("#divDealerSelection").hide();
        $("#divRetailerSelection").hide();
    });
</script>
<script>
    jQuery(document).ready(function(){
        $('#example').DataTable({
            "columns": [
                { "name": "id" },
                { "name": "users_group_id" },
                { "name": "name" },
                { "name": "email" },
                { "name": "mobile" },
                { "name": "cities.name" },
                { "name": "states.name" },
                { "name": "status" },
                { "name": "action" }
            ],
            "columnDefs": [
            {
                "targets":[ 4 ],
                "className": "text-center",
            },
            {
                "targets": [ 4,7,8 ],
                "orderable": false
            },
            {
                "targets": [ 0,1 ],
                "visible": false
            },
            ],
            aaSorting: [[0, 'desc']],
            "processing": true,
            "serverSide": true,
            "ajax":{
                "url": "{{route('customerlistajax')}}",
                "type": "POST",
                "data": function ( d ) {
                    d._token = $("meta[name='csrf-token']").attr("content");
                    d.radio_user = $('input[name="radio_user"]:checked').val();
                    d.dealer = $('#dealer').val();
                    d.retailer = $('#retailer').val();
                }
            },
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
        $('input[type=radio][name=radio_user]').change(function() {
            $('#dealer').val(null).trigger('change');
            $('#retailer').val(null).trigger('change');
            $("#divDealerSelection").hide();
            $("#divRetailerSelection").hide();
            if (this.value == 'Dealers') {
                $("#divDealerSelection").show();
            }
            else if (this.value == 'Retailers' || this.value == 'DRetailers') {
                $("#divRetailerSelection").show();
            }
        });
        $('body').on('click',"#filter",function(){
            $('#example').DataTable().ajax.reload();
        });
        $('.dealer').select2({
            placeholder: "Select",
            minimumInputLength: 2,
            allowClear: true,
            ajax: {
                url: "{{route('get_value')}}",
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        table:'users',
                        fields: "users.id, CONCAT(`users`.`name`, ' - ', `zipcode`,' - ',`cities`.`name`) as text",
                        where:`(users.users_group_id = 2) AND users.deleted_at IS NULL AND users.status = 1`,
                        joins:[{'join_table':'cities','to_column':'users.city_id','from_column':'cities.id','type':'left'}]
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });
        $('.retailer').select2({
            placeholder: "Select",
            minimumInputLength: 2,
            allowClear: true,
            ajax: {
                url: "{{route('get_value')}}",
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    var is_dealer = "{{auth()->user()->users_group_id == 2}}";
                    if(is_dealer){
                        return {
                            q: params.term, // search term
                            table:'users',
                            fields: "users.id, CONCAT(`users`.`name`, ' - ', `zipcode`,' - ',`cities`.`name`) as text",
                            where:`(users.users_group_id = 3) AND users.parent_id = {{auth()->user()->id}} AND users.deleted_at IS NULL AND users.status = 1`,
                            joins:[{'join_table':'cities','to_column':'users.city_id','from_column':'cities.id','type':'left'}]
                        };
                    } else {
                        return {
                            q: params.term, // search term
                            table:'users',
                            fields: "users.id, CONCAT(`users`.`name`, ' - ', `zipcode`,' - ',`cities`.`name`) as text",
                            where:`(users.users_group_id = 3) AND users.deleted_at IS NULL AND users.status = 1`,
                            joins:[{'join_table':'cities','to_column':'users.city_id','from_column':'cities.id','type':'left'}]
                        };
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
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
                        url: "{{ url('/admin/customer/') }}/"+id,
                        type: 'DELETE',
                        data: {
                            "id": id,
                            "_token": token,
                        },
                        success: function (){
                            $('#example').DataTable().ajax.reload(null, false);
                        }
                    });
                }
            }
        }
</script>
@endsection