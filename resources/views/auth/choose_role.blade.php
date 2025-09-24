@extends('layouts.app')

@section('title', 'Choose Role')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Choose Your Role</h2>
        <form method="POST" action="{{ route('role.select') }}">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-semibold">Select a role:</label>
                <select name="role" class="w-full border rounded px-3 py-2">
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 transition">Continue</button>
        </form>
    </div>
</div>
@endsection
