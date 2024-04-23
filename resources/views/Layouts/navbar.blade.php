<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    @section('search')
      @if ($search)
        @include('layouts.search')
      @endif
    @show
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- Language -->
      <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="fi fi-us fis rounded-circle me-1 fs-3"><img src="{{asset('images/language/'.Session::get('locale').'.png')}}" style="width:26px; height:26px;margin-top:-3px;"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          @foreach ($langs as $lang)
          @if ($lang->name != Session::get('locale'))
          <li>
            <a class="dropdown-item selected"  href="{!! route('change-language', [$lang->name]) !!}" data-language="en"><i class="fi fi-us fis rounded-circle fs-4 me-1"><img src="{{asset('images/language/'.$lang->name.'.png')}}" style="width:26px; height:26px;margin-top:-3px;margin-right:2px;"></i><span class="align-middle">{{$lang->languages_name}}</span>
            </a>
          </li>
          @endif
          @endforeach
        </ul>
      </li>
      <!--/ Language -->

      <!-- User -->
      @section('user')
        @include('layouts.user')
      @show
      <!--/ User -->
    </ul>
  </div>
</nav>