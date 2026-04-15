@extends('layouts.app')

@section('title', 'Question Management')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Question Management</div>
            <p class="muted">Upload, review, and add multiple-choice questions with category and calibrated difficulty.</p>
        </div>
        <span class="pill">MCQ Library</span>
    </div>

    <div class="stack-lg">
        <div class="metric-grid">
            <div class="metric-card">
                <div class="muted">Total Uploaded Questions</div>
                <div class="metric-value">{{ $questionCount }}</div>
            </div>
            <div class="metric-card">
                <div class="muted">Current Page Items</div>
                <div class="metric-value">{{ $questions->count() }}</div>
            </div>
            <div class="metric-card">
                <div class="muted">Question Type</div>
                <div class="metric-value" style="font-size: 1.5rem;">Multiple Choice</div>
            </div>
            <a class="metric-card" href="#duplicate-scan">
                <div class="muted">Duplicate Questions</div>
                <div class="metric-value">{{ $duplicateQuestionCount }}</div>
            </a>
            <a class="metric-card" href="#encoding-cleanup">
                <div class="muted">Encoding Issues</div>
                <div class="metric-value">{{ $encodingIssueCount }}</div>
            </a>
            <a class="metric-card" href="#encoding-cleanup">
                <div class="muted">Auto Fix Candidates</div>
                <div class="metric-value">{{ $autoFixCandidateCount }}</div>
            </a>
        </div>

        <div class="panel">
            <strong>Preview Test Questions</strong>
            <p class="muted">Open a test-style preview page to inspect how the uploaded questions will look in sequence.</p>
            <div class="section-actions" style="margin-top: 12px;">
                <a class="btn" href="{{ route('admin.questions.preview') }}">Preview questions</a>
                <form method="POST" action="{{ route('admin.questions.destroyAll') }}" data-confirm="Delete all uploaded questions? This cannot be undone.">
                    @csrf
                    @method('DELETE')
                    <button class="btn secondary" type="submit">Delete all questions</button>
                </form>
            </div>
        </div>

        <div class="panel" id="duplicate-scan">
            <strong>Duplicate Scan</strong>
            <p class="muted">This scan checks for duplicate questions using normalized question text. When duplicates are found, the oldest copy is kept and later copies can be removed.</p>
            <div class="section-actions" style="margin-top: 12px;">
                <span class="pill">{{ $duplicateGroups->count() }} Duplicate Group(s)</span>
                <form method="POST" action="{{ route('admin.questions.destroyDuplicates') }}" data-confirm="Delete duplicate questions and keep the earliest copy in each group?">
                    @csrf
                    @method('DELETE')
                    <button class="btn secondary" type="submit">Delete duplicate questions</button>
                </form>
            </div>

            @if ($duplicateGroups->isNotEmpty())
                <div class="stack" style="margin-top: 16px;">
                    @foreach ($duplicateGroups as $group)
                        <div class="question-card">
                            <div class="section-actions">
                                <strong>{{ $group['duplicate_count'] }} matching entries</strong>
                                <span class="pill">Keep earliest, remove later copies</span>
                            </div>
                            <div class="stack" style="gap: 10px;">
                                @foreach ($group['questions'] as $index => $question)
                                    <div class="choice-item">
                                        <div class="section-actions" style="margin-bottom: 8px;">
                                            <div><strong>{{ $index === 0 ? 'Keep' : 'Duplicate' }}</strong> | ID {{ $question['id'] }} | {{ $question['category'] }} | {{ ucfirst($question['difficulty']) }}</div>
                                            <a class="btn secondary" href="{{ route('admin.questions.edit', $question['id']) }}">Edit question</a>
                                        </div>
                                        <div class="muted">{{ $question['question_text'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="muted" style="margin-top: 16px; margin-bottom: 0;">No duplicate questions were detected.</p>
            @endif
        </div>

        <div class="panel" id="encoding-cleanup">
            <strong>Encoding Cleanup Tool</strong>
            <p class="muted">This scan finds questions containing the broken replacement character `�`, which usually means the CSV was imported with the wrong text encoding. Use the edit button to correct each affected item.</p>
            <div class="section-actions" style="margin-top: 12px;">
                <span class="pill">{{ $autoFixCandidateCount }} Auto Fix Candidate(s)</span>
                <form method="POST" action="{{ route('admin.questions.fixEncoding') }}" data-confirm="Apply automatic fixes for common broken characters? Review the questions afterward to confirm the replacements.">
                    @csrf
                    @method('PATCH')
                    <button class="btn secondary" type="submit">Auto-fix common encoding issues</button>
                </form>
            </div>

            @if ($encodingIssues->isNotEmpty())
                <div class="stack" style="margin-top: 16px;">
                    @foreach ($encodingIssues as $question)
                        <div class="question-card">
                            <div class="section-actions">
                                <div>
                                    <strong>ID {{ $question->id }}</strong> | {{ $question->category }} | {{ ucfirst($question->difficulty) }}
                                </div>
                                <a class="btn secondary" href="{{ route('admin.questions.edit', $question) }}">Edit question</a>
                            </div>
                            <div class="choice-list">
                                <div class="choice-item"><strong>Question:</strong> {{ $question->question_text }}</div>
                                <div class="choice-item"><strong>A.</strong> {{ $question->option_a }}</div>
                                <div class="choice-item"><strong>B.</strong> {{ $question->option_b }}</div>
                                <div class="choice-item"><strong>C.</strong> {{ $question->option_c }}</div>
                                <div class="choice-item"><strong>D.</strong> {{ $question->option_d }}</div>
                            </div>
                            <div class="section-actions">
                                <span class="pill">Correct Answer: {{ $question->correct_option }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="muted" style="margin-top: 16px; margin-bottom: 0;">No broken replacement characters were detected in the current question bank.</p>
            @endif
        </div>

        <div class="panel">
            <strong>CSV Upload</strong>
            <p class="muted">Use the exact headers: <code>question_text,category,difficulty,option_a,option_b,option_c,option_d,correct_option</code>. Difficulty must be one of: {{ implode(', ', $difficulties) }}. Correct option must be one of: {{ implode(', ', $correctOptions) }}.</p>
            <div class="actions" style="margin-top: 12px; margin-bottom: 12px;">
                <a class="btn secondary" href="{{ route('admin.questions.sample') }}">Download Blank CSV Template</a>
            </div>
            <form class="stack" method="POST" action="{{ route('admin.questions.import') }}" enctype="multipart/form-data" style="margin-top: 16px;">
                @csrf
                <div class="field">
                    <label for="csv_file">CSV file</label>
                    <input id="csv_file" type="file" name="csv_file" accept=".csv,text/csv" required>
                    @error('csv_file')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="actions">
                    <button class="btn" type="submit">Import CSV</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <strong>Add Single Question</strong>
            <form class="stack" method="POST" action="{{ route('admin.questions.store') }}" style="margin-top: 16px;">
                @csrf
                <div class="field">
                    <label for="question_text">Question</label>
                    <textarea id="question_text" name="question_text" rows="4" required>{{ old('question_text') }}</textarea>
                    @error('question_text')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="category">Category</label>
                    <input id="category" type="text" name="category" value="{{ old('category') }}" required>
                    @error('category')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="difficulty">Difficulty</label>
                    <select id="difficulty" name="difficulty" required>
                        <option value="">Select difficulty</option>
                        @foreach ($difficulties as $difficulty)
                            <option value="{{ $difficulty }}" @selected(old('difficulty') === $difficulty)>{{ ucfirst($difficulty) }}</option>
                        @endforeach
                    </select>
                    @error('difficulty')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="option_a">Option A</label>
                    <input id="option_a" type="text" name="option_a" value="{{ old('option_a') }}" required>
                    @error('option_a')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="option_b">Option B</label>
                    <input id="option_b" type="text" name="option_b" value="{{ old('option_b') }}" required>
                    @error('option_b')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="option_c">Option C</label>
                    <input id="option_c" type="text" name="option_c" value="{{ old('option_c') }}" required>
                    @error('option_c')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="option_d">Option D</label>
                    <input id="option_d" type="text" name="option_d" value="{{ old('option_d') }}" required>
                    @error('option_d')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="field">
                    <label for="correct_option">Correct Option</label>
                    <select id="correct_option" name="correct_option" required>
                        <option value="">Select correct option</option>
                        @foreach ($correctOptions as $option)
                            <option value="{{ $option }}" @selected(old('correct_option') === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('correct_option')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="actions">
                    <button class="btn" type="submit">Save question</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <strong>Questions</strong>
            @if ($questions->isEmpty())
                <p class="muted" style="margin-bottom: 0;">No questions have been added yet.</p>
            @else
                <div class="table-wrap desktop-only" style="margin-top: 16px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Category</th>
                                <th>Difficulty</th>
                                <th>Options</th>
                                <th>Answer</th>
                                <th>Actions</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questions as $question)
                                <tr>
                                    <td>{{ $question->question_text }}</td>
                                    <td>{{ $question->category }}</td>
                                    <td>{{ ucfirst($question->difficulty) }}</td>
                                    <td>
                                        <strong>A.</strong> {{ $question->option_a }}<br>
                                        <strong>B.</strong> {{ $question->option_b }}<br>
                                        <strong>C.</strong> {{ $question->option_c }}<br>
                                        <strong>D.</strong> {{ $question->option_d }}
                                    </td>
                                    <td>{{ $question->correct_option }}</td>
                                    <td>
                                        <a class="btn secondary" href="{{ route('admin.questions.edit', $question) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" style="display: inline;" data-confirm="Delete this question? This cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn secondary" type="submit" style="background-color: #dc3545; color: white;">Delete</button>
                                        </form>
                                    </td>
                                    <td>{{ $question->created_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mobile-only" style="margin-top: 16px;">
                    <div class="stack">
                        @foreach ($questions as $question)
                            <div class="question-card">
                                <div class="actions" style="justify-content: space-between; gap: 8px;">
                                    <span class="pill">{{ $question->category }}</span>
                                    <span class="pill">{{ ucfirst($question->difficulty) }}</span>
                                </div>
                                <div style="font-weight: 700;">{{ $question->question_text }}</div>
                                <div class="choice-list">
                                    <div class="choice-item"><strong>A.</strong> {{ $question->option_a }}</div>
                                    <div class="choice-item"><strong>B.</strong> {{ $question->option_b }}</div>
                                    <div class="choice-item"><strong>C.</strong> {{ $question->option_c }}</div>
                                    <div class="choice-item"><strong>D.</strong> {{ $question->option_d }}</div>
                                </div>
                                <div class="section-actions">
                                    <span class="pill">Answer: {{ $question->correct_option }}</span>
                                    <a class="btn secondary" href="{{ route('admin.questions.edit', $question) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" style="display: inline;" data-confirm="Delete this question? This cannot be undone.">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn secondary" type="submit" style="background-color: #dc3545; color: white;">Delete</button>
                                    </form>
                                </div>
                                <div class="muted">{{ $question->created_at?->format('Y-m-d H:i') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="panel" style="margin-top: 16px; padding: 14px 18px;">
                    <div class="section-actions">
                        <div class="muted">
                            Showing {{ $questions->firstItem() }} to {{ $questions->lastItem() }} of {{ $questions->total() }} questions
                        </div>
                        <div class="actions">
                            @if ($questions->onFirstPage())
                                <span class="btn secondary" style="pointer-events: none; opacity: 0.5;">Previous</span>
                            @else
                                <a class="btn secondary" href="{{ $questions->previousPageUrl() }}">Previous</a>
                            @endif

                            <span class="pill">Page {{ $questions->currentPage() }} of {{ $questions->lastPage() }}</span>

                            @if ($questions->hasMorePages())
                                <a class="btn secondary" href="{{ $questions->nextPageUrl() }}">Next</a>
                            @else
                                <span class="btn secondary" style="pointer-events: none; opacity: 0.5;">Next</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
