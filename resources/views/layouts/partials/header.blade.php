<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <!-- <li class="nav-item d-none d-sm-inline-block">
        <a href="/" class="nav-link">@if (trans()->has('lang.home')) {{ __('lang.home') }} @else Home @endif</a>
      </li> -->
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown notifications">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell bell-icon"></i>
        <span class="badge badge-danger navbar-badge notify-counter"></span>
      </a>
      <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right notification-menu overflow-hidden">
      </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fas fa-user"></i>
          Logged as {{auth()->user()->name}}
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">          
          <a class="dropdown-item edit-profile" href="javascript:void(0);" role="button">
            <i class="fas fa-user-alt"></i> @if (trans()->has('lang.edit_profile')) {{ __('lang.edit_profile') }} @else {{__('Edit Profile')}} @endif
          </a>
          <a class="dropdown-item" href="{{ route('changepassword') }}" role="button">
            <i class="fas fa-lock"></i> @if (trans()->has('lang.change_password')) {{ __('lang.change_password') }} @else {{__('Change Password')}} @endif   
          </a> 
          <div class="dropdown-divider"></div>                       
          <a class="dropdown-item" href="{{ route('logout') }}" >
            <i class="fas fa-sign-out-alt"></i> @if (trans()->has('lang.sign_out')) {{ __('lang.sign_out') }} @else {{__('Sign Out')}} @endif 
          </a> 
        </div>
      </li>
    </ul>
  </nav>