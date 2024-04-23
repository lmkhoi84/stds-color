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
        <!-- Account -->
        <form method="post" action="{{url($active_url).'/edit'}}" enctype="multipart/form-data">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <div class="card mb-4">
                <h5 class="card-header">{{$translate->trans_page['edit_title']}}</h5>

                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img src="{{url('images/users/'.$item->avatar)}}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-2" tabindex="0">
                                <span class="d-none d-sm-block">{{$translate->trans_page['upload_image']}}</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="upload" name="profile_picture" class="account-file-input" hidden accept="image/png, image/jpeg, image/jpg"/>
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
                            <label for="email" class="form-label">{{$translate->trans_page['email']}}</label>
                            <input class="form-control" type="text" id="email" name="email" value="{{$item->email}}" placeholder="{{$translate->trans_page['email_holder']}}" />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="full_name" class="form-label">{{$translate->trans_page['full_name']}}</label>
                            <input class="form-control" type="text" name="full_name" id="full_name" value="{{$item->full_name}}" placeholder="{{$translate->trans_page['full_name_holder']}}" />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="password" class="form-label">{{$translate->trans_page['password']}}</label>
                            <input class="form-control" type="password" id="password" name="password" value="" placeholder="********" autocomplete="new-password"/>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="phoneNumber">{{$translate->trans_page['phone_number']}}</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">VN (+84)</span>
                                <input type="text" id="phoneNumber" name="phoneNumber" value="{{$item->phone}}" class="form-control" placeholder="{{$translate->trans_page['phone_number_holder']}}" />
                            </div>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="address" class="form-label">{{$translate->trans_page['address']}}</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{$item->address}}" placeholder="{{$translate->trans_page['address_holder']}}" />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="language" class="form-label">Language</label>
                            <select id="language" class="select2 form-select">
                                @foreach($langs as $lang)
                                <option value="{{$lang->name}}" {{$lang->name == $langs[$item->langId - 1]->name?'selected':''}}>{{$lang->languages_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Account -->
            <div class="text-center">
                <button type="submit" class="btn btn-info">{{$translate->trans_page['update']}}</button>
                <a href="{{url('home')}}" class="btn btn-warning" style="margin-left: 10px !important;">{{$translate->trans_page['back_to_home']}}</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#upload').change(function() {
        var filename = $('#upload').val();
        filename = filename.substring(12);
        $('#show_file').html(filename);
    });

    $('#reset').click(function(){
        $('#show_file').html('&nbsp');
        $('#upload').val('');
    });
</script>
@endsection