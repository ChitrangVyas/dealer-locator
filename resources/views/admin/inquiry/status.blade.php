<form method="POST" id="inquiryStatusfrm">
    @csrf
    <input type="hidden" name="inquiry_id" value="{{$inquiry->id}}">
<div class="row">
    <div class="col-sm-12 col-md-11">
        <div class="form-group">
            <label>Status</label>
            <select class="form-control select2 status" id='status' name='status'>
                <option value="" {{((empty($inquiry->status)))?' selected':''}}>Select</option>
                <option value="1" {{((!empty($inquiry->status)) && $inquiry->status ==1)?' selected':''}}>New</option>
                <option value="2" {{((!empty($inquiry->status)) && $inquiry->status ==2)?' selected':''}}>In Progress</option>
                <option value="3" {{((!empty($inquiry->status)) && $inquiry->status ==3)?' selected':''}}>Attempted to Contact</option>
                <option value="4" {{((!empty($inquiry->status)) && $inquiry->status ==4)?' selected':''}}>Connected</option>
                <option value="5" {{((!empty($inquiry->status)) && $inquiry->status ==5)?' selected':''}}>Open Quote</option>
            </select>
        </div>
    </div>
</div>
</form>