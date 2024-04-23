<li class="nav-item navbar-dropdown dropdown-user dropdown">
    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
    <div class="avatar avatar-online">
        <img src="{{asset('images/users/'.$avatar)}}" alt class="w-px-40 h-auto rounded-circle" />
    </div>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
    <li>
        <a class="dropdown-item" href="#">
        <div class="d-flex">
            <div class="flex-shrink-0 me-3">
            <div class="avatar avatar-online">
                <img src="{{asset('images/users/'.$avatar)}}" alt class="w-px-40 h-auto rounded-circle" />
            </div>
            </div>
            <div class="flex-grow-1">
            <span class="fw-semibold d-block">{{Auth::user()->full_name}}</span>
            <small class="text-muted">Admin</small>
            </div>
        </div>
        </a>
    </li>
    <li>
        <div class="dropdown-divider"></div>
    </li>
    <li>
        <a class="dropdown-item" href="{{url('account')}}">
        <i class="bx bx-user me-2"></i>
        <span class="align-middle">{{$menu_temp->trans_page['my_profile']}}</span>
        </a>
    </li>
    <li>
        <div class="dropdown-divider"></div>
    </li>
    <li>
        <a class="dropdown-item" href="{{url('logout')}}">
        <i class="bx bx-power-off me-2"></i>
        <span class="align-middle">{{$menu_temp->trans_page['logout']}}</span>
        </a>
    </li>
    </ul>
</li>