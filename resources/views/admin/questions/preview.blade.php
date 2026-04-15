@extends('layouts.app')

@section('title', 'Preview Test Questions')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Preview Test Questions</div>
            <p class="muted">Review the uploaded multiple-choice questions in a test-style sequence.</p>
        </div>
        <span class="pill">{{ $questionCount }} Total Questions</span>
    </div>

    <div class="stack-lg">
        <div class="panel">
            <strong>Preview Mode</strong>
            <p class="muted">This view shows question wording, choices, category, difficulty, and the stored correct answer for verification. Use search to locate a specific uploaded item quickly.</p>
            <form class="stack" method="GET" action="{{ route('admin.questions.preview') }}" style="margin-top: 12px;">
                <div class="field">
                    <label for="search">Search Questions</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" placeholder="Search by question, category, difficulty, choices, or answer">
                </div>
                <div class="actions">
                    <button class="btn" type="submit">Search</button>
                    <a class="btn secondary" href="{{ route('admin.questions.preview') }}">Clear</a>
                    <a class="btn secondary" href="{{ route('admin.questions.export') }}">Download All Questions CSV</a>
                </div>
            </form>
            <div class="actions" style="margin-top: 12px;">
                <a class="btn secondary" href="{{ route('admin.questions.index') }}">Back to question management</a>
            </div>
        </div>

        @forelse ($questions as $index => $question)
            <div class="question-card">
                <div class="section-actions" style="margin-bottom: 4px;">
                    <div>
                        <strong>Question {{ $index + 1 }}</strong>
                    </div>
                    <div class="actions">
                        <span class="pill">{{ $question->category }}</span>
                        <span class="pill">{{ ucfirst($question->difficulty) }}</span>
                        <a class="btn secondary" href="{{ route('admin.questions.edit', $question) }}">Edit question</a>
                        <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" style="display: inline;" data-confirm="Delete this question? This cannot be undone.">
                            @csrf
                            @method('DELETE')
                            <button class="btn secondary" type="submit" style="background-color: #dc3545; color: white;">Delete</button>
                        </form>
                    </div>
                </div>

                <div style="font-size: 1.05rem; font-weight: 600; margin-bottom: 16px;">
                    {{ $question->question_text }}
                </div>

                <div class="choice-list">
                    <div class="choice-item">
                        <strong>A.</strong> {{ $question->option_a }}
                    </div>
                    <div class="choice-item">
                        <strong>B.</strong> {{ $question->option_b }}
                    </div>
                    <div class="choice-item">
                        <strong>C.</strong> {{ $question->option_c }}
                    </div>
                    <div class="choice-item">
                        <strong>D.</strong> {{ $question->option_d }}
                    </div>
                </div>

                <div class="section-actions" style="margin-top: 4px;">
                    <span class="pill">Correct Answer: {{ $question->correct_option }}</span>
                </div>
            </div>
        @empty
            <div class="panel">
                <strong>No Questions Available</strong>
                <p class="muted" style="margin-bottom: 0;">Upload a CSV or add a question manually before using preview mode.</p>
            </div>
        @endforelse
    </div>
@endsection
