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
      
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fas fa-user"></i>
          Logged as {{auth()->user()->name}}
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">          
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