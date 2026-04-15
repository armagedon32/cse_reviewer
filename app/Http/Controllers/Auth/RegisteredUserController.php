<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'gcash_reference' => ['required', 'string', 'max:255'],
            'gcash_receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $receiptPath = $request->file('gcash_receipt')->store('gcash-receipts');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'student',
            'payment_status' => User::PAYMENT_PENDING,
            'gcash_reference' => $validated['gcash_reference'],
            'gcash_receipt_path' => $receiptPath,
            'payment_submitted_at' => now(),
        ]);

        return redirect()
            ->route('login')
            ->with('status', "Student account created for {$user->email}. GCash payment submitted and awaiting admin approval before login.");
    }
}
