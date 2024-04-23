@extends('layouts.master')

@section('title',$title_page)
@section('page_name',$title_page)

@section('style')
@parent
<link rel="stylesheet" type="text/css" href="{{url('plugins/combotree/themes/default/easyui.css')}}">
<link rel="stylesheet" type="text/css" href="{{url('plugins/combotree/themes/icon.css')}}">
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

        <form method="post" action="{{url($active_url).'/add'}}">
            {{ csrf_field() }}
            <div class="nav-align-top mb-2">
                <ul class="nav nav-pills mb-1" role="tablist">
                    @foreach($langs as $lang)
                    @php
                    if ($lang->name == session('locale')) $active = "active";
                    else $active ="";
                    @endphp
                    <li class="nav-item">
                        <button type="button" class="nav-link {{$active}}" role="tab" data-bs-toggle="tab" data-bs-target="#{{$lang->name}}" aria-controls="navs-pills-top-home" aria-selected="true">
                            {{$lang->languages_name}}
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="card mb-3">
                <h5 class="card-header">{{$translate->trans_page['create_new']}}</h5>
                <!-- Structures Form -->
                <div class="card-body">
                    <div class="tab-content">
                        @foreach ($langs as $lang)
                        @if ($lang->name == session('locale'))
                        <div class="tab-pane fade show active" id="{{$lang->name}}" role="tabpanel">

                            <div class="mb-3 row">
                                <label for="html5-text-input" class="col-md-2 col-form-label">{{$translate->trans_page['parent']}}</label>
                                <div class="col-md-10">
                                    <input class="easyui-combotree form-control" name="parent_id" id="parent_id" data-options="url:'{{url('json_data/structure.json')}}',method:'get'" style="width:100%" value="{{Session::get('parent_id')?Session::get('parent_id'):$parent_id}}">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="html5-text-input" class="col-md-2 col-form-label {{$errors->has('strucName_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['structure_name']}}</label>
                                <div class="col-md-10">
                                    <input class="form-control {{$errors->has('strucName_'.$lang->name)?'border-danger':''}}" name="strucName_{{$lang->name}}" type="text" value="{{old('strucName_'.$lang->name)?old('strucName_'.$lang->name):''}}" id="html5-search-input" placeholder="{{$translate->trans_page['structure_name_holder']}}" required />
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="html5-text-input" class="col-md-2 col-form-label {{$errors->has('icon')?'text-danger':''}}">{{$translate->trans_page['menu_icon']}}</label>
                                <div class="col-md-10">
                                    <input class="form-control {{$errors->has('icon')?'border-danger':''}}" name="icon" type="text" value="{{old('icon')?old('icon'):''}}" id="html5-url-input" placeholder="{{$translate->trans_page['menu_icon_holder']}}" />
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="html5-text-input" class="col-md-2 col-form-label {{$errors->has('sort')?'text-danger':''}}">{{$translate->trans_page['sort']}}</label>
                                <div class="col-md-10">
                                    <input class="form-control {{$errors->has('sort')?'border-danger':''}}" name="sort" type="text" value="{{old('sort')?old('sort'):Session::get('sort')}}" id="html5-url-input" placeholder="{{$translate->trans_page['sort_holder']}}" />
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="html5-number-input" class="col-md-2 col-form-label">{{$translate->trans_page['page_type']}}</label>
                                <div class="col-md-10">
                                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="page_type" id="page_type1" checked autocomplete="off" value="1" />
                                        <label class="btn btn-outline-primary" for="page_type1">{{$translate->trans_page['menu']}}</label>
                                        <input type="radio" class="btn-check" name="page_type" id="page_type2" autocomplete="off" value="2" />
                                        <label class="btn btn-outline-primary" for="page_type2">{{$translate->trans_page['category']}}</label>
                                        <input type="radio" class="btn-check" name="page_type" id="page_type3" autocomplete="off" value="3" />
                                        <label class="btn btn-outline-primary" for="page_type3">{{$translate->trans_page['action']}}</label>
                                    </div>
                                </div>

                            </div>

                            <div class="mb-3 row">
                                <label for="html5-number-input" class="col-md-2 col-form-label">{{$translate->trans_page['status']}}</label>
                                <div class="col-md-10">
                                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="status" id="status1" checked autocomplete="off" value="1" />
                                        <label class="btn btn-outline-primary" for="status1">{{$translate->trans_page['enable']}}</label>
                                        <input type="radio" class="btn-check" name="status" id="status2" autocomplete="off" value="0" />
                                        <label class="btn btn-outline-primary" for="status2">{{$translate->trans_page['disable']}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="tab-pane fade" id="{{$lang->name}}" role="tabpanel">
                            <div class="mb-3 row">
                                <label for="html5-text-input" class="col-md-2 col-form-label {{$errors->has('strucName_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['structure_name']}}</label>
                                <div class="col-md-10">
                                    <input class="form-control {{$errors->has('strucName_'.$lang->name)?'border-danger':''}}" name="strucName_{{$lang->name}}" type="text" value="{{old('strucName_'.$lang->name)?old('strucName_'.$lang->name):''}}" id="html5-search-input" placeholder="{{$translate->trans_page['structure_name_holder']}}" required />
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-info">{{$translate->trans_page['create_new']}}</button>
                <a href="{{url($active_url)}}" class="btn btn-warning" style="margin-left: 10px !important;">{{$translate->trans_page['back_to_list']}}</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{url('plugins/combotree/jquery.easyui.min.js')}}" type="text/javascript"></script>
@endsection