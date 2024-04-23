<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
    <a href="index.html" class="app-brand-link">
        <span class="app-brand-logo demo">
        <img src="{{asset('images/logo/logo.png')}}" style="width: 80px; height:40px; margin-right:5px">
        </span>
        <span class="app-brand-text menu-text fw-bolder h2" style="line-height: 50px; padding-top:15px; color:#02650f">
            <i class="text-dark">C</i>
            <i class="text-warning">o</i>
            <i class="text-success">l</i>
            <i class="text-danger">o</i>
            <i class="text-primary">r</i>
        </span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
    </div>
    
    <div class="menu-inner-shadow"></div>
    {!!makeMenu($structure,$menus_permissions)!!}
    
</aside>