@if(!empty($inquiry))
    <form method="POST" action="" id="updateInquiryLog">
    @method('PUT')
@else
    <form method="POST" action="" id="saveReminder">
@endif
        @csrf
        <input type="hidden" name="logged_by" value="{{auth()->user()->id}}" />
        <input type="hidden" name="inquiry_id" id="reminder_inquiry_id" value="" />
        <input type="hidden" name="id" value="{{ (!empty($inquiry))?$inquiry->id:'' }}" />
        <!-- <input type="hidden" name="type" id="type" value="" /> -->

        <div class="row">
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Log Type</label>
                    <select class="form-control select2 type" id='reminder_type' name='type' required>
                        <option value="">Select</option>
                        <option value="email">Email</option>
                        <option value="calls">Calls</option>
                        <option value="meetings">Meetings</option>
                    </select>
                    @error('type')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group">
                    <label for="source">Source</label>
                    <input type="text" class="form-control source" id="reminder_source" name="source" required />
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <!-- text input -->
                <div class="form-group clearfix">
                    <label>Reminder</label>
                        <input type="text" class="form-control datetimepicker-input reminder" id="reminder_datetimepicker" name="for_created_at" data-toggle="datetimepicker" data-target="#reminder_datetimepicker" autocomplete="off" required/>
                        @error('for_created_at')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                </div>
            </div>
            <!-- <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label>Reminder</label>
                </div>
            </div> -->
            <div class="col-sm-12 col-md-12">
                <!-- text input -->
                <div class="form-group">
                    <label>Summary</label>
                    <textarea name="description" id="reminder_summernote" class="form-control summernote" rows="5"></textarea>
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