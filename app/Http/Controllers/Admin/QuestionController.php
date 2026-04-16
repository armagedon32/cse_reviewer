<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionController extends Controller
{
    public function index(): View
    {
        $questions = Question::query()->latest()->paginate(10);
        $duplicateGroups = $this->duplicateGroups();
        $encodingIssues = $this->encodingIssues();
        $autoFixCandidates = $this->autoFixCandidates();

        return view('admin.questions.index', [
            'questions' => $questions,
            'questionCount' => Question::query()->count(),
            'duplicateGroups' => $duplicateGroups,
            'duplicateQuestionCount' => $duplicateGroups->sum(fn ($group) => count($group['questions']) - 1),
            'encodingIssues' => $encodingIssues,
            'encodingIssueCount' => $encodingIssues->count(),
            'autoFixCandidates' => $autoFixCandidates,
            'autoFixCandidateCount' => $autoFixCandidates->count(),
            'difficulties' => Question::DIFFICULTIES,
            'correctOptions' => Question::CORRECT_OPTIONS,
        ]);
    }

    public function preview(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $questionsQuery = Question::query()->orderBy('id');

        if ($search !== '') {
            $questionsQuery->where(function ($query) use ($search): void {
                $like = '%'.$search.'%';

                $query
                    ->where('question_text', 'like', $like)
                    ->orWhere('category', 'like', $like)
                    ->orWhere('difficulty', 'like', $like)
                    ->orWhere('option_a', 'like', $like)
                    ->orWhere('option_b', 'like', $like)
                    ->orWhere('option_c', 'like', $like)
                    ->orWhere('option_d', 'like', $like)
                    ->orWhere('correct_option', 'like', $like);
            });
        }

        return view('admin.questions.preview', [
            'questions' => $questionsQuery->get(),
            'questionCount' => Question::query()->count(),
            'search' => $search,
        ]);
    }

    public function downloadSample(): BinaryFileResponse
    {
        $path = storage_path('app/private/sample-cse-questions.csv');

        if (! file_exists($path)) {
            abort(404, "File not found at: $path");
        }

        if (! is_readable($path)) {
            abort(500, "File not readable at: $path");
        }

        return response()->download($path, 'sample-cse-questions.csv');
    }

    public function exportAll(): StreamedResponse
    {
        $filename = 'all-questions-export.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'question_text',
                'category',
                'difficulty',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'correct_option',
            ]);

            Question::query()
                ->orderBy('id')
                ->chunk(200, function ($questions) use ($handle): void {
                    foreach ($questions as $question) {
                        fputcsv($handle, [
                            $question->question_text,
                            $question->category,
                            $question->difficulty,
                            $question->option_a,
                            $question->option_b,
                            $question->option_c,
                            $question->option_d,
                            $question->correct_option,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function edit(Question $question): View
    {
        return view('admin.questions.edit', [
            'question' => $question,
            'difficulties' => Question::DIFFICULTIES,
            'correctOptions' => Question::CORRECT_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateQuestion($request);

        Question::create($validated);

        return redirect()
            ->route('admin.questions.index')
            ->with('status', 'Question created successfully.');
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $validated = $this->validateQuestion($request);

        $question->update($validated);

        return redirect()
            ->route('admin.questions.preview')
            ->with('status', 'Question updated successfully.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $question->delete();

        return redirect()
            ->route('admin.questions.index')
            ->with('status', 'Question deleted successfully.');
    }

    public function destroyAll(): RedirectResponse
    {
        $count = Question::query()->count();
        Question::query()->delete();

        return redirect()
            ->route('admin.questions.index')
            ->with('status', "{$count} question(s) deleted successfully.");
    }

    public function destroyDuplicates(): RedirectResponse
    {
        $groups = $this->duplicateGroups();
        $idsToDelete = [];

        foreach ($groups as $group) {
            $questions = collect($group['questions'])->sortBy('id')->values();

            foreach ($questions->slice(1) as $question) {
                $idsToDelete[] = $question['id'];
            }
        }

        if ($idsToDelete === []) {
            return redirect()
                ->route('admin.questions.index')
                ->with('status', 'No duplicate questions were found.');
        }

        $deleted = Question::query()->whereIn('id', $idsToDelete)->delete();

        return redirect()
            ->route('admin.questions.index')
            ->with('status', "{$deleted} duplicate question(s) deleted successfully.");
    }

    public function fixEncodingIssues(): RedirectResponse
    {
        $questions = $this->autoFixCandidates();
        $updated = 0;

        foreach ($questions as $question) {
            $changes = [];

            foreach (['question_text', 'option_a', 'option_b', 'option_c', 'option_d'] as $field) {
                $fixed = $this->replaceBrokenCharacters((string) $question->{$field});

                if ($fixed !== $question->{$field}) {
                    $changes[$field] = $fixed;
                }
            }

            if ($changes !== []) {
                $question->update($changes);
                $updated++;
            }
        }

        return redirect()
            ->route('admin.questions.index')
            ->with('status', $updated > 0
                ? "{$updated} question(s) were auto-corrected for common encoding issues."
                : 'No auto-fixable encoding issues were found.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return back()->withErrors([
                'csv_file' => 'The uploaded CSV file could not be opened.',
            ]);
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);

            return back()->withErrors([
                'csv_file' => 'The uploaded CSV file is empty.',
            ]);
        }

        $normalizedHeader = array_map(
            fn (?string $value): string => strtolower(trim($this->normalizeCsvValue($value))),
            $header,
        );

        $requiredHeader = ['question_text', 'category', 'difficulty', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_option'];

        if ($normalizedHeader !== $requiredHeader) {
            fclose($handle);

            return back()->withErrors([
                'csv_file' => 'CSV headers must be exactly: question_text, category, difficulty, option_a, option_b, option_c, option_d, correct_option.',
            ]);
        }

        $created = 0;
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if ($this->rowIsEmpty($row)) {
                continue;
            }

            $normalizedRow = array_slice(
                array_pad($row, count($requiredHeader), ''),
                0,
                count($requiredHeader),
            );

            $payload = array_combine($requiredHeader, array_map(
                fn (?string $value): string => trim($this->normalizeCsvValue($value)),
                $normalizedRow,
            ));

            $validator = Validator::make($payload, [
                'question_text' => ['required', 'string'],
                'category' => ['required', 'string', 'max:255'],
                'difficulty' => ['required', Rule::in(Question::DIFFICULTIES)],
                'option_a' => ['required', 'string', 'max:255'],
                'option_b' => ['required', 'string', 'max:255'],
                'option_c' => ['required', 'string', 'max:255'],
                'option_d' => ['required', 'string', 'max:255'],
                'correct_option' => ['required', Rule::in(Question::CORRECT_OPTIONS)],
            ]);

            if ($validator->fails()) {
                fclose($handle);

                return back()->withErrors([
                    'csv_file' => "Row {$rowNumber} is invalid: ".$validator->errors()->first(),
                ]);
            }

            Question::create($validator->validated());
            $created++;
        }

        fclose($handle);

        return redirect()
            ->route('admin.questions.index')
            ->with('status', "{$created} question(s) imported successfully.");
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function validateQuestion(Request $request): array
    {
        return $request->validate([
            'question_text' => ['required', 'string'],
            'category' => ['required', 'string', 'max:255'],
            'difficulty' => ['required', Rule::in(Question::DIFFICULTIES)],
            'option_a' => ['required', 'string', 'max:255'],
            'option_b' => ['required', 'string', 'max:255'],
            'option_c' => ['required', 'string', 'max:255'],
            'option_d' => ['required', 'string', 'max:255'],
            'correct_option' => ['required', Rule::in(Question::CORRECT_OPTIONS)],
        ]);
    }

    private function normalizeCsvValue(?string $value): string
    {
        $value = (string) $value;

        if ($value === '') {
            return '';
        }

        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        $converted = @mb_convert_encoding($value, 'UTF-8', 'Windows-1252,ISO-8859-1,UTF-8');

        return $converted === false ? $value : $converted;
    }

    private function duplicateGroups()
    {
        return Question::query()
            ->selectRaw('LOWER(TRIM(question_text)) as normalized_question')
            ->selectRaw('COUNT(*) as duplicate_count')
            ->whereRaw("TRIM(question_text) != ''")
            ->groupByRaw('LOWER(TRIM(question_text))')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->map(function ($group) {
                $questions = Question::query()
                    ->whereRaw('LOWER(TRIM(question_text)) = ?', [$group->normalized_question])
                    ->orderBy('id')
                    ->get(['id', 'question_text', 'category', 'difficulty', 'created_at'])
                    ->toArray();

                return [
                    'normalized_question' => $group->normalized_question,
                    'duplicate_count' => (int) $group->duplicate_count,
                    'questions' => $questions,
                ];
            });
    }

    private function encodingIssues()
    {
        return Question::query()
            ->where(function ($query): void {
                $query
                    ->where('question_text', 'like', '%�%')
                    ->orWhere('option_a', 'like', '%�%')
                    ->orWhere('option_b', 'like', '%�%')
                    ->orWhere('option_c', 'like', '%�%')
                    ->orWhere('option_d', 'like', '%�%');
            })
            ->orderBy('id')
            ->get([
                'id',
                'question_text',
                'category',
                'difficulty',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'correct_option',
            ]);
    }

    private function autoFixCandidates()
    {
        return Question::query()
            ->where(function ($query): void {
                foreach (array_keys($this->brokenCharacterMap()) as $pattern) {
                    $query
                        ->orWhere('question_text', 'like', '%'.$pattern.'%')
                        ->orWhere('option_a', 'like', '%'.$pattern.'%')
                        ->orWhere('option_b', 'like', '%'.$pattern.'%')
                        ->orWhere('option_c', 'like', '%'.$pattern.'%')
                        ->orWhere('option_d', 'like', '%'.$pattern.'%');
                }
            })
            ->orderBy('id')
            ->get();
    }

    private function replaceBrokenCharacters(string $value): string
    {
        return str_replace(
            array_keys($this->brokenCharacterMap()),
            array_values($this->brokenCharacterMap()),
            $value,
        );
    }

    private function brokenCharacterMap(): array
    {
        return [
            '�' => "'",
            'â€“' => '–',
            'â€œ' => '"',
            'â€' => '"',
            'â€˜' => "'",
            'â€™' => "'",
            'âˆš' => '√',
            'Â±' => '±',
            'Ã·' => '÷',
            'Ã—' => '×',
            'â‰¤' => '≤',
            'â‰¥' => '≥',
            'â‰ˆ' => '≈',
            'â€¦' => '…',
            '‘' => "'",
            '’' => "'",
            '“' => '"',
            '”' => '"',
        ];
    }
}
