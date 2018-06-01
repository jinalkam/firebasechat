@extends('admin.layouts.admin-auth')

@section('content')


   <div class="ibox-content">
    <div>
       <h3>Welcome to {{ config('app.name') }}  Admin Panel</h3>
    </div>

       

        <form class="m-t" role="form" method="POST" action="{{ route('admin.login') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Username" required autofocus>

                @if ($errors->has('username'))
                    <span class="help-block">
                        <strong>{{ $errors->first('username') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>

                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group pull-left">
                <div class="i-checks">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}><i></i> Remember me
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary block full-width m-b">
                Login
            </button>
        </form>
  
</div>

@endsection
