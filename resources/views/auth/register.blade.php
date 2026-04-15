@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Create account</div>
            <p class="muted">Student access is activated only after GCash payment submission and admin approval.</p>
        </div>
        <a class="btn secondary" href="{{ route('login') }}">Back to login</a>
    </div>

    <div class="panel" style="margin-bottom: 16px;">
        <strong>GCash Payment Details</strong>
        <p class="muted">Send payment to: <strong>{{ config('app.gcash_number') }}</strong> ({{ config('app.gcash_name') }})</p>
        <p class="muted" style="margin-bottom: 0;">Pay first via GCash, then submit your reference number and receipt here. Your account cannot log in until an admin approves the payment.</p>
    </div>

    <form class="stack" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
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

        <div class="field">
            <label for="gcash_reference">GCash Reference Number</label>
            <input id="gcash_reference" type="text" name="gcash_reference" value="{{ old('gcash_reference') }}" required>
            @error('gcash_reference')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="gcash_receipt">GCash Receipt Screenshot or PDF</label>
            <input id="gcash_receipt" type="file" name="gcash_receipt" accept=".jpg,.jpeg,.png,.pdf" required>
            @error('gcash_receipt')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="actions">
            <button class="btn" type="submit">Submit registration and payment</button>
        </div>
    </form>
@endsection
