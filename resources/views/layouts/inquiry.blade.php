<ul class="todo-list" data-widget="inquiry-list">
    @if(!empty($inquiries))
    @foreach ($inquiries as $inquiry)
    <li>
        <!-- drag handle -->
        <a href="{{route('inquiry.index')}}" class="btn btn-link">
            <span class="handle">
                <i class="fas fa-ellipsis-v"></i>
                <i class="fas fa-ellipsis-v"></i>
            </span>
            <!-- todo text -->
            <span class="text text-secondary">{{$inquiry->customer->name}}</span>
            <span class="text text-secondary">{{$inquiry->customer->email}}</span>
            <small class="badge badge-primary">{{$inquiry->getInquirystatusName()}}</small>
        </a>
        <span class="badge badge-secondary float-right"><i class="far fa-clock mr-1"></i>{{date('h:i A',strtotime($inquiry->created_at))}}</span>
            <span class="badge badge-secondary float-right"><i class="far fa-calendar-alt mr-1"></i>{{date('d/m/Y',strtotime($inquiry->created_at))}}</span>
    </li>
    @endforeach
    @else
    <li>
        <p class="text-center"><h4 class="text-center text-secondary">NO Inquiries</h4></p>
    </li>
    @endif
</ul>