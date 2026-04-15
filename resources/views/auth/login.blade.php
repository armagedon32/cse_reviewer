@extends('layouts.app')

@section('title', 'Login')

@section('content')
    @if ($errors->any())
        <div class="notification notification-error">
            <span class="notif-icon">✕</span>
            <div class="notif-content">
                <div class="notif-title">Error</div>
                <div class="notif-text">{{ $errors->first() }}</div>
            </div>
        </div>
    @endif

    <div class="topbar">
        <div>
            <div class="brand">CSE Reviewer</div>
            <p class="muted">Students can log in only after GCash payment approval. Access expires every 30 days until a new payment is approved.</p>
        </div>
        <a class="btn secondary" href="{{ route('register') }}">Create account</a>
    </div>

    <form class="stack" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>

        <label>
            <input type="checkbox" name="remember" value="1"> Remember me
        </label>

        <div class="actions">
            <button class="btn" type="submit">Log in</button>
        </div>
    </form>
@endsection
