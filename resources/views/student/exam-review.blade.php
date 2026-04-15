@extends('layouts.app')

@section('title', 'Exam Review')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Exam Review</div>
            <p class="muted">Review your answers and see which questions were correct or incorrect.</p>
        </div>
        <a class="btn secondary" href="{{ route('student.dashboard') }}">Back to dashboard</a>
    </div>

    <div class="panel">
        <div class="section-actions" style="justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div>
                <strong>Result Summary</strong>
                <p class="muted" style="margin: 6px 0 0;">Your exam score is calculated from all answered items.</p>
            </div>
            <div style="text-align: right;">
                <div class="metric-value" style="font-size: 1.8rem;">{{ $attempt->score }}%</div>
                <div class="muted">{{ $attempt->correct_answers }} correct / {{ $attempt->incorrect_answers }} incorrect</div>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-top: 18px;">
        <strong>Answer Review</strong>
        <p class="muted" style="margin: 6px 0 16px;">Each question shows your selected answer, the correct answer, and whether it was right or wrong.</p>

        @foreach ($attempt->answers as $questionId => $answerData)
            @php
                $answerData = is_object($answerData) ? (array) $answerData : (array) $answerData;
                $questionText = $answerData['question_text'] ?? $answerData['question'] ?? null;
                $options = $answerData['options'] ?? [];
                $selected = $answerData['selected'] ?? null;
                $correct = $answerData['correct'] ?? null;
                $isCorrect = $answerData['is_correct'] ?? false;

                if ($questionText === null || empty($options)) {
                    $question = \App\Models\Question::find($questionId);
                    if ($question) {
                        $questionText = $questionText ?? $question->question_text;
                        $options = $options ?: [
                            'A' => $question->option_a,
                            'B' => $question->option_b,
                            'C' => $question->option_c,
                            'D' => $question->option_d,
                        ];
                    }
                }
            @endphp

            <div class="question-card" style="margin-bottom: 18px;">
                <div class="section-actions" style="justify-content: space-between; gap: 10px;">
                    <div>
                        <strong>Question {{ $loop->iteration }}</strong>
                    </div>
                    <span class="pill" style="background: {{ $isCorrect ? '#d4f5d7' : '#f8d7da' }}; color: {{ $isCorrect ? '#2f7a37' : '#a71d2a' }};">
                        {{ $isCorrect ? 'Correct' : 'Incorrect' }}
                    </span>
                </div>

                <div style="font-weight: 700; margin: 12px 0;">{{ $questionText ?? 'Question text not available' }}</div>
                <div class="choice-list">
                    @foreach ($options as $optionKey => $optionText)
                        <label class="choice-item" style="background: {{ $correct === $optionKey ? '#eaf7e9' : 'transparent' }}; border-color: {{ $selected === $optionKey ? '#2b6cb0' : '#e2e8f0' }};">
                            <span style="display: block; font-weight: {{ $selected === $optionKey ? '700' : '400' }};">
                                {{ $optionKey }}. {{ $optionText }}
                            </span>
                            @if ($selected === $optionKey)
                                <small class="muted">Your answer</small>
                            @endif
                            @if ($correct === $optionKey)
                                <small class="muted">Correct answer</small>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection
