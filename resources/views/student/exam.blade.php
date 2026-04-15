@extends('layouts.app')

@section('title', 'Start Exam')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Start Exam</div>
            <p class="muted">Answer random questions from the bank to simulate a real exam session.</p>
            <div style="margin-top: 10px; display: flex; gap: 18px; flex-wrap: wrap;">
                <div style="font-size: 0.95rem;">
                    <span class="muted">Account:</span>
                    <strong>{{ auth()->user()->name }}</strong>
                </div>
                <div style="font-size: 0.95rem; word-break: break-all;">
                    <span class="muted">Email:</span>
                    <strong>{{ auth()->user()->email }}</strong>
                </div>
            </div>
        </div>
        <a class="btn secondary" href="{{ route('student.dashboard') }}">Back to dashboard</a>
    </div>

    <div class="stack">
        @if ($questions->isEmpty())
            <div class="panel">
                <strong>No questions available</strong>
                <p class="muted">The question bank is empty. Ask the administrator to upload exam questions.</p>
            </div>
        @else
            <div class="panel">
                <div class="section-actions" style="justify-content: space-between; align-items: center; gap: 16px;">
                    <div>
                        <strong>Exam Session</strong>
                        <p class="muted">Review the randomly selected questions below. This is a real exam session with a one-hour timer.</p>
                    </div>
                    <div style="text-align: right;">
                        <div class="muted">Time remaining</div>
                        <div id="exam-timer" class="pill" style="font-size: 0.95rem; padding: 10px 14px;">60:00</div>
                    </div>
                </div>
            </div>

            <form id="exam-form" method="POST" action="{{ route('student.exam.submit') }}">
                @csrf

                <div id="exam-questions">
                    @foreach ($questions as $index => $question)
                        <div class="question-card">
                            <div class="section-actions" style="justify-content: space-between; gap: 10px;">
                                <div>
                                    <strong>Question {{ $index + 1 }}</strong>
                                </div>
                            </div>

                            <div style="font-weight: 700; margin-bottom: 12px;">{{ $question->question_text }}</div>
                            <div class="choice-list">
                                <label class="choice-item">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="A" required>
                                    <span>A. {{ $question->option_a }}</span>
                                </label>
                                <label class="choice-item">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="B">
                                    <span>B. {{ $question->option_b }}</span>
                                </label>
                                <label class="choice-item">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="C">
                                    <span>C. {{ $question->option_c }}</span>
                                </label>
                                <label class="choice-item">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="D">
                                    <span>D. {{ $question->option_d }}</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="panel" style="margin-top: 18px;">
                    <strong>Submit Exam</strong>
                    <p class="muted">When you are ready, submit your answers to record your score.</p>
                    <button class="btn" type="submit">Submit exam</button>
                </div>
            </form>

            <script id="exam-data" type="application/json">
                {!! json_encode($questions->map(fn ($question) => [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'option_a' => $question->option_a,
                    'option_b' => $question->option_b,
                    'option_c' => $question->option_c,
                    'option_d' => $question->option_d,
                ])->all()) !!}
            </script>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timerElement = document.getElementById('exam-timer');
            const examForm = document.getElementById('exam-form');
            const examDataElement = document.getElementById('exam-data');
            const serverSeconds = Math.max(0, {{ $remainingSeconds ?? 0 }});
            let remainingSeconds = serverSeconds;
            const storageKey = 'student_exam_state';
            const questionCardsContainer = document.getElementById('exam-questions');
            const serverQuestions = examDataElement ? JSON.parse(examDataElement.textContent || '[]') : [];

            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }

            function currentQuestionIds() {
                return serverQuestions.map((question) => question.id.toString());
            }

            function getStoredState() {
                const stored = localStorage.getItem(storageKey);
                if (!stored) {
                    return null;
                }

                try {
                    return JSON.parse(stored);
                } catch (e) {
                    return null;
                }
            }

            function saveState(state) {
                localStorage.setItem(storageKey, JSON.stringify(state));
            }

            function removeState() {
                localStorage.removeItem(storageKey);
            }

            function saveAnswers() {
                const answers = {};
                examForm.querySelectorAll('input[name^="answers"]').forEach((input) => {
                    if (input.checked) {
                        const match = input.name.match(/answers\[(\d+)\]/);
                        if (match) {
                            answers[match[1]] = input.value;
                        }
                    }
                });

                const storedState = getStoredState() || {};
                storedState.answers = answers;
                saveState(storedState);
            }

            function restoreAnswers(answers) {
                if (!answers) {
                    return;
                }

                Object.entries(answers).forEach(([questionId, selectedValue]) => {
                    const selector = `input[name="answers[${questionId}]"][value="${selectedValue}"]`;
                    const input = document.querySelector(selector);
                    if (input) {
                        input.checked = true;
                    }
                });
            }

            function bindAnswerInputs() {
                examForm.querySelectorAll('input[name^="answers"]').forEach((input) => {
                    input.addEventListener('change', saveAnswers);
                });
            }

            function buildQuestionCard(question, index, answers) {
                const selectedAnswer = answers?.[question.id]?.toString() ?? null;
                return `
                    <div class="question-card">
                        <div class="section-actions" style="justify-content: space-between; gap: 10px;">
                            <div>
                                <strong>Question ${index + 1}</strong>
                            </div>
                        </div>
                        <div style="font-weight: 700; margin-bottom: 12px;">${question.question_text}</div>
                        <div class="choice-list">
                            <label class="choice-item">
                                <input type="radio" name="answers[${question.id}]" value="A" ${selectedAnswer === 'A' ? 'checked' : ''} required>
                                <span>A. ${question.option_a}</span>
                            </label>
                            <label class="choice-item">
                                <input type="radio" name="answers[${question.id}]" value="B" ${selectedAnswer === 'B' ? 'checked' : ''}>
                                <span>B. ${question.option_b}</span>
                            </label>
                            <label class="choice-item">
                                <input type="radio" name="answers[${question.id}]" value="C" ${selectedAnswer === 'C' ? 'checked' : ''}>
                                <span>C. ${question.option_c}</span>
                            </label>
                            <label class="choice-item">
                                <input type="radio" name="answers[${question.id}]" value="D" ${selectedAnswer === 'D' ? 'checked' : ''}>
                                <span>D. ${question.option_d}</span>
                            </label>
                        </div>
                    </div>
                `;
            }

            function renderQuestions(questions, answers) {
                questionCardsContainer.innerHTML = questions
                    .map((question, index) => buildQuestionCard(question, index, answers))
                    .join('');
            }

            function initializeState() {
                const storedState = getStoredState();
                const now = Date.now();

                if (storedState && storedState.expiresAt && storedState.expiresAt > now && Array.isArray(storedState.questions)) {
                    const storedIds = storedState.questions.map((q) => q.id.toString());
                    const currentIds = currentQuestionIds();
                    remainingSeconds = Math.max(0, Math.round((storedState.expiresAt - now) / 1000));

                    if (storedIds.length && currentIds.length && storedIds.join(',') !== currentIds.join(',')) {
                        renderQuestions(storedState.questions, storedState.answers || {});
                    }

                    saveState({
                        questions: storedState.questions,
                        answers: storedState.answers || {},
                        expiresAt: storedState.expiresAt,
                    });
                    return;
                }

                const expiresAt = Date.now() + serverSeconds * 1000;
                saveState({
                    questions: serverQuestions,
                    answers: {},
                    expiresAt,
                });
            }

            if (examForm) {
                initializeState();
                bindAnswerInputs();
                examForm.addEventListener('submit', removeState);

                const storedState = getStoredState();
                if (storedState && storedState.answers) {
                    restoreAnswers(storedState.answers);
                }
            }

            function tick() {
                remainingSeconds -= 1;
                remainingSeconds = Math.max(0, remainingSeconds);
                timerElement.textContent = formatTime(remainingSeconds);

                if (remainingSeconds <= 0) {
                    clearInterval(intervalId);
                    if (examForm) {
                        examForm.submit();
                    }
                }
            }

            if (timerElement && examForm) {
                timerElement.textContent = formatTime(remainingSeconds);
                if (remainingSeconds <= 0) {
                    return;
                }
                const intervalId = setInterval(tick, 1000);
            }
        });
    </script>
@endsection