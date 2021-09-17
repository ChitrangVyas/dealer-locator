<!-- Timelime example  -->
<div class="row">
    <div class="col-md-12">
        <!-- The time line -->
        <div class="timeline">
            <!-- timeline time label -->
            @foreach ($inquiry_logs as $date=>$logsss)
                <div class="time-label">
                    <span class="bg-red">{{$date}}</span>
                </div>
                <!-- /.timeline-label -->
                <!-- timeline item -->           
                @foreach ( $inquiry->getLogByDate($date) as $log )                
                <div>
                    <i class="fas {{$log->getIconClass()}} bg-blue"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{ date('H:i', strtotime($log->for_created_at)) }} </span>
                        <h3 class="timeline-header">{{$log->getTitle() }}</h3>
                        @if(!empty($log->description))
                        <div class="timeline-body">
                            {!!$log->description !!}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @endforeach
            @if(empty($inquiry_logs)|| count($inquiry_logs) < 1 )
                <div class="jumbotron">
                    <h4 class="text-center text-secondary">No log found</h4>
                </div>
            @endif
        </div>
    </div>
    <!-- /.col -->
</div>