<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        User::create([
            ...$validated,
            'role' => 'admin',
            'payment_status' => User::PAYMENT_APPROVED,
            'payment_approved_at' => now(),
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Admin account created successfully.');
    }

    public function payments(): View
    {
        return view('admin.users.payments', [
            'students' => User::query()
                ->where('role', 'student')
                ->orderByRaw("case payment_status when 'pending' then 0 when 'rejected' then 1 else 2 end")
                ->latest()
                ->get(),
        ]);
    }

    public function approvePayment(User $user): RedirectResponse
    {
        abort_unless($user->isStudent(), 404);

        if (! $user->gcash_receipt_path) {
            return redirect()
                ->route('admin.payments.index')
                ->with('status', "Cannot approve {$user->email} because no GCash receipt was uploaded.");
        }

        $user->update([
            'payment_status' => User::PAYMENT_APPROVED,
            'payment_approved_at' => now(),
        ]);

        return redirect()
            ->route('admin.payments.index')
            ->with('status', "Payment approved for {$user->email}.");
    }

    public function rejectPayment(User $user): RedirectResponse
    {
        abort_unless($user->isStudent(), 404);

        $user->update([
            'payment_status' => User::PAYMENT_REJECTED,
            'payment_approved_at' => null,
        ]);

        return redirect()
            ->route('admin.payments.index')
            ->with('status', "Payment rejected for {$user->email}.");
    }

    public function receipt(User $user): Response
    {
        abort_unless($user->isStudent() && $user->gcash_receipt_path, 404);

        return Storage::disk('local')->download(
            $user->gcash_receipt_path,
            basename($user->gcash_receipt_path),
        );
    }

    public function index(Request $request): View
    {
        $search = $request->query('search');

        $users = User::query()
            ->when($search, fn ($query) => $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User deleted successfully.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $tempCode = strtoupper(bin2hex(random_bytes(4)));

        $user->update([
            'password' => Hash::make($tempCode),
            'password_reset_code' => $tempCode,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Password reset for {$user->email}. Temporary code: {$tempCode}");
    }
}
