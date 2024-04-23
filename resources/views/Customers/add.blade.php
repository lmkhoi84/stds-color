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
            <div class="card mb-3">
                <h5 class="card-header">{{$translate->trans_page['create_new']}}</h5>
                <!-- Customer Form -->
                <div class="card-body">
                    <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label {{$errors->has('name')?'text-danger':''}}">{{$translate->trans_page['name']}} (*)</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('name')?'border-danger':''}}" type="text" name="name" id="name" value="{{Session::has('name')?Session::get('name'):''}}" placeholder="{{$translate->trans_page['name_holder']}}" required />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="address" class="col-md-2 col-form-label {{$errors->has('address')?'text-danger':''}}">{{$translate->trans_page['address']}} (*)</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('address')?'border-danger':''}}" type="text" name="address" id="address" value="{{Session::has('address')?Session::get('address'):''}}" placeholder="{{$translate->trans_page['address_holder']}}" required/>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="tax_code" class="col-md-2 col-form-label {{$errors->has('tax_code')?'text-danger':''}}">{{$translate->trans_page['tax_code']}}</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('tax_code')?'border-danger':''}}" type="text" name="tax_code" id="tax_code" value="{{Session::has('tax_code')?Session::get('tax_code'):''}}" placeholder="{{$translate->trans_page['tax_code_holder']}}" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="contact_name" class="col-md-2 col-form-label {{$errors->has('contact_name')?'text-danger':''}}">{{$translate->trans_page['contact_name']}}</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('contact_name')?'border-danger':''}}" type="text" name="contact_name" id="contact_name" value="{{Session::has('contact_name')?Session::get('contact_name'):''}}" placeholder="{{$translate->trans_page['contact_name_holder']}}" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="contact_phone" class="col-md-2 col-form-label {{$errors->has('contact_phone')?'text-danger':''}}">{{$translate->trans_page['contact_phone']}}</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('contact_phone')?'border-danger':''}}" type="text" name="contact_phone" id="contact_phone" value="{{Session::has('contact_phone')?Session::get('contact_phone'):''}}" placeholder="{{$translate->trans_page['contact_phone_holder']}}" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="contact_email" class="col-md-2 col-form-label {{$errors->has('contact_email')?'text-danger':''}}">{{$translate->trans_page['contact_email']}}</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('contact_email')?'border-danger':''}}" type="text" name="contact_email" id="contact_email" value="{{Session::has('contact_email')?Session::get('contact_email'):''}}" placeholder="{{$translate->trans_page['contact_email_holder']}}" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="consignee_name" class="col-md-2 col-form-label {{$errors->has('consignee_name')?'text-danger':''}}">{{$translate->trans_page['consignee_name']}}</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('consignee_name')?'border-danger':''}}" type="text" name="consignee_name" id="consignee_name" value="{{Session::has('consignee_name')?Session::get('consignee_name'):''}}" placeholder="{{$translate->trans_page['consignee_name_holder']}}" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="consignee_phone" class="col-md-2 col-form-label {{$errors->has('consignee_phone')?'text-danger':''}}">{{$translate->trans_page['consignee_phone']}}</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('consignee_phone')?'border-danger':''}}" type="text" name="consignee_phone" id="consignee_phone" value="{{Session::has('consignee_phone')?Session::get('consignee_phone'):''}}" placeholder="{{$translate->trans_page['consignee_phone_holder']}}" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="delivery_address" class="col-md-2 col-form-label {{$errors->has('delivery_address')?'text-danger':''}}">{{$translate->trans_page['delivery_address']}}</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('delivery_address')?'border-danger':''}}" type="text" name="delivery_address" id="delivery_address" value="{{Session::has('delivery_address')?Session::get('delivery_address'):''}}" placeholder="{{$translate->trans_page['delivery_address_holder']}}" />
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
                </div>
                @php
                    Session::forget('name');
                    Session::forget('address');
                    Session::forget('tax_code');
                    Session::forget('contact_name');
                    Session::forget('contact_phone');
                    Session::forget('contact_email');
                    Session::forget('consignee_name');
                    Session::forget('consignee_phone');
                    Session::forget('delivery_address');
                    Session::forget('status');
                @endphp
                <!-- Material Form -->
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
<!-- Autoconplete -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script src="{{url('js/autocomplete.js')}}"></script>
<script>
    var list = {!! $list !!};
    $('#name').keyup(function() {
        customer_autocomplete('name', list);
    });
</script>
@endsection