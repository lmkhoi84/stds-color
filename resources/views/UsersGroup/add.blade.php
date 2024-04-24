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
        @if(Session::has('alert_messenge'))
        <div class="alert alert-{{session('type')}} alert-dismissible text-center" role="alert">
            {{Session::get('alert_messenge')}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Account -->
        <form method="post" action="{{url($active_url).'/add'}}">
            {{ csrf_field() }}
            <div class="card mb-4">
                <h5 class="card-header">{{$translate->trans_page['add_title']}}</h5>
                <hr class="my-0" />
                <div class="card-body">
                    <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label {{$errors->has('name')?'text-danger':''}}">{{$translate->trans_page['name']}} (*)</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('name')?'border-danger':''}}" type="text" name="name" id="name" value="{{Session::has('addUsersGroup.name')?Session::get('addUsersGroup.name'):''}}" placeholder="{{$translate->trans_page['name_holder']}}" required/>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="sort" class="col-md-2 col-form-label {{$errors->has('sort')?'text-danger':''}}">{{$translate->trans_page['sort']}}</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('sort')?'border-danger':''}}" type="text" name="sort" id="sort" value="{{Session::has('addUsersGroup.sort')?Session::get('addUsersGroup.sort'):''}}" placeholder="{{$translate->trans_page['sort_holder']}}"/>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="status" class="col-md-2 col-form-label {{$errors->has('name')?'text-danger':''}}">{{$translate->trans_page['status']}}</label>
                        <div class="col-md-10">
                            <div class="form-check form-switch text-start">
                                <input class="form-check-input" type="checkbox" role="switch" name="status" id="status" value="1" {{Session::has('addUsersGroup.status') && Session::has('addUsersGroup.status') == 1?"checked":""}}/>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="menus_tree" class="col-md-2 col-form-label {{$errors->has('name')?'text-danger':''}}">{{$translate->trans_page['menus_permission']}}</label>
                        <div class="col-md-10">
                            Root
                            <div id="menus_tree"></div>
                            <input type="hidden" name="menus_permission" id="menus_permission" value="">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Account -->
            <div class="text-center">
                <button type="submit" onclick="click_submit()" class="btn btn-info">{{$translate->trans_page['create_new']}}</button>
                <a href="{{url($active_url)}}" class="btn btn-warning" style="margin-left: 10px !important;">{{$translate->trans_page['back_to_list']}}</a>
            </div>
        </form>
        @php
        Session::forget('addUsersGroup');
        @endphp
    </div>
</div>
@endsection

@section('scripts')
<!-- Jstree -->
<script src="{{asset('plugins/jstree-master/dist/jstree.min.js')}}"></script>
<script src="{{asset('plugins/jstree-master/src/jstree.checkbox.js')}}"></script>

<script>
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