<span class="dropdown-item dropdown-header">{{$total_notification}} Notifications</span>
<div class="dropdown-divider"></div>
@foreach ($notifications as $notification)
<a href="#" class="dropdown-item" data-notification="{{$notification->id}}">
    <!-- <i class="fas fa-envelope mr-2"></i> -->{{$notification->message}}
    <!-- <span class="float-right text-muted text-sm">3 mins</span> -->
</a>
<div class="dropdown-divider"></div>
@endforeach
<div class="dropdown-divider"></div>
<a href="{{route('notification.index')}}" class="dropdown-item dropdown-footer">See All Notifications</a>