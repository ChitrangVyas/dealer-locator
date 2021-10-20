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
                        <h2 class="card-title">{{ucwords($title)}}</h2>
                        <div class="float-right">
                            <!-- <a id="newPo" data-toggle='modal' data-target='#PoModal' class="btn btn-primary btn-md create-po">Add PO</a> -->
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                Add PO
                                </button>
                                <div class="dropdown-menu">
                                    <a href="javascript:void(0);" data-po='vendor' class="dropdown-item new-po">Add PO For Vendor</a>
                                    @if((auth()->user()->users_group_id =='2' ) || (auth()->user()->users_group_id =='3' ))
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0);" data-po='dealer' class="dropdown-item new-po">Add PO For {{(auth()->user()->users_group_id =='3' )? 'Dealer' : 'Manufacturer'}}</a>
                                    @endif
                                </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <!-- <th>Inquiry</th> -->
                                    <th>Order Number</th>
                                    <!-- <th>Product</th> -->
                                    <!-- <th>Qty</th> -->
                                    <th>Total</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Created Date</th>
                                    <th width="20%">Action</th>
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
<div class="modal fade overflow-auto" id="orderModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="cartTitle"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- modal -->
<div class="modal fade" id="PoModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- modal -->
<div class="modal fade" id="modal-lg" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle">Vendor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.tempusdominus', true)
@section('plugins.summernote', true)
@section('plugins.Sweetalert2', true)
@section('js')
<script>
    jQuery(document).ready(function() {
        $('#example').DataTable({
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
                    "name": "d.name"
                },
                {
                    "name": "c.name"
                },
                {
                    "name": "orders.created_at"
                },
                {
                    "name": "action"
                }
            ],
            "columnDefs": [
                {
                    "targets": [6],
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
            "ajax": dataTableRequest("{{ route('list.po') }}"),
        });
        $('.datetimepicker-input').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            date: moment()
        });
        $('.summernote').summernote({
            height: 150
        });

        $(window).keydown(function(event){
            if(event.keyCode == 13) {
            event.preventDefault();
            return false;
            }
        });

        $('body').on('click','.view-order',function(){
            var view_url = $(this).data('url');
            $(`#orderModal .modal-body`).html("");
            getOrderData(view_url,{'order_id':$(this).data('order')}).then(function(res){
                select2Product();
            });
        });

        $('body').on('click','.new-po',function(){
            let type = $(this).data('po');
            if(type=='vendor'){
                getData("{{route('po.create')}}",{'type':type},'PoModal','modalTitle').then(function(res){
                    select2Vendor();
                    $('#PoModal').modal('show');
                });
            }else{
                getOrderData("{{route('cart.po.create')}}",{'dealer':"{{auth()->user()->parent_id}}"}).then(function(res){
                    $('#orderModal').modal('show');
                    select2Product();
                });
            }

        });

        $('body').on('submit','#saveQuote', function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            let url = ($('#type').val() == 'sale')?"{{ route('order.sale.store') }}":"{{ route('order.po.store') }}";
            let data = $('#saveQuote').serializeArray();
            var frm = $(this);
            var btn = $(document.activeElement);
            data.push({name:'btnSave', value:btn.val()});
            
            // let inquiry_id = $('#inquiry_id').val();
            saveFormData(url,data,'orderModal').then(function(res){
                $('#example').DataTable().ajax.reload(null, false);
            });
        });
        $('body').on('submit','#uploadPo',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            let url = "{{route('order.po.recieved')}}";
            // let formData = new FormData($(this)[0]);
            let formData = new FormData($('#uploadPo')[0]);
            let file = $('input[type=file]')[0].files[0];
            if(file != '' && file != undefined && file.length != 0){
                formData.append('file', file, file.name);
            }
            saveFormData(url,formData).then(function(res){
                
            });
        });
        
        $('body').on('change','#vendor',function(){
            //$(this).val()
            if($(this).val()){
                //cart.po.create
                $('#PoModal').modal('hide');
                getOrderData("{{route('cart.po.create')}}",{'vendor':$(this).val()}).then(function(res){
                    $('#orderModal').modal('show');
                    select2Product();
                });
            }
        });

        $('body').on('change','input[type="file"]',function(e){
            var fileName = e.target.files[0].name;
            $('.custom-file-label').html(fileName);
        });

        $('body').on('click','#imageUpload',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            let formData = new FormData($('#uploadPo')[0]);
            let url = "{{route('order.po.upload.image')}}";
            let file = $('input[type=file]')[0].files[0];
            formData.append('file', file, file.name);
            saveFormData(url,formData).then(function(res){
                let images = [];
                if($('#uploadedImage').val() != "" && $('#uploadedImage').val().length !== 0){

                    images.push($('#uploadedImage').val());
                }
                images.push(res.fileName);
                $('#uploadedImage').val(images);
                $('#exampleInputFile').val("");
                $('.custom-file-label').html("");
                getOrderImage("{{ route('order.po.image') }}",{'order_id':$('input[name=order_id]').val()}).then(function(res){

                });
            });
        });

        $('body').on('click','#addVendor',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        // var data = $('#saveInquiryLog').serialize();        
        $.ajax({
            url: "{{ route('customerCreateAjax') }}",
            type: 'GET',
            data: {'vendor':1},
            success: function (data) {                
                $("#modal-lg .modal-body").html(data);
                $("#modal-lg").modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("Eror");
                    /* Toast.fire({
                        type: 'error',
                        title: JSON.parse(jqXHR.responseText).message
                    }); */
            }
        });
    });
        
        $("body").on("click","#addProductToCart",function(){
            addProductToCart().then(function(){
                select2Product();
            });
        });
        $("body").on("click","#addNewProductToCart",function(){
            addNewProductToCart();
        });
        
        
        
        $("body").on("change",".tax, .product, .qty, .price", function(){
            /* price = $(this).closest('.product-detail').find('.price').val();
            qty =  $(this).closest('.product-detail').find('.qty').val();
            amount = price * qty;
            // amount = ele.closest('.product-detail').find('.amount').val();
            $(this).closest('.product-detail').find('.amount').text(amount); */
            updateCart().then(function(){     
                select2Product();
            });
        });

        $("body").on("click",".delete",function(){
            if(confirm("Do you want to remove this product from the cart?")){
                removeCartProduct($(this).data('product')).then(function(){
                    select2Product();
                });

            }
        });

        $("body").on('shown.bs.modal',function(){
            var selectedTab = $(".modal-body .tab-content").find(".active");
            activeTab(selectedTab);
        });

    });

    /* function toggleProductDetail(ele){
        if (!ele.is(":checked")) {
            ele.closest('.product-detail').find('input').not('input[type=checkbox]').attr('disabled',true)
        }else{
            ele.closest('.product-detail').find('input').not('input[type=checkbox]').attr('disabled',false)
        }
    } */

    function getTotal(){
        // ele.closest('.product-detail').find('input').not('input[type=checkbox]').attr('disabled',true);
        total = 0.00;
        $('.amount').each(function(){
            if($(this).closest('.product-detail').find('.product').is(':checked')){
                total += parseFloat($(this).text());
            }
        });
        $('#total').text(total);
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
                if(loadTo == 'task'){
                    $('#saveReminder .reminder').val('');
                }
            }
        });
    }

    function saveFormData(url,formData,modal=''){
            const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                        });
            return $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                beforeSend: function(){
                    $('#overlay').show();
                    $('.btn-save').attr('disabled',true);
                },
                complete: function(){
                    $('#overlay').hide();
                },
                success: function (data) {
                    if(data.code == 1){
                        Toast.fire({
                            type: 'success',
                            title: data.message
                        });
                        if(modal !== ''){
                            $(`#${modal}`).modal('hide');
                        }
                    }else{
                        Toast.fire({
                            type: 'error',
                            title: data.message
                        })
                    }
                },
                completed:function(){
                    $('.btn-save').attr('disabled',false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.btn-save').attr('disabled',false);
                    var err = JSON.parse(jqXHR.responseText).errors;
                    Toast.fire({
                        type: 'error',
                        title: JSON.parse(jqXHR.responseText).message
                    });
                    $.each(err,function(key,val){
                        var input_field = key.split('.');
                        if($(`[name=${input_field[0]}]`).length > 0){
                            $(`[name=${input_field[0]}]`).closest('.form-group').after(`<div class="alert alert-danger mt-2">${val}</div>`);                                        
                        }
                    });
                }
            });
    }

    function getOrderData(url,data){
        var token = $("meta[name='csrf-token']").attr("content");
        return $.ajax({
            url: url,
            type: 'POST',
            data:{data,'_token':token},
            beforeSend : function(){
                $('#loader').show();
            },
            success: function(res) {
                $(`#orderModal .modal-body`).html(res.data.cart_content);
                $('#cartTitle').text(res.data.cart_title);
                // $('.product-detail input').not('input[type=checkbox]').attr('disabled',true);
            },
            complete:function(){
                $('#loader').hide();
            }
        });
    }

    function getData(url,data,modal='',ele_class=''){
        var token = $("meta[name='csrf-token']").attr("content");
        return $.ajax({
            url: url,
            type: 'POST',
            data:{data,'_token':token},
            beforeSend : function(){
                $('#loader').show();
            },
            success: function(res) {
                if(modal !== ''){
                    $(`#${modal} .modal-body`).html(res.data.modal_content);
                }
                if(ele_class !== ''){
                    $(`#${ele_class}`).text(res.data.modal_title);
                }
                // $('.product-detail input').not('input[type=checkbox]').attr('disabled',true);
            },
            complete:function(){
                $('#loader').hide();
            }
        }); 
    }

    function getOrderImage(url,data){
        var token = $("meta[name='csrf-token']").attr("content");
        return $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data:{data,'_token':token},
            beforeSend : function(){
                $('#loader').show();
            },
            success: function(res) {
                $(`#orderModal #uploadedPo`).html(res.data.image_content);
                // $('.product-detail input').not('input[type=checkbox]').attr('disabled',true);
            },
            complete:function(){
                $('#loader').hide();
            }
        });
    }

    function addProductToCart(){
        const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 8000
                        });
        var token = $("meta[name='csrf-token']").attr("content");
        return $.ajax({
            url: "{{route('cart.product.add')}}",
            type: 'POST',
            data:{'_token':token,'id':$('#modalId').val(),'product_id':$('#selectProduct').val()},
            success: function(res) {
                if(res.code == 1){
                    $('#orderModal .modal-body').html(res.data.cart_content);
                }else{
                    Toast.fire({
                            type: 'error',
                            title: res.message
                        });
                }
                // $('.product-detail input').not('input[type=checkbox]').attr('disabled',true);
            }
        });
    }

    function addNewProductToCart(){
        const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 8000
                        });
        var token = $("meta[name='csrf-token']").attr("content");
        return $.ajax({
            url: "{{route('cart.product.new')}}",
            type: 'POST',
            data:{'_token':token,'id':$('#modalId').val(),'is_new_product':$('#is_new_product').val()},
            success: function(res) {
                if(res.code == 1){
                    $('#orderModal .modal-body').html(res.data.cart_content);
                }else{
                    Toast.fire({
                            type: 'error',
                            title: res.message
                        });
                }
                // $('.product-detail input').not('input[type=checkbox]').attr('disabled',true);
            }
        });
    }

    function updateCart(){
        var token = $("meta[name='csrf-token']").attr("content");
        return $.ajax({
            url: "{{route('cart.update')}}",
            type: 'PUT',
            data:$('#saveQuote').serialize(),
            success: function(res) {
                $(`#orderModal .modal-body`).html(res);
                // $('.product-detail input').not('input[type=checkbox]').attr('disabled',true);
            }
        });
    }

    function removeCartProduct(product_id){
        var token = $("meta[name='csrf-token']").attr("content");
        return $.ajax({
            url: "{{ route('cart.product.destroy') }}",
            type: 'DELETE',
            data:{'_token':token,'id':$('#modalId').val(),'product_id':product_id,'is_new_product':$('#is_new_product').val()},
            success: function(res) {
                $(`#orderModal .modal-body`).html(res);
                // $('.product-detail input').not('input[type=checkbox]').attr('disabled',true);
            }
        });
    }

    function select2Product(){
        $('.select-product').select2({
            placeholder: "Select",
            allowClear: true,
            minimumInputLength: 2,
            width:"100%",
            ajax: {
                url: "{{route('get_value')}}",
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        table:'products',
                        where: 'deleted_at is NULL  AND status = 1'
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });
    }

    function select2Vendor(){
        $('.vendor').select2({
            placeholder: "Select",
            allowClear: true,
            minimumInputLength: 1,
            width:"80%",
            ajax: {
                url: "{{route('get_value')}}",
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        table:'users',
                        where: 'users_group_id = 5 AND deleted_at is NULL  AND status = 1 AND created_by={{auth()->user()->id}}'
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
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
                        window.location.reload();
                    }
                });
            }
        }
    }
</script>
@endsection