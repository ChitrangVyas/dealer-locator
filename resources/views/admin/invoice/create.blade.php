<div class="row">
    <div class="col-sm-12 col-md-11">
        <div class="form-group">
            @if(auth()->user()->users_group_id==3)
            <label>Customer</label>
            @endif
            @if(auth()->user()->users_group_id==2)
            <label>Customer/Retailer</label>
            @else
            <label>Customer/Retailer/Dealer</label>
            @endif
            <div class="input-group input-group-sm">
                <select class="form-control select2 customer" id='customer' name='customer'>
                    <option value="">Select</option>
                </select>
                <!-- <span class="input-group-append">
                    <button id="addCustomer" class="btn btn-primary btn-flat addCustomer" data-toggle='modal' data-target='#modal-lg'>Add New</button>
                </span> -->
            </div>
        </div>
    </div>
</div>