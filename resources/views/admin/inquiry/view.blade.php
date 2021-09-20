<div class="card">
    <div class="card-header bg-light">
        <h3 class="card-title">Inquiry</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td><strong>Customer Name</strong></td>
                    <td>{{$inquiry->customer->name}}</td>
                </tr>
                <tr>
                    <td><strong>Customer Email</strong></td>
                    <td>{{$inquiry->customer->email}}</td>
                </tr>
                <tr>
                    <td><strong>Customer Phone</strong></td>
                    <td>{{$inquiry->customer->mobile}}</td>
                </tr>
                <tr>
                    <td><strong>Source</strong></td>
                    <td>{{!(empty($inquiry->inquirySource)) ? $inquiry->inquirySource->source : 'N/A'}}</td>
                </tr>
                <tr>
                    <td><strong>Dealer</strong></td>
                    <td>{{(!empty($inquiry->dealer))?$inquiry->dealer->name:'N/A'}}</td>
                </tr>
                <tr>
                    <td><strong>Retailer</strong></td>
                    <td>{{(!empty($inquiry->retailer))?$inquiry->retailer->name:'N/A'}}</td>
                </tr>
                <tr>
                    <td><strong>Created By</strong></td>
                    <td>{{(!empty($inquiry->createdBy))?$inquiry->createdBy->name:'N/A'}}</td>
                </tr>
                <tr>
                    <td><strong>Status</strong></td>
                    <td>{{(!empty($inquiry->getInquirystatusName()))?$inquiry->getInquirystatusName():'N/A'}}</td>
                </tr>
                <tr>
                    <td><strong>Product</strong></td>
                    <td>{{$inquiry_products}}</td>
                </tr>
                <tr>
                    <td><strong>Created At</strong></td>
                    <td>{{$inquiry->created_at}}</td>
                </tr>
                <tr>
                    <td><strong>Updated At</strong></td>
                    <td>{{$inquiry->updated_at}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
    </div>
</div>