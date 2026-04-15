<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();

        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('student.dashboard');
    }

    public function admin(Request $request): View
    {
        return view('dashboard.admin', [
            'user' => $request->user(),
            'questionCount' => Question::query()->count(),
            'latestQuestions' => Question::query()->latest()->limit(5)->get(),
            'pendingPayments' => User::query()
                ->where('role', 'student')
                ->where('payment_status', User::PAYMENT_PENDING)
                ->count(),
        ]);
    }

    public function student(Request $request): View
    {
        $user = $request->user();

        if ($user->requiresPaymentRenewal()) {
            return redirect()
                ->route('student.payment.edit')
                ->with('status', 'Your 30-day access period has expired. Upload a new GCash payment receipt to continue.');
        }

        $attemptCount = $user->examAttempts()->count();
        $latestAttempt = $user->examAttempts()->latest()->first();
        $averageScore = $attemptCount > 0
            ? (int) round($user->examAttempts()->average('score'))
            : 0;

        $recentAttempts = $user->examAttempts()
            ->latest()
            ->limit(10)
            ->get()
            ->reverse()
            ->values();

        return view('dashboard.student', [
            'user' => $user,
            'examSummary' => [
                'overallScore' => $attemptCount > 0 ? $averageScore : 0,
                'targetPassing' => 80,
                'status' => $attemptCount > 0
                    ? ($latestAttempt->isPassing() ? 'Passed' : 'Needs Improvement')
                    : 'No exam taken yet',
                'readiness' => $attemptCount > 0 ? $latestAttempt->score : 0,
                'latestResult' => $attemptCount > 0 ? $latestAttempt->score : null,
                'attempts' => $attemptCount,
                'bestScore' => $attemptCount > 0 ? $user->examAttempts()->max('score') : 0,
                'latestAttemptId' => $attemptCount > 0 ? $latestAttempt->id : null,
                'hasExam' => $attemptCount > 0,
                'recentAttempts' => $recentAttempts,
            ],
        ]);
    }

    public function exam(Request $request): View
    {
        $examSession = $request->session()->get('student_exam');
        $questions = collect();
        $remainingSeconds = 0;
        $expiresAt = null;

        if (is_array($examSession)
            && ! empty($examSession['questions'])
            && ! empty($examSession['expires_at'])
            && is_numeric($examSession['expires_at'])) {
            $expiresAt = Carbon::createFromTimestamp($examSession['expires_at']);

            if ($expiresAt->isFuture()) {
                $questions = collect($examSession['questions'])
                    ->map(fn (array $question): object => (object) $question);

                $remainingSeconds = max(0, $expiresAt->diffInSeconds(now()));
            }
        }

        if ($questions->isEmpty() || $remainingSeconds <= 0) {
            $questions = Question::query()->inRandomOrder()->limit(50)->get();
            $expiresAt = now()->addHour();
            $remainingSeconds = 60 * 60;

            $request->session()->put('student_exam', [
                'questions' => $questions->map(fn (Question $question) => [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'option_a' => $question->option_a,
                    'option_b' => $question->option_b,
                    'option_c' => $question->option_c,
                    'option_d' => $question->option_d,
                ])->all(),
                'started_at' => now()->timestamp,
                'expires_at' => $expiresAt->timestamp,
            ]);
        }

        return view('student.exam', [
            'questions' => $questions,
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    public function submitExam(Request $request): RedirectResponse
    {
        $request->session()->forget('student_exam');

        $answers = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'string'],
        ])['answers'];

        $questionIds = array_keys($answers);
        $questions = Question::query()
            ->whereIn('id', $questionIds)
            ->get()
            ->keyBy('id');

        $totalQuestions = $questions->count();
        $correctAnswers = 0;
        $recordedAnswers = [];

        foreach ($answers as $questionId => $selectedOption) {
            $questionId = (int) $questionId;
            $question = $questions->get($questionId);
            if (! $question) {
                continue;
            }

            $normalizedAnswer = strtoupper(trim($selectedOption));
            $isCorrect = $normalizedAnswer === $question->correct_option;
            $recordedAnswers[$questionId] = [
                'question_text' => $question->question_text,
                'options' => [
                    'A' => $question->option_a,
                    'B' => $question->option_b,
                    'C' => $question->option_c,
                    'D' => $question->option_d,
                ],
                'selected' => $normalizedAnswer,
                'correct' => $question->correct_option,
                'is_correct' => $isCorrect,
            ];

            if ($isCorrect) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0
            ? (int) round($correctAnswers / $totalQuestions * 100)
            : 0;

        ExamAttempt::create([
            'user_id' => $request->user()->id,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'incorrect_answers' => $totalQuestions - $correctAnswers,
            'answers' => $recordedAnswers,
        ]);

        $attempt = ExamAttempt::create([
            'user_id' => $request->user()->id,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'incorrect_answers' => $totalQuestions - $correctAnswers,
            'answers' => $recordedAnswers,
        ]);

        return redirect()
            ->route('student.exam.review', ['examAttempt' => $attempt])
            ->with('status', "Exam submitted successfully. Your score is {$score}%.");
    }

    public function reviewExam(ExamAttempt $examAttempt): View
    {
        abort_unless($examAttempt->user_id === auth()->id(), 403);

        return view('student.exam-review', [
            'attempt' => $examAttempt,
        ]);
    }

    public function examHistory(Request $request): View
    {
        $attempts = $request->user()->examAttempts()
            ->latest()
            ->get();

        return view('student.exam-history', [
            'attempts' => $attempts,
        ]);
    }
}
