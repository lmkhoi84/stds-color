@extends('layouts.master')

@section('title',$title_page)
@section('page_name',$title_page)

@section('style')
@parent
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
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
            <input type="hidden" name="previous_page" id="previous_page" value="{{Session::has('previous_page')?Session::get('previous_page'):url()->previous()}}">
            <input type="hidden" name="item_id" id="item_id" value="{{$item->id}}">
            <input type="hidden" name="items" id="items" value="{{Session::has('items')?Session::get('items'):$item->items}}">
            <input type="hidden" name="delete_items" id="delete_items" value="{{Session::has('delete_items')?Session::get('delete_items'):''}}">
            
            <div class="card mb-3">
                <h5 class="card-header">{{$translate->trans_page['edit']}}</h5>
                <!-- Product Form -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="mb-3 row">
                            <label for="date" class="col-md-2 col-form-label {{$errors->has('date')?'text-danger':''}}">{{$translate->trans_page['date']}}</label>
                            <div class="col-md-10">
                                <input class="form-control {{$errors->has('date')?'border-danger':''}}" name="date" id="date" type="text" value="{{Session::has('date')?Session::get('date'):$item->date}}" placeholder="{{$translate->trans_page['date_holder']}}" required />
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="staff" class="col-md-2 col-form-label {{$errors->has('staff')?'text-danger':''}}">{{$translate->trans_page['staff']}}</label>
                            <div class="col-md-10">
                                <input class="form-control {{$errors->has('staff')?'border-danger':''}}" name="staff" id="staff" type="text" value="{{Session::has('staff')?Session::get('staff'):$item->staff}}" placeholder="{{$translate->trans_page['staff_holder']}}" required />
                                <input type="hidden" name="staff_id" id="staff_id" value="{{Session::has('staff_id')?Session::get('staff_id'):$item->staff_id}}">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="customer" class="col-md-2 col-form-label {{$errors->has('customer')?'text-danger':''}}">{{$translate->trans_page['customer']}}</label>
                            <div class="col-md-10">
                                <input class="form-control {{$errors->has('customer')?'border-danger':''}}" name="customer" id="customer" type="text" value="{{Session::has('customer')?Session::get('customer'):$item->customer}}" placeholder="{{$translate->trans_page['customer_holder']}}" required />
                                <input type="hidden" name="customer_id" id="customer_id" value="{{Session::has('customer_id')?Session::get('customer_id'):$item->customer_id}}">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="number" class="col-md-2 col-form-label {{$errors->has('number')?'text-danger':''}}">{{$translate->trans_page['number']}}</label>
                            <div class="col-md-10">
                                <input class="form-control {{$errors->has('number')?'border-danger':''}}" name="number" id="number" type="text" value="{{Session::has('number')?Session::get('number'):$item->number}}" placeholder="{{$translate->trans_page['number_holder']}}" required/>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="note" class="col-md-2 col-form-label">{{$translate->trans_page['note']}}</label>
                            <div class="col-md-10">
                                <input class="form-control" name="note" id="note" type="text" value="{{Session::has('note')?Session::get('note'):$item->note}}" />
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">{{$translate->trans_page['details']}}</label>
                            <div class="col-md-10">
                                <div class="table-responsive text-nowrap">
                                    <table id="details" class="table table-hover table-striped">
                                        <thead>
                                            <tr class="bg-dark">
                                                <th>#</th>
                                                <th>{{$translate->trans_page['material']}}</th>
                                                <th>{{$translate->trans_page['unit']}}</th>
                                                <th>{{$translate->trans_page['type']}}</th>
                                                <th>{{$translate->trans_page['quantity']}}</th>
                                                <th>{{$translate->trans_page['price']}}</th>
                                                <th>{{$translate->trans_page['total']}}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            @php
                                                Session::forget('code');
                                                if(Session::has('items')){
                                                    $items = Session::get('items');
                                                }else{
                                                    $items = $item->items;
                                                }
                                                $i = 1;
                                                @endphp
                                            @foreach ($itemDetails as $detail)
                                            <tr>
                                                <td>
                                                    <span id="num_{{$i}}">{{$i}}</span>
                                                    <input type="hidden" name="idDetail_{{$i}}" id="idDetail_{{$i}}" value="{{$detail->id}}">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="materialCode_{{$i}}" id="materialCode_{{$i}}" type="text" value="{{Session::has('materialCode_'.$i)?Session::get('materialCode_'.$i):$detail->crayola_code.' / '.$detail->crayola_name}}" required alt="{{$i}}" onkeyup="materialDetail_autocomplete(mList, this.alt)" />
                                                    <input type="hidden" name="materialId_{{$i}}" id="materialId_{{$i}}" value="{{Session::has('materialId_'.$i)?Session::get('materialId_'.$i):$detail->material_id}}">
                                                </td>
                                                <td><input class="form-control" style="width: 65px;" name="unit_{{$i}}" id="unit_{{$i}}" type="text" value="{{Session::has('unit_'.$i)?Session::get('unit_'.$i):$detail->unit}}" disabled /></td>
                                                <td><input class="form-control" style="width: 70px;" name="type_{{$i}}" type="text" value="{{Session::has('type_'.$i)?Session::get('type_'.$i):($detail->type==1?$translate->trans_page['powder']:$translate->trans_page['liquid'])}}" id="type_{{$i}}" disabled /></td>
                                                <td>
                                                    <input class="form-control text-right" style="width: 100px;" name="quantity_{{$i}}" id="quantity_{{$i}}" alt="{{$i}}" type="text" value="{{Session::has('quantity_'.$i)?Session::get('quantity_'.$i):$detail->quantity*1}}" required onkeypress="check_number('quantity_{{$i}}')" onchange="calculate_qty(this.alt)" />
                                                </td>
                                                
                                                <td>
                                                    <input class="form-control text-right" style="width: 100px;" name="price_{{$i}}" id="price_{{$i}}" alt="{{$i}}" type="text" value="{{Session::has('price_'.$i)?Session::get('price_'.$i):$detail->price*1}}" onkeypress="check_number('price_{{$i}}')" onchange="calculate_qty(this.alt)"/>
                                                </td>
                                                <td>
                                                    <input class="form-control text-right" style="width: 100px;" name="total_{{$i}}" id="total_{{$i}}" type="text" value="{{Session::has('total_'.$i)?Session::get('total_'.$i):($detail->quantity*$detail->price)}}" disabled />
                                                </td>
                                            </tr>
                                                @php
                                                    Session::forget('materialCode_'.$i);
                                                    Session::forget('materialId_'.$i);
                                                    Session::forget('unit_'.$i);
                                                    Session::forget('type_'.$i);
                                                    Session::forget('quantity_'.$i);
                                                    Session::forget('price_'.$i);
                                                    Session::forget('total_'.$i);
                                                    $i++;
                                                @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="amount" class="col-md-2 col-form-label">{{$translate->trans_page['amount']}}</label>
                            <div class="col-md-10">
                                <input class="form-control text-right" style="padding-right: 35px;" name="amount" id="amount" type="text" value="{{Session::has('amount')?Session::get('amount'):format_number($item->amount*1)}}" disabled />
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-12 text-right">
                                <input type="button" class="btn btn-success" name="addRow" id="addRow" value="{{$translate->trans_page['addRow']}}" onclick="add_record('details')" />
                                <input type="button" class="btn btn-danger" name="deleteLastRow" id="deleteLastRow" value="{{$translate->trans_page['deleteLastRow']}}" onclick="delete_record('details', '{{$translate->trans_page['askDeleteLastRow']}}', '{{$translate->trans_page['cantDeleteLastRow']}}' )" />
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Product Form -->
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-info">{{$translate->trans_page['update']}}</button>
                <a href="{{url($active_url)}}" class="btn btn-warning" style="margin-left: 10px !important;">{{$translate->trans_page['back_to_list']}}</a>
            </div>
        </form>
    </div>
