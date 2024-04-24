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

        <div class="card mb-4">
            <h5 class="card-header">{{$translate->trans_page['list_title']}}</h5>
            <!-- List user -->
            <div class="card-body">

                <div class="container-fluid mb-3 ps-0">
                    <div class="row mb-1">
                        <div class="col-lg-3 col-md-6 col-sm-12 text-left col-xs-4 mt-1">
                            <label>{{$translate->trans_page['show']}} &nbsp;</label>
                            <select class="form-control-sm" id="limit" name="limit" onchange="click_search()">
                                <option value="30" {{($limit==30?'selected':'')}}>30</option>
                                <option value="50" {{($limit==50?'selected':'')}}>50</option>
                                <option value="100" {{($limit==100?'selected':'')}}>100</option>
                                <option value="0" {{(empty($limit)?'selected':'')}}>
                                    {{$translate->trans_page['all']}}
                                </option>
                            </select>
                            <label>&nbsp; {{$translate->trans_page['rows_per_page']}}</label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive text-nowrap" id="table-list">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-white">#</th>
                                <th class="text-white">{{$translate->trans_page['name']}}</th>
                                <th class="text-center text-white">{{$translate->trans_page['status']}}</th>
                                <th class="text-center text-white">{{$translate->trans_page['action']}}</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @php $i=1; @endphp
                            @foreach ($itemsList as $item)
                            <tr>
                                <td scope="row">{{$i}}</td>
                                <td>{{$item->name}}</td>
                                <td class="text-center">
                                    <a href="{{url($active_url).'/change-status/'.$item->id}}">
                                        {!!$item->status == 0?'<img src="'.asset('images/table/status_0.png').'">':'<img src="'.asset('images/table/status_1.png').'">'!!}</a>
                                </td>
                                <td class="text-center"><a href="{{url($active_url).'/edit/'.$item->id}}" class="text-primary"><i class="bx bx-edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <a href="{{'#delete'.$item->id}}" id="{{$item->id}}" onclick="post_delete(this.id)" class="text-danger ml-3"><i class="bx bx-trash"></i></a>
                                </td>
                            </tr>
                            @php $i++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                    
                </div>
                <div class="col-lg-12">
                    <div class="text-center">
                        <!-- Pagination -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                {{$itemsList->links()}}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!-- /List -->
        </div>
    </div>
</div>
<form method="POST" id="form_delete">
    {{ method_field('DELETE') }}
    @csrf
</form>
@endsection

@section('scripts')
<script>
    function post_delete(id) {
        result = confirm("{{$translate->trans_page['ask_delete']}}");
        if (result) {
            $('#form_delete').attr('action', '{{url($active_url).'/delete'}}/' + id);
            $('#form_delete').submit();
        } else {
            return false;
        }
    }
</script>
@endsection