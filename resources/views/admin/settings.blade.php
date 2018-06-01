@extends('admin.layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="breadcrumbs">
            <a href="{{ route('admin.settings') }}">Dashboard</a> / <span>Settings</span>
        </div>
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Settings</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form  action="{{ route('admin.savesettings') }}" method="post" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="form-group">
             
                                <label>   Check For One To One Conversation</label>
                                <input type="checkbox" name="one_to_one_verification" id="title" value="1" {{ $settings[1]->status==1? 'checked':''}} />
                            </div>
                          
                            <div class="form-group text-center marg-top15">
                                <button type="submit" class="btn btn-md btn-primary">Submit</button>
                                <button type="button" class="btn btn-md btn-primary" onclick="window.location = '{{ route('admin.settings') }}'">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
