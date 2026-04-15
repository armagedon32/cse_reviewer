@extends('layouts.app')

@section('title', 'Renew Student Payment')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Renew Student Access</div>
            <p class="muted">Your 30-day payment window has ended. Submit a new GCash receipt to restore access.</p>
        </div>
        <span class="pill">Payment Renewal</span>
    </div>

    <div class="panel" style="margin-bottom: 16px;">
        <strong>GCash Payment Details</strong>
        <p class="muted">Send payment to: <strong>{{ config('app.gcash_number') }}</strong> ({{ config('app.gcash_name') }})</p>
        <p class="muted" style="margin-bottom: 0;">Pay via GCash, then submit your reference number and receipt. Wait for admin approval before accessing your account again.</p>
    </div>

    <div class="panel">
        <form class="stack" method="POST" action="{{ route('student.payment.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="field">
                <label for="gcash_reference">New GCash Reference Number</label>
                <input id="gcash_reference" type="text" name="gcash_reference" value="{{ old('gcash_reference', $user->gcash_reference) }}" required>
                @error('gcash_reference')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="gcash_receipt">New GCash Receipt Screenshot or PDF</label>
                <input id="gcash_receipt" type="file" name="gcash_receipt" accept=".jpg,.jpeg,.png,.pdf" required>
                @error('gcash_receipt')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <button class="btn" type="submit">Submit renewal payment</button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="margin-top: 12px;">
            @csrf
            <button class="btn secondary" type="submit">Log out</button>
        </form>
    </div>
@endsection