</div>
@php
    Session::forget('date');
    Session::forget('staff');
    Session::forget('staff_id');
    Session::forget('customer');
    Session::forget('customer_id');
    Session::forget('number');
    Session::forget('note');
@endphp
@endsection

@section('scripts')
<script src="{{asset('plugins/combotree/jquery.easyui.min.js')}}" type="text/javascript"></script>
<!-- Autoconplete -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script src="{{asset('js/jquery/ui/i18n/jquery.ui.datepicker-'.Session::get('locale').'.js')}}"></script>
<script src="{{asset('js/autocomplete.js')}}"></script>
<script src="{{asset('js/script.js')}}"></script>
<script>
    var sList = {!! $sList !!};
    $('#staff').keyup(function() {
        staffInOut_autocomplete('staff', sList);
    });

    var cList = {!! $cList !!};
    $('#customer').keyup(function() {
        customer_autocomplete('customer', cList);
    });

    var mList = {!! $mList !!};

    function materials_list(rowId) {
        $('#materialCode_' + rowId).keyup(function() {
            materialDetail_autocomplete(mList, rowId);
        });
    }

    var url = '{{url("ajax/input-output")}}';
    function calculate_qty(id){
        calculate_formula(id,url);
    }

    $("#date").datepicker({
        dateFormat: "dd/mm/yy",
        //minDate: 0,
        maxDate: 0,
        regional: "{{Session::get('locale')}}"
    });
</script>
@endsection