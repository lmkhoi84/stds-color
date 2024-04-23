@extends('layouts.master')

@section('title',$title_page)
@section('page_name',$title_page)

@section('style')
@parent

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

        <form method="post" action="{{url($active_url).'/edit'}}">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <input type="hidden" name="item_id" value="{{$item_id}}">
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
                <h5 class="card-header">{{$translate->trans_page['translate_list']}}</h5>
                <!-- Structures Form -->

                <div class="card-body">
                    <div class="tab-content">
                        @foreach ($langs as $lang)
                        @if ($lang->name == session('locale'))
                        <div class="tab-pane fade show active" id="{{$lang->name}}" role="tabpanel">
                            @foreach($key_lang as $key)
                            <div class="mb-3 row">
                                <label for="html5-text-input" class="col-md-2 col-form-label">{{$key}}</label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control" id="{{$key}}" name="{{$key}}[{{$lang->id}}]" value="{{isset($data[$lang->name][$key])?$data[$lang->name][$key]:''}}">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="tab-pane fade" id="{{$lang->name}}" role="tabpanel">
                            @foreach($key_lang as $key)
                            <div class="mb-3 row">
                                <label for="html5-text-input" class="col-md-2 col-form-label">{{$key}}</label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control" id="{{$key}}" name="{{$key}}[{{$lang->id}}]" value="{{isset($data[$lang->name][$key])?$data[$lang->name][$key]:''}}">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
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

@endsection