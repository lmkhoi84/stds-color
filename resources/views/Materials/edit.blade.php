@extends('layouts.master')

@section('title',$title_page)
@section('page_name',$title_page)

@section('style')
@parent
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
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

        <form method="post" action="{{url($active_url).'/edit/'.$item->id}}">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <input type="hidden" name="item_id" value="{{$item->id}}">
            <input type="hidden" name="previous_page" id="previous_page" value="{{url()->previous()}}">
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

            <div class="card mb-4">
                <h5 class="card-header">{{$translate->trans_page['edit']}}</h5>
                <!-- Material Form -->
                <div class="card-body">
                    <div class="tab-content">
                        @foreach ($langs as $lang)
                            @if ($lang->name == session('locale'))
                            <div class="tab-pane fade show active" id="{{$lang->name}}" role="tabpanel">

                            <div class="mb-3 row">
                                    <label for="crayolaCode" class="col-md-2 col-form-label {{$errors->has('crayolaCode')?'text-danger':''}}">{{$translate->trans_page['crayola_code']}}</label>
                                    <div class="col-md-10">
                                        <input class="form-control {{$errors->has('crayolaCode')?'border-danger':''}}" name="crayolaCode" type="text" value="{{Session::has('crayolaCode')?Session::get('crayolaCode'):$item->crayola_code}}" id="crayolaCode" placeholder="{{$translate->trans_page['crayola_code_holder']}}" required />
                                        <input type="hidden" name="materialId" id="materialId" value="{{$item->id}}">
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="crayolaName_{{$lang->name}}" class="col-md-2 col-form-label {{$errors->has('crayolaName_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['crayola_name']}}</label>
                                    <div class="col-md-10">
                                        <input class="form-control {{$errors->has('crayolaName_'.$lang->name)?'border-danger':''}}" name="crayolaName_{{$lang->name}}" type="text" value="{{Session::has('crayolaName_'.$lang->name)?Session::get('crayolaName_'.$lang->name):$cName[$lang->name]}}" id="crayolaName_{{$lang->name}}" placeholder="{{$translate->trans_page['crayola_name_holder']}}" required />
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="producerCode" class="col-md-2 col-form-label {{$errors->has('producerCode')?'text-danger':''}}">{{$translate->trans_page['producer_code']}}</label>
                                    <div class="col-md-10">
                                        <input class="form-control {{$errors->has('producerCode')?'border-danger':''}}" name="producerCode" type="text" value="{{Session::has('producerCode')?Session::get('producerCode'):$item->producer_code}}" id="producerCode" placeholder="{{$translate->trans_page['producer_code_holder']}}" required />
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="producerName_{{$lang->name}}" class="col-md-2 col-form-label {{$errors->has('producerName_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['producer_name']}}</label>
                                    <div class="col-md-10">
                                        <input class="form-control {{$errors->has('producerName_'.$lang->name)?'border-danger':''}}" name="producerName_{{$lang->name}}" type="text" value="{{Session::has('producerName_'.$lang->name)?Session::get('producerName_'.$lang->name):$pName[$lang->name]}}" id="producerName_{{$lang->name}}" placeholder="{{$translate->trans_page['producer_name_holder']}}" required />
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="unit_{{$lang->name}}" class="col-md-2 col-form-label {{$errors->has('unit_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['unit']}}</label>
                                    <div class="col-md-10">
                                        <input class="form-control {{$errors->has('unit_'.$lang->name)?'border-danger':''}}" name="unit_{{$lang->name}}" type="text" value="{{Session::has('unit_'.$lang->name)?Session::get('unit_'.$lang->name):$unit[$lang->name]}}" id="unit_{{$lang->name}}" placeholder="{{$translate->trans_page['unit_holder']}}" required />
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-md-2 col-form-label">{{$translate->trans_page['type']}}</label>
                                    <div class="col-md-10">
                                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                            <input type="radio" class="btn-check" name="type" id="type1" autocomplete="off" value="1" {{$item->type==1?"checked":""}} />
                                            <label class="btn btn-outline-primary" for="type1">{{$translate->trans_page['powder']}}</label>
                                            <input type="radio" class="btn-check" name="type" id="type2" autocomplete="off" value="2" {{$item->type==2?"checked":""}}/>
                                            <label class="btn btn-outline-primary" for="type2">{{$translate->trans_page['liquid']}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-md-2 col-form-label">{{$translate->trans_page['status']}}</label>
                                    <div class="col-md-10">
                                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                            <input type="radio" class="btn-check" name="status" id="status1" autocomplete="off" value="1" {{$item->status==1?"checked":""}}/>
                                            <label class="btn btn-outline-primary" for="status1">{{$translate->trans_page['enable']}}</label>
                                            <input type="radio" class="btn-check" name="status" id="status2" autocomplete="off" value="0" {{$item->status==0?"checked":""}}/>
                                            <label class="btn btn-outline-primary" for="status2">{{$translate->trans_page['disable']}}</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @else
                            <div class="tab-pane fade" id="{{$lang->name}}" role="tabpanel">
                                <div class="mb-3 row">
                                    <label for="crayolaName_{{$lang->name}}" class="col-md-2 col-form-label {{$errors->has('crayolaName_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['crayola_name']}}</label>
                                    <div class="col-md-10">
                                        <input class="form-control {{$errors->has('crayolaName_'.$lang->name)?'border-danger':''}}" name="crayolaName_{{$lang->name}}" type="text" value="{{Session::has('crayolaName_'.$lang->name)?Session::get('crayolaName_'.$lang->name):$cName[$lang->name]}}" id="crayolaName_{{$lang->name}}" placeholder="{{$translate->trans_page['crayola_name_holder']}}" required />
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="producerName_{{$lang->name}}" class="col-md-2 col-form-label {{$errors->has('producerName_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['producer_name']}}</label>
                                    <div class="col-md-10">
                                        <input class="form-control {{$errors->has('producerName_'.$lang->name)?'border-danger':''}}" name="producerName_{{$lang->name}}" type="text" value="{{Session::has('producerName_'.$lang->name)?Session::get('producerName_'.$lang->name):$pName[$lang->name]}}" id="producerName_{{$lang->name}}" placeholder="{{$translate->trans_page['producer_name_holder']}}" required />
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="unit_{{$lang->name}}" class="col-md-2 col-form-label {{$errors->has('unit_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['unit']}}</label>
                                    <div class="col-md-10">
                                        <input class="form-control {{$errors->has('unit_'.$lang->name)?'border-danger':''}}" name="unit_{{$lang->name}}" type="text" value="{{Session::has('unit_'.$lang->name)?Session::get('unit_'.$lang->name):$unit[$lang->name]}}" id="unit_{{$lang->name}}" placeholder="{{$translate->trans_page['unit_holder']}}" required />
                                    </div>
                                </div>
                            </div>
                            @endif
                            @php
                                Session::forget('crayolaCode');
                                Session::forget('producerCode');
                                Session::forget('crayolaName_'.$lang->name);
                                Session::forget('producerName_'.$lang->name);
                                Session::forget('unit_'.$lang->name);
                            @endphp
                        @endforeach
                    </div>
                </div>
                <!-- Material Form -->
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-info">{{$translate->trans_page['update']}}</button>
                <a href="{{url($active_url)}}" class="btn btn-warning" style="margin-left: 10px !important;">{{$translate->trans_page['back_to_list']}}</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{url('plugins/combotree/jquery.easyui.min.js')}}" type="text/javascript"></script>
<!-- Autoconplete -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script src="{{url('js/autocomplete.js')}}"></script>
<script>
    var list = {!! $list !!};
    $('#crayolaCode').keyup(function(){
        material_autocomplete('crayolaCode',list);
    });
</script>
@endsection