@extends('layouts.app')

@section('title', 'Edit Question')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Edit Question</div>
            <p class="muted">Correct the question text, choices, category, difficulty, or stored answer.</p>
        </div>
        <span class="pill">Question #{{ $question->id }}</span>
    </div>

    <div class="panel">
        <form class="stack" method="POST" action="{{ route('admin.questions.update', $question) }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="question_text">Question</label>
                <textarea id="question_text" name="question_text" rows="4" required>{{ old('question_text', $question->question_text) }}</textarea>
                @error('question_text')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="category">Category</label>
                <input id="category" type="text" name="category" value="{{ old('category', $question->category) }}" required>
                @error('category')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="difficulty">Difficulty</label>
                <select id="difficulty" name="difficulty" required>
                    @foreach ($difficulties as $difficulty)
                        <option value="{{ $difficulty }}" @selected(old('difficulty', $question->difficulty) === $difficulty)>{{ ucfirst($difficulty) }}</option>
                    @endforeach
                </select>
                @error('difficulty')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="option_a">Option A</label>
                <input id="option_a" type="text" name="option_a" value="{{ old('option_a', $question->option_a) }}" required>
                @error('option_a')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="option_b">Option B</label>
                <input id="option_b" type="text" name="option_b" value="{{ old('option_b', $question->option_b) }}" required>
                @error('option_b')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="option_c">Option C</label>
                <input id="option_c" type="text" name="option_c" value="{{ old('option_c', $question->option_c) }}" required>
                @error('option_c')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="option_d">Option D</label>
                <input id="option_d" type="text" name="option_d" value="{{ old('option_d', $question->option_d) }}" required>
                @error('option_d')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="correct_option">Correct Option</label>
                <select id="correct_option" name="correct_option" required>
                    @foreach ($correctOptions as $option)
                        <option value="{{ $option }}" @selected(old('correct_option', $question->correct_option) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                @error('correct_option')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <button class="btn" type="submit">Save changes</button>
                <a class="btn secondary" href="{{ route('admin.questions.preview') }}">Back to preview</a>
            </div>
        </form>
    </div>
@endsection
