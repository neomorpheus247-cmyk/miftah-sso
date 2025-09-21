@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Admin Dashboard</div>

                <div class="card-body">
                    Welcome to the admin dashboard!
                    <div class="mt-4">
                        <h5>Admin Actions:</h5>
                        <ul>
                            <li>Manage Users</li>
                            <li>Manage Roles</li>
                            <li>System Settings</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection