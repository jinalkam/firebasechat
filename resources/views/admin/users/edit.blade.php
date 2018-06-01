@extends('layouts.app')
@section('title', 'SNS | Edit Templates')
@section('content')   


<div class="wrapper wrapper-content animated fadeInRight add-template">
    <div class="row">
        
                {!! Form::model($mastertemplates, [
                    'method' => 'PATCH',
                    'route' => ['mastertemplates.update', $mastertemplates->recid],
                   
                ]) !!}
               <input type="hidden" name="_token" value="{{ csrf_token() }}">
                {{ csrf_field() }}
                <!-- https://laracast.blogspot.in/2016/06/laravel-ajax-crud-search-sort-and.html  -->
                   @include("mastertemplates/_form")
                {{ Form::close() }}

            </div>
        </div>


@endsection

@section('scripts')

<script type="text/javascript">
    var url = "{{ route('templates.data') }}";
</script>
<script src="{{ asset('js/webjs/mastertemplates.js') }}"></script>
@endsection



