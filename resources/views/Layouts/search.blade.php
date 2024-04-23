<div class="navbar-nav align-items-center">
    <div class="nav-item d-flex align-items-center">
        <i class="bx bx-search fs-4 lh-0"></i>
        <input type="text" id="search_text" name="search_text" class="form-control border-0 shadow-none" placeholder="{{$menu_temp->trans_page['search_holder']}}" aria-label="Search..." onkeyup="enter_search()" value="{{isset($key)?$key:''}}"/>
    </div>
</div>