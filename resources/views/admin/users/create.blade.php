@extends('layouts.app')

@section('title', 'Create Admin Account')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Create Admin Account</div>
            <p class="muted">Provision a new administrator through the protected management console.</p>
        </div>
        <span class="pill">Restricted Action</span>
    </div>

    <div class="panel">
        <form class="stack" method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="field">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <div class="actions">
                <button class="btn" type="submit">Create admin</button>
            </div>
        </form>
    </div>
@endsection
