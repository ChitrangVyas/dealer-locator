@if(!empty($inquiry))
<form method="POST" action="{{ route('inquiry-log.update',$inquiry->id) }}" id="updateInquiryLog">
    @method('PUT')
@else
<form method="POST" action="{{ route('inquiry-log.index') }}" id="saveInquiryLog">
    @endif
    @csrf
    <input type="hidden" name="logged_by" value="{{auth()->user()->id}}" />
    <input type="hidden" name="inquiry_id" id="inquiry_id" value="" />
    <input type="hidden" name="id" value="{{ (!empty($inquiry))?$inquiry->id:'' }}" />
    <input type="hidden" name="type" id="type" value="" />


    <div class="row">
        <div class="col-sm-12 col-md-6">
            <!-- text input -->
            <div class="form-group">
                <label>Reminder</label>
                <input type="text" class="form-control datetimepicker-input reminder" id="datetimepicker" name="reminder" data-toggle="datetimepicker" data-target="#datetimepicker" readonly />
                @error('reminder')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-12 col-md-12">
            <!-- text input -->
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="summernote" class="form-control summernote"rows="5"></textarea>
                @error('reminder')
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