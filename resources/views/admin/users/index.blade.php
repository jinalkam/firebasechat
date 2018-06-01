@extends('admin.layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="breadcrumbs">
            <a href="{{ route('users.index') }}">Dashboard</a> / <span>Users</span>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success" data-dismiss="close" id="alert-message">
                <p>{{ $message }}</p>
            </div>
        @elseif ($message = Session::get('error'))
            <div class="alert alert-success" data-dismiss="close" id="alert-message">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Users</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-12">
                        <form  action="{{ route('admin.savesettings') }}" method="post" class="form-horizontal">
                            {{ csrf_field() }}

                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Date of Birth</th>
                                    <th>Gender</th>
                                    <th>Facebook Id</th>
                                    <th class="nosort">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($Users)
                                    @if(!empty($Users))
                                        @foreach($Users as $key => $value)
                                        <tr class="gradeX">
                                            <td>{{ $key +1  }}</td>
                                            <td>{{ $value['first_name'] . " " . $value['last_name']}}</td>
                                            <td>{{ $value['email'] }}</td>
                                            <td>{{ date('d/m/Y', strtotime($value['dob'])) }}</td>
                                            <td>{{ ($value['gender'] == 'M') ? 'Male' : 'Female' }}</td>
                                            <td>{{ $value['facebook_id'] }}</td>
                                            <td>
                                                <?php /* {{ Html::link('', "", ['class' => 'btn btn-primary btn-xs fa fa-pencil'])}} */ ?>
                                                {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $value['id']]]) !!}
                                                {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs', 'onclick' => 'return confirm("Do you really want to Delete?")']) !!}
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                @endisset
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
{!! Html::script('js/plugins/dataTables/datatables.min.js') !!}
{!! Html::script('js/admin/custom-datatable.js') !!}

@endsection

    


