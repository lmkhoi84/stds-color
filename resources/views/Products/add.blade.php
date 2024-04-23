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

        <form method="POST" action="{{url($active_url).'/add'}}">
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
                <!-- Product Form -->
                <div class="card-body">
                    <div class="tab-content">
                        @foreach ($langs as $lang)
                        @if ($lang->name == session('locale'))
                        <div class="tab-pane fade show active" id="{{$lang->name}}" role="tabpanel">
                            <input type="hidden" name="items" id="items" value="{{Session::has('items')?Session::get('items'):1}}">
                            <div class="mb-3 row">
                                <label for="code" class="col-md-2 col-form-label {{$errors->has('code')?'text-danger':''}}">{{$translate->trans_page['code']}}</label>
                                <div class="col-md-10">
                                    <input class="form-control {{$errors->has('code')?'border-danger':''}}" name="code" type="text" value="{{Session::has('code')?Session::get('code'):''}}" id="code" placeholder="{{$translate->trans_page['code_holder']}}" required />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="productName_{{$lang->name}}" class="col-md-2 col-form-label {{$errors->has('productName_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['name']}}</label>
                                <div class="col-md-10">
                                    <input class="form-control {{$errors->has('productName_'.$lang->name)?'border-danger':''}}" name="productName_{{$lang->name}}" type="text" value="{{Session::has('productName_'.$lang->name)?Session::get('productName_'.$lang->name):''}}" id="productName_{{$lang->name}}" placeholder="{{$translate->trans_page['name_holder']}}" required />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="unit_{{$lang->name}}" class="col-md-2 col-form-label {{$errors->has('unit_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['unit']}}</label>
                                <div class="col-md-10">
                                    <input class="form-control {{$errors->has('unit_'.$lang->name)?'border-danger':''}}" name="unit_{{$lang->name}}" type="text" value="{{Session::has('unit_'.$lang->name)?Session::get('unit_'.$lang->name):''}}" id="unit_{{$lang->name}}" placeholder="{{$translate->trans_page['unit_holder']}}" required />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-md-2 col-form-label">{{$translate->trans_page['status']}}</label>
                                <div class="col-md-10">
                                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="status" id="status1" checked autocomplete="off" value="1" />
                                        <label class="btn btn-outline-primary" for="status1">{{$translate->trans_page['enable']}}</label>
                                        <input type="radio" class="btn-check" name="status" id="status2" autocomplete="off" value="0" />
                                        <label class="btn btn-outline-primary" for="status2">{{$translate->trans_page['disable']}}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-md-2 col-form-label">{{$translate->trans_page['composition']}}</label>
                                <div class="col-md-10">
                                    <div class="table-responsive text-nowrap">
                                        <table id="details" class="table table-hover table-striped">
                                            <thead>
                                                <tr class="bg-dark">
                                                    <th>#</th>
                                                    <th>{{$translate->trans_page['material_code']}}</th>
                                                    <th>{{$translate->trans_page['unit']}}</th>
                                                    <th>{{$translate->trans_page['type']}}</th>
                                                    <th>{{$translate->trans_page['percentage']}}</th>
                                                    <th>{{$translate->trans_page['accuracy']}}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                @php
                                                if (old('items')){
                                                    $items = old('items');
                                                } else if(Session::has('items')){
                                                    $items = Session::get('items');
                                                }else{
                                                    $items = 1;
                                                }
                                                @endphp
                                                @for($i = 1; $i <= $items; $i++) <tr>
                                                    <td>
                                                        <span id="num_{{$i}}">{{$i}}</span>
                                                    </td>
                                                    <td>
                                                        <input class="form-control" name="materialCode_{{$i}}" id="materialCode_{{$i}}" type="text" value="{{Session::has('materialCode_'.$i)?Session::get('materialCode_'.$i):''}}" required alt="{{$i}}" onkeyup="materialDetail_autocomplete(materialsList, this.alt)" />
                                                        <input type="hidden" name="materialId_{{$i}}" id="materialId_{{$i}}" value="{{Session::has('materialId_'.$i)?Session::get('materialId_'.$i):''}}">
                                                    </td>
                                                    <td><input class="form-control" style="width: 65px;" name="unit_{{$i}}" id="unit_{{$i}}" type="text" value="{{Session::has('unit_'.$i)?Session::get('unit_'.$i):''}}" readonly /></td>
                                                    <td><input class="form-control" style="width: 100px;" name="type_{{$i}}" type="text" value="{{Session::has('type_'.$i)?Session::get('type_'.$i):''}}" id="type_{{$i}}" readonly /></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input class="form-control" style="width: 40px;" name="percentage_{{$i}}" type="text" value="{{Session::has('percentage_'.$i)?Session::get('percentage_'.$i):''}}" id="percentage_{{$i}}" required />
                                                            <span class="input-group-text" id="percen_{{$i}}">%</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text" id="acc_{{$i}}">&#177;</span>
                                                            <input class="form-control" style="width: 50px !important;" name="accuracy_{{$i}}" type="text" value="{{Session::has('accuracy_'.$i)?Session::get('accuracy_'.$i):''}}" id="accuracy_{{$i}}" required />
                                                        </div>
                                                    </td>
                                                    </tr>
                                                    @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <div class="col-md-12 text-right">
                                    <input type="button" class="btn btn-success" name="addRow" id="addRow" value="{{$translate->trans_page['addRow']}}" onclick="add_record('details')" />
                                    <input type="button" class="btn btn-danger" name="deleteLastRow" id="deleteLastRow" value="{{$translate->trans_page['deleteLastRow']}}" onclick="delete_record('details', '{{$translate->trans_page['askDeleteLastRow']}}', '{{$translate->trans_page['cantDeleteLastRow']}}' )" />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="formula" class="col-md-2 col-form-label {{$errors->has('formula')?'text-danger':''}}">{{$translate->trans_page['formula']}}</label>
                                <div class="col-md-10">
                                    <textarea class="form-control" id="formula" name="formula" rows="10" style="width: 100%;">{{Session::has('formula')?Session::get('formula'):''}}</textarea>
                                </div>
                            </div>

                        </div>
                        @else
                        <div class="tab-pane fade" id="{{$lang->name}}" role="tabpanel">
                            <div class="mb-3 row">
                                <label for="productName_{{$lang->name}}" class="col-md-2 col-form-label {{$errors->has('productName_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['name']}}</label>
                                <div class="col-md-10">
                                    <input class="form-control {{$errors->has('productName_'.$lang->name)?'border-danger':''}}" name="productName_{{$lang->name}}" type="text" value="{{Session::has('productName_'.$lang->name)?Session::get('productName_'.$lang->name):''}}" id="productName_{{$lang->name}}" placeholder="{{$translate->trans_page['name_holder']}}" required />
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="unit_{{$lang->name}}" class="col-md-2 col-form-label {{$errors->has('unit_'.$lang->name)?'text-danger':''}}">{{$translate->trans_page['unit']}}</label>
                                <div class="col-md-10">
                                    <input class="form-control {{$errors->has('unit_'.$lang->name)?'border-danger':''}}" name="unit_{{$lang->name}}" type="text" value="{{Session::has('unit_'.$lang->name)?Session::get('unit_'.$lang->name):''}}" id="unit_{{$lang->name}}" placeholder="{{$translate->trans_page['unit_holder']}}" required />
                                </div>
                            </div>
                        </div>
                        @endif
                        @php
                        Session::forget('productName_'.$lang->name);
                        Session::forget('unit_'.$lang->name);
                        @endphp
                        @endforeach
                    </div>
                </div>
                <!-- Product Form -->
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-info">{{$translate->trans_page['create_new']}}</button>
                <a href="{{url($active_url)}}" class="btn btn-warning" style="margin-left: 10px !important;">{{$translate->trans_page['back_to_list']}}</a>
            </div>
        </form>
    </div>
</div>
@php
Session::forget('code');
Session::forget('type');
Session::forget('status');
@endphp
@endsection

@section('scripts')
<script src="{{asset('plugins/combotree/jquery.easyui.min.js')}}" type="text/javascript"></script>
<!-- Autoconplete -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script src="{{asset('js/autocomplete.js')}}"></script>
<script src="{{asset('js/script.js')}}"></script>
<script>
    var products = {!! $pList !!};
    $('#code').keyup(function(){
        product_autocomplete('code',products);
    });

    var materialsList = {!! $mList !!};
    function materials_list(rowId) {
        $('#materialCode_' + rowId).keyup(function() {
            materialDetail_autocomplete(materialsList, rowId);
        });
    }
</script>
@endsection