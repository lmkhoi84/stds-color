@extends('layouts.master')

@section('title',$title_page)
@section('page_name',$title_page)

@section('style')
@parent
<link rel="stylesheet" href="{{asset('plugins/jstree-master/dist/themes/default/style.min.css')}}" />
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session('alert_messenge'))
        <div class="alert alert-{{session('type')}} alert-dismissible text-center" role="alert">
            {{session('alert_messenge')}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <!-- Account -->
        <form method="post" action="{{url($active_url).'/edit'}}" enctype="multipart/form-data">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <div class="card mb-4">
                <h5 class="card-header">{{$translate->trans_page['edit_title']}}</h5>

                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img src="{{asset('images/users/'.$item->profile_picture)}}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                        <div class="button-wrapper">
                            <label for="profile_picture" class="btn btn-primary me-2 mb-2" tabindex="0">
                                <span class="d-none d-sm-block">{{$translate->trans_page['upload_image']}}</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="profile_picture" name="profile_picture" class="account-file-input" hidden accept="image/png, image/jpeg, image/jpg"/>
                            </label>
                            <button type="button" id="reset" class="btn btn-outline-secondary account-image-reset mb-2">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">{{$translate->trans_page['reset']}}</span>
                            </button>
                            <span class="d-none d-sm-block mb-1" id="show_file">&nbsp;</span>
                            <p class="text-muted mb-0">{{$translate->trans_page['max_size']}}</p>
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                    <div class="row">

                        <div class="mb-3 col-md-6">
                            <label for="full_name" class="form-label {{$errors->has('full_name')?'text-danger':''}}">{{$translate->trans_page['full_name']}}</label>
                            <input class="form-control {{$errors->has('full_name')?'border-danger':''}}" type="text" name="full_name" id="full_name" value="{{session('addUser.full_name')?session('addUser.full_name'):$item->full_name}}" placeholder="{{$errors->has('full_name')?$errors->first('full_name'):$translate->trans_page['full_name_holder']}}" required/>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label {{$errors->has('phone_number')?'text-danger':''}}" for="phone_number">{{$translate->trans_page['phone_number']}}</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">VN (+84)</span>
                                <input type="text" id="phone_number" name="phone_number" value="{{Session::has('addUser.phone_number')?Session::get('addUser.phone_number'):$item->phone_number}}" class="form-control" placeholder="{{$translate->trans_page['phone_number_holder']}}" />
                            </div>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="address" class="form-label {{$errors->has('address')?'text-danger':''}}">{{$translate->trans_page['address']}}</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{Session::has('addUser.address')?Session::get('addUser.address'):$item->address}}" placeholder="{{$translate->trans_page['address_holder']}}" />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="id_card" class="form-label {{$errors->has('id_card')?'text-danger':''}}">{{$translate->trans_page['idCard']}}</label>
                            <input type="text" class="form-control" id="id_card" name="id_card" value="{{Session::has('addUser.id_card')?Session::get('addUser.id_card'):$item->id_card}}" placeholder="{{$translate->trans_page['idCard_holder']}}" />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label {{$errors->has('email')?'text-danger':''}}">{{$translate->trans_page['email']}}</label>
                            <input class="form-control" type="text" id="email" name="email" value="{{Session::has('addUser.email')?Session::get('addUser.email'):$item->email}}" placeholder="{{$errors->has('email')?$errors->first('email'):$translate->trans_page['email_holder']}}" required/>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="password" class="form-label {{$errors->has('password')?'text-danger':''}}">{{$translate->trans_page['password']}}</label>
                            <input class="form-control" type="password" id="password" name="password" placeholder="********" autocomplete="new-password" required/>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="default_language" class="form-label {{$errors->has('default_language')?'text-danger':''}}">{{$translate->trans_page['language']}}</label>
                            <select id="default_language" name="default_language" class="select2 form-select" required>
                                <option value="">{{$translate->trans_page['choose_language']}}</option>
                                @foreach($langs as $lang)
                                <option value="{{$lang->id}}" {{$item->default_language == $lang->id || Session::get('addUser.default_language') == $lang->id?'selected':''}}>{{$lang->languages_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="user_group" class="form-label {{$errors->has('password')?'text-danger':''}}">{{$translate->trans_page['group']}}</label>
                            <select id="user_group" name="user_group" class="select2 form-select" required>
                                <option value="">{{$translate->trans_page['choose_group']}}</option>
                                @foreach($groups as $group)
                                <option value="{{$group->id}}" {{$item->user_group == $group->id || Session::get('addUser.user_group') == $group->id?'selected':''}}>{{$group->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <div class="row">
                                <div class="col-6 mb-sm-0 mb-2">
                                    <label for="status" class="form-label {{$errors->has('status')?'text-danger':''}}">{{$translate->trans_page['status']}}</label>
                                </div>
                                <div class="col-6 text-end">
                                    <div class="form-check form-switch text-end">
                                        <input class="form-check-input float-end" type="checkbox" role="switch" name="status" id="status" value="1" {{$item->status == 1 || Session::has('addUser.status') == 1?"checked":""}}/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 col-12">
                            <label for="address" class="form-label">{{$translate->trans_page['menus_permission']}}</label>
                            <div id="menus_tree"></div>
                            <input type="hidden" name="menus_permission" id="menus_permission" value="">
                        </div>

                    </div>
                </div>
            </div>
            <!-- /Account -->
            <div class="text-center">
                <button type="submit" class="btn btn-info">{{$translate->trans_page['update']}}</button>
                <a href="{{url($active_url)}}" class="btn btn-warning" style="margin-left: 10px !important;">{{$translate->trans_page['back_to_list']}}</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<!-- Jstree -->
<script src="{{asset('plugins/jstree-master/dist/jstree.min.js')}}"></script>
<script src="{{asset('plugins/jstree-master/src/jstree.checkbox.js')}}"></script>

<script>
    $('#profile_picture').change(function() {
        if(this.files && this.files[0]){
            $('#show_file').html(this.value.substring(12));
            let reader = new FileReader();
            reader.onload = function(e){
                
                $('#uploadedAvatar').attr('src',e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    $('#reset').click(function(){
        $('#show_file').html('&nbsp');
        $('#profile_picture').val('');
        $('#uploadedAvatar').attr('src','{{asset("images/users/no-image.jpg")}}');
    });

    $('#menus_tree').jstree({
		'core' : {
			'data' : {
				"url" : "{{asset('json_data/user_menus.json')}}",
				"dataType" : "json" // needed only if you do not supply JSON headers
			}
		},
        "checkbox" : {
            "keep_selected_style" : false,
            'deselect_all': true,
            'three_state' : false,
            },
        "plugins" : [ "checkbox" ]
	});
   
    function click_submit(){
        var menu_ids = [];

        menu_ids =  $("#menus_tree").jstree("get_checked",null,true);
        $('#menus_permission').val(menu_ids.join(","));
    };
</script>
@endsection