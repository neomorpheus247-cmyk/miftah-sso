@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">Choose Your Role</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register.role') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="form-label">Register as:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="student" value="student" required>
                                <label class="form-check-label" for="student">Student</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="teacher" value="teacher" required>
                                <label class="form-check-label" for="teacher">Teacher</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Continue</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
