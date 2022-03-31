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
                        <div class="float-right"><a id="newinvoice" data-create-quote-url="{{route('cart.quote.create')}}" class="btn btn-primary btn-md create-quote-0">Create Order</a></div>
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
                                    <th>To</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
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
<div class="modal fade" id="orderModal">
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
<div class="modal fade" id="invoiceModal" data-keyboard="false" data-backdrop="static">
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
                    "name": "status"
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
                    "targets": [4],
                    "orderable": false
                },
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
            "ajax": dataTableRequest("{{ route('list.sale') }}"),
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
        
        $('body').on('click','.courier_details_btn',function(){
            saveFormData("{{route('invoice.status.update')}}",$('#invoice').serialize(),'orderModal').then(function(res){
                $('#example').DataTable().ajax.reload(null, false);
            });
        });
        $('body').on('click','.view-order',function(){
            var view_url = $(this).data('url');
            
            $("#uploadedPo").html("");
            $(`#orderModal .modal-body`).html("");
            console.log("asdasd");
            getOrderData(view_url,{'order_id':$(this).data('order')}).then(function(res){
                if($(this).attr('title')=="View"){
                    $('.dateimepicker-input').not('.hasDatePicker').datepicker({format:"YYYY-MM-DD"});
                }else{
                    $('.dateimepicker-input').not('.hasDatePicker').datepicker({format:"YYYY-MM-DD"});
                }                 
                
            });
        });
        $('body').on('change','#order_status',function(){
            saveFormData("{{route('invoice.status.update')}}",$('#invoice').serialize(),'orderModal').then(function(res){
                $('#example').DataTable().ajax.reload(null, false);
            });
        });

        $('body').on('change','#order_stage',function(){
            saveFormData("{{route('sale.stage.update')}}",$('#invoice').serialize(),'orderModal').then(function(res){
                $('#example').DataTable().ajax.reload(null, false);
            });
        });

        $('body').on('click','#newinvoice',function(e){
            var create_quote_url = $(this).data('create-quote-url');
            let type = $(this).data('quote');
            getData("{{route('invoice.create')}}",{'type':'vendor'},'invoiceModal','modalTitle').then(function(res){
                $('#invoiceModal').modal('show');
                select2Customer();
            });
        });
       
        $('body').on('submit','#saveQuote', function(e){
            e.preventDefault();
            let url = ($('#type').val() == 'sale')?"{{ route('order.sale.store') }}":"{{ route('order.po.store') }}";
            let data = $('#saveQuote').serializeArray();
            var frm = $(this);
            var btn = $(document.activeElement);
            data.push({name:'btnSave', value:btn.val()});
            // let inquiry_id = $('#inquiry_id').val();
            saveFormData(url,data,'orderModal').then(function(res){
                // console.log(res);
                if(res.code == 1){
                  $(`.create-po-${res.data.quote}`).hide();  
                }
                $('#example').DataTable().ajax.reload(null, false);
            });
        });
        $('body').on('submit','#uploadPo',function(e){
            e.preventDefault();
            let url = "{{route('order.po.recieved')}}";
            // let formData = new FormData($(this)[0]);
            let formData = new FormData($('#uploadPo')[0]);
            let file = $('input[type=file]')[0].files[0];
            if(file != '' && file != undefined && file.length != 0){
                formData.append('file', file, file.name);
            }
            saveFormDataForFile(url,formData).then(function(res){
                
            });
        });
        
        $('body').on('change','input[type="file"]',function(e){
            var fileName = e.target.files[0].name;
            $('.custom-file-label').html(fileName);
        });

        $('body').on('change','#customer',function(){
            //$(this).val()
            if($(this).val()){
                //cart.po.create
                $('#invoiceModal').modal('hide');
                getOrderData("{{route('cart.invoice.create')}}",{'customer':$(this).val()}).then(function(res){
                    $('#orderModal').modal('show');                                        
                    select2Product();
                });
            }
        });
        
        $('body').on('click','#imageUpload',function(){
            let formData = new FormData($('#rutika')[0]);
            let url = "{{route('order.po.upload.image')}}";
            let file = $('input[type=file]')[0].files[0];
            formData.append('file', file, file.name);
            saveFormDataForFile(url,formData).then(function(res){
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
        $('body').on('submit','#rutika',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            let url = "{{route('order.po.recieved')}}";
            // let formData = new FormData($(this)[0]);
            let formData = new FormData($('#rutika')[0]);                        
            let file = $('#rutika input[type=file]')[0].files[0];
            if(file != '' && file != undefined && file.length != 0){
                formData.append('file', file, file.name);
            }
            saveFormDataForFile(url,formData).then(function(res){
                if(res.code == 1){
                  $(`#orderModal`).modal('hide');
                }
                $('#example').DataTable().ajax.reload(null, false);
            });
        });
        $("body").on("click",".product", function(){
            var selected_product = $('input:checkbox:checked').length;
            if(selected_product < 1){
                //alert("Please select at least one product.");
                $(this).prop('checked',true);
            }
            toggleProductDetail($(this));
            getTotal();
        });
        $("body").on("click","#addProductToCart",function(){
            addProductToCart().then(function(){
                select2Product();
                $('.dateimepicker-input').datetimepicker({format:"YYYY-MM-DD"});
                $('.dateimepicker-input').on('change.datetimepicker',function(){
                    UpdateCreatedDateInSession();
                });
            });
        });        
        
        $("body").on("change",".tax, .qty, .price, .discount", function(){
            /* price = $(this).closest('.product-detail').find('.price').val();
            qty =  $(this).closest('.product-detail').find('.qty').val();
            amount = price * qty;
            // amount = ele.closest('.product-detail').find('.amount').val();
            $(this).closest('.product-detail').find('.amount').text(amount); */
            updateCart().then(function(){     
                select2Product();
                $('.dateimepicker-input').datetimepicker({format:"YYYY-MM-DD"});
                    $('.dateimepicker-input').on('change.datetimepicker',function(){
                        UpdateCreatedDateInSession();
                    });
            });
        });

        $("body").on("click",".delete",function(){
            if(confirm("Do you want to remove this product from the cart?")){
                removeCartProduct($(this).data('product')).then(function(){
                    select2Product();
                    $('.dateimepicker-input').datetimepicker({format:"YYYY-MM-DD"});
                    $('.dateimepicker-input').on('change.datetimepicker',function(){
                        UpdateCreatedDateInSession();
                    });
                });

            }
        });

        $('body').on('click','.delete-installation-image',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            let data = confirm("Are you sure? you wants to delete this image?");
            if(data){
                let order_image = $(this).data('order-image');
                saveFormData("{{route('installation.image.delete')}}",{order_image_id:$(this).data('order-image')},'orderModal').then(function(res){
                });
            }
        });

        $("body").on('shown.bs.modal',function(){
            var selectedTab = $(".modal-body .tab-content").find(".active");
            activeTab(selectedTab);
        });

        $('body').on('click','#btnUpdate',function(){
            saveFormData("{{route('order.token.update')}}",$('#invoice').serializeArray());
        });

    });
    function isReminderShow(){
        if (!$("#saveInquiryLog .is-reminder").is(":checked")) {
                $('#saveInquiryLog .reminder').fadeOut("500");
                $('#saveInquiryLog .reminder').val('');
            }else{
                $('#saveInquiryLog .reminder').show("500");
            }
    }

    function toggleProductDetail(ele){
        if (!ele.is(":checked")) {
            ele.closest('.product-detail').find('input').not('input[type=checkbox]').attr('disabled',true)
        }else{
            ele.closest('.product-detail').find('input').not('input[type=checkbox]').attr('disabled',false)
        }
    }

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
                    $.each(err,function(key,val){                
                        $(`[name=${key}]`).closest('.form-group').after(`<div class="alert alert-danger mt-2">${val}</div>`);                                        
                    });
                    Toast.fire({
                        type: 'error',
                        title: JSON.parse(jqXHR.responseText).message
                    });
                }
            });
    }

    function saveFormDataForFile(url,formData,modal=''){
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
                processData: false,
                contentType: false,
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
                    $.each(err,function(key,val){                
                        $(`[name=${key}]`).closest('.form-group').after(`<div class="alert alert-danger mt-2">${val}</div>`);                                        
                    });
                    Toast.fire({
                        type: 'error',
                        title: JSON.parse(jqXHR.responseText).message
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
                $('.dateimepicker-input').datetimepicker({format:"YYYY-MM-DD"});
                $('.dateimepicker-input').on('change.datetimepicker',function(){
                    UpdateCreatedDateInSession();
                });
                getOrderImage("{{ route('order.po.image') }}",{'order_id':$('input[name=order_id]').val()});   
            },
            complete:function(){
                $('#loader').hide();
            }
        });
    }
    function UpdateCreatedDateInSession(){
        console.log($("input[name='cart_id']").val());
        var token = $("meta[name='csrf-token']").attr("content");
        $.ajax({
            url: '{{route("cart.created_at.update")}}',
            type: 'POST',
            data:{'_token':token,'created_at':$("#datetimepickerM").val(),"id":$("input[name='cart_id']").val()},
            beforeSend : function(){
                $('#loader').show();
            },
            success: function(res) {                
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
            data:{'_token':token,'id':$('#modalId').val(),'product_id':product_id},
            success: function(res) {
                $(`#orderModal .modal-body`).html(res);
                // $('.product-detail input').not('input[type=checkbox]').attr('disabled',true);
            }
        });
    }

    function select2Product(){
        $('.select-product').select2({
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
                        where: 'deleted_at is NULL'
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

    function deleteOrder(id) {
        if (id) {
            let data = confirm("Are you sure? you wants to delete this Sale?");
            if (data) {
                var token = $("meta[name='csrf-token']").attr("content");
                $.ajax({
                    url: "{{route('order.delete')}}",
                    type: 'POST',
                    data: {
                        "order_id": id,
                        "_token": token,
                    },
                    success: function() {
                        $('#example').DataTable().ajax.reload(null, false);
                    }
                });
            }
        }
    }
    function select2Customer(){
        var option_group = [
                            @if(auth()->user()->users_group_id==3 || auth()->user()->users_group_id==2 || (empty(auth()->user()->users_group_id) || auth()->user()->users_group_id==1))
                            {name:'Customer',table:'users',where:'users_group_id = 4 AND deleted_at is NULL  AND status = 1 AND created_by={{auth()->user()->id}}'},
                            @endif
                            @if(auth()->user()->users_group_id==2 || (empty(auth()->user()->users_group_id) || auth()->user()->users_group_id==1))
                            {name:'Retailer',table:'users',where:'users_group_id = 3 AND deleted_at is NULL AND status = 1' + "{{(empty(auth()->user()->users_group_id) || auth()->user()->users_group_id==1) ? '':  ' AND created_by='.auth()->user()->id}} OR (parent_id={{auth()->user()->id}} AND created_by=1)"},
                            @endif
                            @if(empty(auth()->user()->users_group_id) || auth()->user()->users_group_id==1)
                            {name:'Dealer',table:'users',where:'users_group_id = 2 AND deleted_at is NULL AND status = 1 OR (parent_id={{auth()->user()->id}} AND created_by=1)'},
                            @endif

                        ];
        $('.customer').select2({
            placeholder: "Select",
            allowClear: true,
            width:"80%",
            ajax: {
                url: "{{route('get_value')}}",
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        table:'users',
                        where: 'users_group_id = 4 AND deleted_at is NULL AND created_by={{auth()->user()->id}}',
                        option_group:option_group
                    };
                },
                processResults: function (data) {
                    return {
                        results:  $.map(data, function (item,key) {
                                    return {
                                        text: item.text,
                                        children: item.children,
                                    }
                                })
                    };
                }
            }
        });
    }
    function copyToClipboard(element) {
        $(element).select();
        document.execCommand("copy");
    }
</script>
@endsection