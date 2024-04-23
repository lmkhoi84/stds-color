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
                <!-- Staff Form -->
                <div class="card-body">

                    <div class="mb-3 row">
                        <label for="code" class="col-md-2 col-form-label {{$errors->has('code')?'text-danger':''}}">{{$translate->trans_page['code']}} (*)</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('code')?'border-danger':''}}" type="text" name="code" id="code" value="{{Session::has('code')?Session::get('code'):''}}" placeholder="{{$translate->trans_page['code_holder']}}" required/>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label {{$errors->has('name')?'text-danger':''}}">{{$translate->trans_page['name']}} (*)</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('name')?'border-danger':''}}" type="text" name="name" id="name" value="{{Session::has('name')?Session::get('name'):''}}" placeholder="{{$translate->trans_page['name_holder']}}" required />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="email" class="col-md-2 col-form-label {{$errors->has('email')?'text-danger':''}}">{{$translate->trans_page['email']}} (*)</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('email')?'border-danger':''}}" type="text" name="email" id="email" value="{{Session::has('email')?Session::get('email'):''}}" placeholder="{{$translate->trans_page['email_holder']}}" required/>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="phone" class="col-md-2 col-form-label {{$errors->has('phone')?'text-danger':''}}">{{$translate->trans_page['phone']}} (*)</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('phone')?'border-danger':''}}" type="text" name="phone" id="phone" value="{{Session::has('phone')?Session::get('phone'):''}}" placeholder="{{$translate->trans_page['phone_holder']}}" required/>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="citizen_identity_card" class="col-md-2 col-form-label {{$errors->has('citizen_identity_card')?'text-danger':''}}">{{$translate->trans_page['citizen_identity_card']}}</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('citizen_identity_card')?'border-danger':''}}" type="text" name="citizen_identity_card" id="citizen_identity_card" value="{{Session::has('citizen_identity_card')?Session::get('citizen_identity_card'):''}}" placeholder="{{$translate->trans_page['citizen_identity_card_holder']}}" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="address" class="col-md-2 col-form-label {{$errors->has('address')?'text-danger':''}}">{{$translate->trans_page['address']}}</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('address')?'border-danger':''}}" type="text" name="address" id="address" value="{{Session::has('address')?Session::get('address'):''}}" placeholder="{{$translate->trans_page['address_holder']}}"/>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="birthday" class="col-md-2 col-form-label {{$errors->has('birthday')?'text-danger':''}}">{{$translate->trans_page['birthday']}}</label>
                        <div class="col-md-10">
                            <input class="form-control {{$errors->has('birthday')?'border-danger':''}}" type="text" name="birthday" id="birthday" value="{{Session::has('birthday')?Session::get('birthday'):''}}" placeholder="{{$translate->trans_page['birthday_holder']}}" />
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
                    Session::forget('code');
                    Session::forget('contact_name');
                    Session::forget('phone');
                    Session::forget('email');
                    Session::forget('citizen_identity_card');
                    Session::forget('birthday');
                    Session::forget('status');
                @endphp
                <!-- Staff Form -->
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
<script src="{{url('js/jquery/ui/i18n/jquery.ui.datepicker-'.Session::get('locale').'.js')}}"></script>
<script src="{{url('js/autocomplete.js')}}"></script>
<script>
    var list = {!! $list !!};
    $('#code').keyup(function() {
        staff_autocomplete('code', list);
    });

    $(document).ready(function() {
        $("#birthday").datepicker({
			dateFormat: "dd/mm/yy",
            //minDate: 0,
            maxDate: 0,
            regional: "{{Session::get('locale')}}"
        });
    });
</script>
@endsection