@extends('admin.layouts.admin')
@section('title', 'SNS | Add Templates')
@section('content')   

<div class="wrapper wrapper-content animated fadeInRight add-template">
    <div class="row">
        
                {{ Form::open() }}
                {{ csrf_field() }}
                     @include("admin.users.form")
                {{ Form::close() }}

    </div>
</div>
@endsection




