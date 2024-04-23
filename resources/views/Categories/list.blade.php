@extends('layouts.master')

@section('title',$title_page)
@section('page_name',$title_page)

@section('style')
@parent
<link rel="stylesheet" type="text/css" href="{{URL::asset('css/tree-view/dtree.css')}}">
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

        <div class="card mb-4">
            <h5 class="card-header">{{$translate->trans_page['list_title']}}</h5>
            <!-- Structures Tree -->
            <div class="card-body">
                <div id="js-tree-view">
                    <ul>
                        <li><b>{{$translate->trans_page['root']}}</b></li>
                        {!! $tree_view !!}
                        <form method="POST" id="form_delete">
                            {{ method_field('DELETE') }}
                            @csrf
                        </form>
                    </ul>
                </div>
            </div>
            <!-- /Tree -->
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Tree Viewer JS
		============================================ -->
<script src="{{URL::asset('js/tree-view/dtree.js')}}"></script>

<script>
    $(document).ready(function() {
        $("#js-tree-view").dTree();
    });

    function post_delete(id) {
        result = confirm('Bạn muốn xóa menu này ?');
        if (result) {
            $('#form_delete').attr('action', '{{url($active_url).'/delete'}}/' + id);
            $('#form_delete').submit();
        } else {
            return false;
        }
    }
</script>
@endsection