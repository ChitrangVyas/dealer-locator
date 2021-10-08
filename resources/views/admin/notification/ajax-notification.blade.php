<span class="dropdown-item dropdown-header">{{$total_notification}} Notifications</span>
<div class="dropdown-divider"></div>
@foreach ($notifications as $notification)
<a href="#" class="dropdown-item" data-notification="{{$notification->id}}" >
   <span> {!! $notification->getIcon()['tag'] !!} {{ Str::limit($notification->message,35,' ...')}}</span> <span class="badge badge-secondary float-right">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans(null, \Carbon\CarbonInterface::DIFF_RELATIVE_AUTO, true) }}</span>
</a>
<div class="dropdown-divider"></div>
@endforeach
<div class="dropdown-divider"></div>
<a href="{{route('notification.index')}}" class="dropdown-item dropdown-footer">See All Notifications</a>