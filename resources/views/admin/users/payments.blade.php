@extends('layouts.app')

@section('title', 'Student Payments')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Student Payments</div>
            <p class="muted">Approve GCash submissions before student accounts can sign in.</p>
        </div>
        <span class="pill">{{ $students->count() }} Student Record(s)</span>
    </div>

    <div class="stack-lg">
        @forelse ($students as $student)
            <div class="question-card">
                <div class="section-actions">
                    <div>
                        <strong>{{ $student->name }}</strong>
                        <div class="muted">{{ $student->email }}</div>
                    </div>
                    <div class="actions">
                        <span class="pill">Status: {{ ucfirst($student->effectivePaymentStatus()) }}</span>
                    </div>
                </div>

                <div class="stack">
                    <div class="muted">GCash Reference: {{ $student->gcash_reference ?? 'Not submitted' }}</div>
                    <div class="muted">Submitted: {{ $student->payment_submitted_at?->format('Y-m-d H:i') ?? 'Not submitted' }}</div>
                    <div class="muted">Approved: {{ $student->payment_approved_at?->format('Y-m-d H:i') ?? 'Not approved' }}</div>
                    <div class="muted">Access valid until: {{ $student->paymentExpiresAt()?->format('Y-m-d H:i') ?? 'Not available' }}</div>
                </div>

                <div class="section-actions">
                    @if ($student->gcash_receipt_path)
                        @if ($student->effectivePaymentStatus() === 'expired')
                            <span class="muted" style="padding: 8px 14px; border: 1px dashed #999; border-radius: 6px; filter: grayscale(100%); opacity: 0.5;" title="Receipt hidden - subscription expired">Receipt (expired)</span>
                        @else
                            <a class="btn secondary" href="{{ route('admin.payments.receipt', $student) }}">Download receipt</a>
                        @endif
                    @else
                        <span class="muted">No receipt uploaded</span>
                    @endif

                    <div class="actions">
                        @if ($student->gcash_receipt_path)
                            @if ($student->effectivePaymentStatus() === 'expired')
                                <button class="btn" type="button" disabled style="opacity: 0.5; cursor: not-allowed;" title="Cannot approve - awaiting new payment">Awaiting renewal</button>
                            @else
                                <form method="POST" action="{{ route('admin.payments.approve', $student) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn" type="submit">Approve</button>
                                </form>
                            @endif
                        @else
                            <button class="btn" type="button" disabled style="opacity: 0.5; cursor: not-allowed;">Approve</button>
                        @endif
                        <form method="POST" action="{{ route('admin.payments.reject', $student) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn secondary" type="submit">Reject</button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $student) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn secondary" type="submit" style="background: #dc3545; color: white; border-color: #dc3545;" onclick="return confirm('Delete this user completely?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="panel">
                <strong>No Student Payments</strong>
                <p class="muted" style="margin-bottom: 0;">There are no student payment records to review yet.</p>
            </div>
        @endforelse
    </div>
@endsection
