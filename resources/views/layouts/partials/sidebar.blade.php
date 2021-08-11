<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
      <img src="{{ asset('assets/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">TechCelerity</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('assets/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{auth()->user()->name}}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div><div class="sidebar-search-results"><div class="list-group"><a href="#" class="list-group-item"><div class="search-title"><strong class="text-light"></strong>N<strong class="text-light"></strong>o<strong class="text-light"></strong> <strong class="text-light"></strong>e<strong class="text-light"></strong>l<strong class="text-light"></strong>e<strong class="text-light"></strong>m<strong class="text-light"></strong>e<strong class="text-light"></strong>n<strong class="text-light"></strong>t<strong class="text-light"></strong> <strong class="text-light"></strong>f<strong class="text-light"></strong>o<strong class="text-light"></strong>u<strong class="text-light"></strong>n<strong class="text-light"></strong>d<strong class="text-light"></strong>!<strong class="text-light"></strong></div><div class="search-path"></div></a></div></div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          
          <li class="nav-item">
            <a href="/admin/home" class="nav-link {{ $elementActive == 'home' ? 'active' : '' }}">
              <i class="nav-icon fas fa-home"></i>
              <p>
                Home
              </p>
            </a>
          </li>
          <!-- <li class="nav-item">
            <a href="/contact" class="nav-link  {{ $elementActive == 'contact' ? 'active' : '' }}">
            <i class="nav-icon fas fa-id-card-alt"></i>
              <p>
                Contact us
              </p>
            </a>
          </li> -->
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>
                User Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">  
              <li class="nav-item">
                <a href="/admin/staff" class="nav-link  {{ $elementActive == 'staff' ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                  <p>
                    Staff User
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/admin/dealer" class="nav-link  {{ $elementActive == 'dealer' ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                  <p>
                    Dealer
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/admin/retailer" class="nav-link  {{ $elementActive == 'retailer' ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                  <p>
                    Retailer
                  </p>
                </a>
              </li>  
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Catalog Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/admin/manufacturer" class="nav-link  {{ $elementActive == 'manufacturer' ? 'active' : '' }}">
                  <i class="nav-icon fas fa-industry"></i>
                  <p>
                  Manufacturer
                  </p>
                </a>
              </li>  
              <li class="nav-item">
                <a href="{{ route('brand.index') }}" class="nav-link  {{ $elementActive == 'brand' ? 'active' : '' }}">
                <i class="nav-icon fas fa-cube"></i>
                  <p>Brand</p>
                </a>
              </li> 
          </li> 
              </li> 
          </li> 
              </li> 
              <li class="nav-item">
                <a href="{{ route('category.index') }}" class="nav-link  {{ $elementActive == 'category' ? 'active' : '' }}">
                <i class="nav-icon fas fa-list-alt"></i>
                  <p>Category</p>
                </a>
              </li> 
          </li> 
              </li> 
          </li> 
              </li> 
              <li class="nav-item">
                <a href="{{ route('product.index') }}" class="nav-link  {{ $elementActive == 'product' ? 'active' : '' }}">
                <i class="nav-icon fas fa-cubes"></i>
                  <p>Product</p>
                </a>
              </li> 
          </li> 
              </li> 
          </li> 
              </li> 
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{ route('inquiries-source.index') }}" class="nav-link  {{ $elementActive == 'users' ? 'active' : '' }}">
            <i class="nav-icon fas fa-link"></i>
              <p>Inquiry source</p>
            </a>
          </li> 
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>