<form method="POST" action="" id="frmAssignToRetailer">
    @csrf
    <div class="form-group">
            <input type="hidden" name="dealer" value="{{auth()->user()->id}}" />
            <input type="hidden" name="id" id='inquiry_id' value="" />
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-7">
            <!-- text input -->
            <div class="form-group">
                <label>Retailer</label>
                <select class="form-control select2 retailer" id='retailer' name='retailer'>
                    <option value="">Select</option>
                    @foreach ($retailers as $retailer)
                    <option value="{{$retailer->id}}">{{$retailer->name .' - ' .$retailer->zipcode.' - '. $retailer->city->name}}</option>
                    @endforeach
                </select>
                @error('retailer')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>