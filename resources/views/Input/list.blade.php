@extends('layouts.master')

@section('title',$title_page)
@section('page_name',$title_page)

@section('style')
@parent
<!-- <link rel="stylesheet" type="text/css" href="{{URL::asset('css/tree-view/dtree.css')}}"> -->
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
            <h5 class="card-header">{{$translate->trans_page['title']}}</h5>
            <!-- Hoverable Table rows -->
            <div class="card-body">

                <div class="container-fluid">
                    <div class="row mb-1">
                        <div class="col-md-5 text-left col-xs-4 mt-1">
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
                        <div class="col-md-7 text-right col-xs-4 text-right">
                            <input type="button" class="btn btn-success text-white export-button" name="excel" id="excel" value=" Excel ">
                            <input type="button" class="btn btn-danger text-white export-button" name="pdf" id="pdf" value=" PDF ">
                            <input type="button" class="btn btn-primary text-white export-button" name="print" id="print" value=" Print ">
                        </div>
                    </div>
                </div>

                <form method="POST" id="form_search">
                    @csrf
                    <div class="table-responsive">
                        <input type="hidden" id="key" name="key" value="{{($key?$key:'')}}">
                        <input type="hidden" id="show" name="show" value="{{($limit?$limit:'')}}">
                        <div class="table-responsive text-nowrap" style="min-height: 300px;">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>{{$translate->trans_page['date']}}</th>
                                        <th>{{$translate->trans_page['number']}}</th>
                                        <th>{{$translate->trans_page['customer']}}</th>
                                        <th>{{$translate->trans_page['amount']}}</th>
                                        <th>{{$translate->trans_page['note']}}</th>
                                        <th>{{$translate->trans_page['actions']}}</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @php
                                    $i = 1;
                                    @endphp
                                    @foreach ($list as $item)
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{format_show_date($item->date)}}</td>
                                        <td class="text-center">{{$item->number}}</td>
                                        <td>{{$item->name}}</td>
                                        <td class="text-right">{{$item->amount*1}}</td>
                                        <td>{{$item->note}}</td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{url($active_url).'/edit/'.$item->id}}"><i class="bx bx-edit-alt me-1"></i> {{$translate->trans_page['edit']}}</a>
                                                    <a class="dropdown-item" href="{{'#delete'.$item->id}}" onclick="post_delete('{{$item->id}}')"><i class="bx bx-trash me-1"></i> {{$translate->trans_page['delete']}}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                    $i++;
                                    @endphp
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="text-center">
                            <!-- Pagination -->
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    {{$list->links()}}
                                </ul>
                            </nav>
                        </div>
                    </div>
                </form>
                <form method="POST" id="form_delete">
                    {{ method_field('DELETE') }}
                    @csrf
                </form>
            </div>
            <!--/ Hoverable Table rows -->
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    function post_delete(id) {
        result = confirm("{{$translate->trans_page['delete_confirm']}}");
        if (result) {
            $('#form_delete').attr('action', '{{url($active_url)}}/delete/' + id);
            $('#form_delete').submit();
        } else {
            return false;
        }
    }

    function click_search() {
        document.location = '{{url($active_url)."?show="}}' + $('#limit').val() + '&search=' + $('#search_text').val();
    }

    function enter_search() {
        if (event.keyCode == 13) {
            document.location = '{{url($active_url)."?show="}}' + $('#limit').val() + '&search=' + $('#search_text').val();
        } else {
            $('#hide_text').val($('#search_text').val());
            return false;
        }
    }

    $('#pdf').click(function() {
        html2canvas($('#products_list')[0], {
            onrendered: function(canvas) {
                var data = canvas.toDataURL();
                var docDefinition = {
                    content: [{
                        image: data,
                        width: 500
                    }]
                };
                pdfMake.createPdf(docDefinition).download("{{$title_page}}.pdf");
            }
        });
    });

    $("#excel").click(function() {
        $("#products_list").table2excel({
            exclude: ".noExl",
            name: "Excel Document Name",
            filename: "{{$title_page}}" + ".xlsx",
            fileext: ".xlsx",
            exclude_img: true,
            exclude_links: true,
            exclude_inputs: true,
            preserveColors: true
        });
    });

    $('#print').click(function() {
        $("#products_list").printThis({
            debug: false, // show the iframe for debugging
            importCSS: true, // import parent page css
            importStyle: false, // import style tags
            printContainer: true, // print outer container/$.selector
            loadCSS: "", // path to additional css file - use an array [] for multiple
            pageTitle: "", // add title to print page
            removeInline: false, // remove inline styles from print elements
            removeInlineSelector: "*", // custom selectors to filter inline styles. removeInline must be true
            printDelay: 1200, // variable print delay
            header: null, // prefix to html
            footer: null, // postfix to html
            base: false, // preserve the BASE tag or accept a string for the URL
            formValues: true, // preserve input/form values
            canvas: false, // copy canvas content
            doctypeString: '...', // enter a different doctype for older markup
            removeScripts: false, // remove script tags from print content
            copyTagClasses: false, // copy classes from the html & body tag
            beforePrintEvent: null, // function for printEvent in iframe
            beforePrint: null, // function called before iframe is filled
            afterPrint: null // function called before iframe is removed
        });
    });
</script>
@endsection