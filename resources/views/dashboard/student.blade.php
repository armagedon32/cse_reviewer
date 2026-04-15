@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Student Dashboard</div>
            <p class="muted">Welcome back, {{ $user->name }}. Your student access and payment details are displayed below.</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn secondary" type="submit">Log out</button>
        </form>
    </div>

    <div class="panel">
        <div class="section-actions" style="align-items: flex-start; gap: 20px;">
            <div>
                <div class="brand" style="font-size: 1.65rem; margin-bottom: 6px;">Performance Overview</div>
                <p class="muted" style="margin: 0;">This is the first thing you should see. Review your overall exam readiness and latest result.</p>
            </div>
            <div class="actions" style="align-items: center; gap: 12px; margin-top: 4px;">
                <a class="btn" href="{{ route('student.payment.edit') }}">Manage payment</a>
            </div>
        </div>

        <div class="performance-grid">
            <div class="performance-card">
                <div class="muted">Average Score</div>
                <div class="metric-value">{{ $examSummary['overallScore'] }}%</div>
            </div>
            <div class="performance-card">
                <div class="muted">Target Passing</div>
                <div class="metric-value">{{ $examSummary['targetPassing'] }}%</div>
            </div>
            <div class="performance-card">
                <div class="muted">Status</div>
                <div class="status-chip">{{ $examSummary['status'] }}</div>
            </div>
        </div>

        @if (! empty($examSummary['recentAttempts']) && count($examSummary['recentAttempts']) > 0)
            <div class="panel" style="margin-top: 18px; padding: 18px;">
                <div class="section-actions" style="justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <strong>Performance Trend</strong>
                        <p class="muted" style="margin: 6px 0 0;">Track your last {{ count($examSummary['recentAttempts']) }} scores.</p>
                    </div>
                </div>
                <div style="position: relative; width: 100%; min-height: 220px; max-height: 320px;">
                    <canvas id="performance-chart" style="width: 100%; height: 100%; display: block;"></canvas>
                </div>
            </div>
        @endif

        <div class="panel" style="margin-top: 18px; padding: 18px;">
            <strong>Account Information</strong>
            <div class="section-actions" style="margin-top: 12px; gap: 16px; flex-wrap: wrap;">
                <div class="performance-card" style="padding: 14px; min-width: 220px;">
                    <div class="muted">Name</div>
                    <div class="metric-value" style="font-size: 1.4rem;">{{ $user->name }}</div>
                </div>
                <div class="performance-card" style="padding: 14px; min-width: 260px;">
                    <div class="muted">Email</div>
                    <div class="metric-value" style="font-size: 1.4rem; word-break: break-all;">{{ $user->email }}</div>
                </div>
                <div class="performance-card" style="padding: 14px; min-width: 220px;">
                    <div class="muted">Subscription</div>
                    @if ($user->paymentExpiresAt() && $user->paymentExpiresAt()->isFuture())
                        <div class="metric-value" style="font-size: 1.4rem;">Active until {{ $user->paymentExpiresAt()->format('M j, Y') }}</div>
                        <div class="muted" style="margin-top: 4px; font-size: 0.95rem;">{{ $user->paymentExpiresAt()->diffForHumans(now(), true) }} remaining</div>
                    @elseif ($user->paymentExpiresAt())
                        <div class="metric-value" style="font-size: 1.4rem;">Expired on {{ $user->paymentExpiresAt()->format('M j, Y') }}</div>
                    @else
                        <div class="metric-value" style="font-size: 1.4rem;">No active subscription</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="panel" style="margin-top: 18px; padding: 18px;">
            <div class="section-actions" style="justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                <div>
                    <strong>Exam Readiness</strong>
                    <p class="muted" style="margin: 6px 0 0;">
                        @if ($examSummary['hasExam'])
                            You are currently at {{ $examSummary['readiness'] }}% of your target readiness.
                        @else
                            No exam results available yet.
                        @endif
                    </p>
                </div>
                <div class="progress-label">
                    <span>{{ $examSummary['readiness'] }}% complete</span>
                    <small>Goal: {{ $examSummary['targetPassing'] }}%</small>
                </div>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width: {{ $examSummary['readiness'] }}%;"></div>
            </div>
        </div>

        <div class="panel" style="margin-top: 18px;">
            <div class="section-actions" style="align-items: flex-start; gap: 16px; flex-wrap: wrap;">
                <div>
                    <strong>Start Exam</strong>
                    <p class="muted" style="margin: 6px 0 0;">Begin an exam session composed of random questions from the question bank.</p>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a class="btn" href="{{ route('student.exam') }}">Start exam</a>
                    <a class="btn secondary" href="{{ route('student.exam.history') }}">Exam history</a>
                    @if ($examSummary['latestAttemptId'])
                        <a class="btn secondary" href="{{ route('student.exam.review', ['examAttempt' => $examSummary['latestAttemptId']]) }}">Review last exam</a>
                    @endif
                </div>
            </div>
            <div class="performance-grid" style="margin-top: 18px; gap: 14px;">
                <div class="performance-card">
                    <div class="muted">Exam Attempts</div>
                    <div class="metric-value">{{ $examSummary['attempts'] }}</div>
                </div>
                <div class="performance-card">
                    <div class="muted">Latest Result</div>
                    <div class="metric-value">{{ $examSummary['latestResult'] ?? '—' }}</div>
                </div>
                <div class="performance-card">
                    <div class="muted">Best Score</div>
                    <div class="metric-value">{{ $examSummary['bestScore'] }}%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <strong>Ready to continue?</strong>
        <p class="muted">Keep your access active by maintaining a valid GCash payment. If you need to renew, use the payment management button below.</p>
        <div class="section-actions" style="margin-top: 18px; gap: 12px; flex-wrap: wrap;">
            @if ($user->paymentExpiresAt() && $user->paymentExpiresAt()->isFuture())
                <button class="btn" type="button" disabled style="opacity: 0.55; cursor: not-allowed; pointer-events: none;">
                    Renew payment
                </button>
                <span class="muted" style="font-size: 0.95rem; margin-top: 6px;">Your subscription remains active until {{ $user->paymentExpiresAt()->format('M j, Y') }}.</span>
            @else
                <a class="btn" href="{{ route('student.payment.edit') }}">Renew payment</a>
            @endif
            <a class="btn secondary" href="{{ route('home') }}">Return home</a>
        </div>
    </div>

    @if (! empty($examSummary['recentAttempts']) && count($examSummary['recentAttempts']) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const labels = @json($examSummary['recentAttempts']->map(fn($attempt) => $attempt->created_at->format('M j')));
                const data = @json($examSummary['recentAttempts']->map(fn($attempt) => $attempt->score));

                const ctx = document.getElementById('performance-chart');
                if (!ctx) {
                    return;
                }

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Score',
                            data,
                            fill: true,
                            backgroundColor: 'rgba(38, 99, 224, 0.12)',
                            borderColor: 'rgba(37, 99, 235, 1)',
                            pointBackgroundColor: 'rgba(37, 99, 235, 1)',
                            tension: 0.25,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif
@endsection
