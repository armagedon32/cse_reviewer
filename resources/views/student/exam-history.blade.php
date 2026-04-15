@extends('layouts.app')

@section('title', 'Exam History')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Exam History</div>
            <p class="muted">Review your past attempts and jump to an answer review for each exam.</p>
        </div>
        <a class="btn secondary" href="{{ route('student.dashboard') }}">Back to dashboard</a>
    </div>

    <div class="stack">
        @if ($attempts->isEmpty())
            <div class="panel">
                <strong>No exam history found</strong>
                <p class="muted">You haven't completed any exams yet. Start an exam to generate a history.</p>
            </div>
        @else
            <div class="panel">
                <div class="section-actions" style="justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                    <div>
                        <strong>Your Exam Attempts</strong>
                        <p class="muted" style="margin: 6px 0 0;">Each attempt shows score, date, and review link.</p>
                    </div>
                </div>
            </div>

            <div class="performance-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-top: 18px;">
                @foreach ($attempts as $attempt)
                    <div class="performance-card">
                        <div class="section-actions" style="justify-content: space-between; align-items: flex-start; gap: 10px;">
                            <div>
                                <div class="muted">Submitted</div>
                                <div>{{ $attempt->created_at->format('M j, Y \a\t g:i A') }}</div>
                            </div>
                            <span class="pill" style="background: {{ $attempt->isPassing() ? '#d4f5d7' : '#f8d7da' }}; color: {{ $attempt->isPassing() ? '#2f7a37' : '#a71d2a' }};">
                                {{ $attempt->isPassing() ? 'Passed' : 'Failed' }}
                            </span>
                        </div>

                        <div style="margin-top: 12px;">
                            <div class="muted">Score</div>
                            <div class="metric-value">{{ $attempt->score }}%</div>
                        </div>
                        <div style="margin-top: 10px;">
                            <div class="muted">Correct</div>
                            <div>{{ $attempt->correct_answers }} / {{ $attempt->total_questions }}</div>
                        </div>

                        <div class="section-actions" style="margin-top: 16px;">
                            <a class="btn secondary" href="{{ route('student.exam.review', ['examAttempt' => $attempt]) }}">Review answers</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
