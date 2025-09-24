@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
        <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M21.35 11.1h-9.17v2.98h5.24c-.23 1.18-1.39 3.47-5.24 3.47-3.15 0-5.72-2.61-5.72-5.83s2.57-5.83 5.72-5.83c1.8 0 3.01.77 3.7 1.43l2.52-2.45C16.44 4.5 14.6 3.5 12.18 3.5 6.98 3.5 2.75 7.73 2.75 12.93s4.23 9.43 9.43 9.43c5.44 0 9.02-3.84 9.02-9.23 0-.62-.07-1.23-.18-1.83z"/></svg>
            Login with Google
        </a>
    </div>
</div>
@endsection
