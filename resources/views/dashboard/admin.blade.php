@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Admin Dashboard</div>
            <p class="muted">Operational overview for question inventory, access control, and recent content activity.</p>
        </div>
        <span class="pill">Role: Admin</span>
    </div>

    <div class="stack-lg">
        <div class="metric-grid">
            <div class="metric-card">
                <div class="muted">Total Questions</div>
                <div class="metric-value">{{ $questionCount }}</div>
            </div>
            <div class="metric-card">
                <div class="muted">Latest Upload Window</div>
                <div class="metric-value">{{ $latestQuestions->count() }}</div>
            </div>
            <div class="metric-card">
                <div class="muted">Access Tier</div>
                <div class="metric-value" style="font-size: 1.5rem;">Administrator</div>
            </div>
            <div class="metric-card">
                <div class="muted">Pending Payments</div>
                <div class="metric-value">{{ $pendingPayments }}</div>
            </div>
        </div>

        <div class="panel">
            <strong>Question Library</strong>
            <p class="muted">Maintain the reviewer bank from a single workspace with CSV upload and manual entry for multiple-choice questions.</p>
            <div class="section-actions" style="margin-top: 12px;">
                <a class="btn" href="{{ route('admin.questions.index') }}">Manage questions</a>
            </div>
        </div>

        <div class="panel">
            <strong>Admin account management</strong>
            <p class="muted">Provision new administrators through the protected admin-only path. Public registration remains student-only.</p>
            <div class="section-actions" style="margin-top: 12px;">
                <a class="btn" href="{{ route('admin.users.create') }}">Create admin account</a>
                <a class="btn" href="{{ route('admin.users.index') }}" style="margin-left: 8px;">View all users</a>
            </div>
        </div>

        <div class="panel">
            <strong>Student payment approvals</strong>
            <p class="muted">Review GCash receipts and approve or reject student access before login is allowed. Approved student access now expires every 30 days until payment is renewed.</p>
            <div class="section-actions" style="margin-top: 12px;">
                <a class="btn" href="{{ route('admin.payments.index') }}">Review payments</a>
            </div>
        </div>

        <div class="panel">
            <strong>Latest questions</strong>
            @if ($latestQuestions->isEmpty())
                <p class="muted" style="margin-bottom: 0;">No questions have been uploaded yet.</p>
            @else
                <div class="stack" style="margin-top: 12px;">
                    @foreach ($latestQuestions as $question)
                        <div style="padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                            <div><strong>{{ $question->category }}</strong> | {{ ucfirst($question->difficulty) }}</div>
                            <div class="muted">{{ $question->question_text }}</div>
                            <div class="muted">A. {{ $question->option_a }} | B. {{ $question->option_b }} | C. {{ $question->option_c }} | D. {{ $question->option_d }} | Answer: {{ $question->correct_option }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
