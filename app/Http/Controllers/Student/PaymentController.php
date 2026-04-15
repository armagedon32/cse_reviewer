<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->isStudent()) {
            abort(403);
        }

        if (! $user->requiresPaymentRenewal()) {
            return redirect()->route('student.dashboard');
        }

        return view('student.payment-renewal', [
            'user' => $user,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isStudent()) {
            abort(403);
        }

        $validated = $request->validate([
            'gcash_reference' => ['required', 'string', 'max:255'],
            'gcash_receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        if ($user->gcash_receipt_path) {
            Storage::disk('local')->delete($user->gcash_receipt_path);
        }

        $receiptPath = $request->file('gcash_receipt')->store('gcash-receipts');

        $user->update([
            'payment_status' => User::PAYMENT_PENDING,
            'gcash_reference' => $validated['gcash_reference'],
            'gcash_receipt_path' => $receiptPath,
            'payment_submitted_at' => now(),
            'payment_approved_at' => null,
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Your renewal payment was submitted. Wait for admin approval before accessing your student account again.');
    }
}
