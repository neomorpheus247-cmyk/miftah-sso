@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Teacher Dashboard</div>

                <div class="card-body">
                    Welcome to the teacher dashboard!
                    <div class="mt-4">
                        <h5>Teacher Actions:</h5>
                        <ul>
                            <li>Create Course</li>
                            <li>Manage Courses</li>
                            <li>View Students</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection