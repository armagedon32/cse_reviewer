@extends('layouts.app')

@section('title', 'Registered Users')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Registered Users</div>
            <p class="muted">Manage all registered users in the system.</p>
        </div>
        <span class="pill">{{ $users->count() }} User(s)</span>
    </div>

    <div class="stack-lg">
        <form method="GET" action="{{ route('admin.users.index') }}" class="panel">
            <div class="section-actions">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by name or email..." 
                    value="{{ $search ?? '' }}"
                    style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 4px; width: 300px;"
                >
                <button class="btn" type="submit">Search</button>
                @if($search)
                    <a class="btn secondary" href="{{ route('admin.users.index') }}">Clear</a>
                @endif
            </div>
        </form>
        @forelse ($users as $user)
            <div class="question-card">
                <div class="section-actions">
                    <div>
                        <strong>{{ $user->name }}</strong>
                        <div class="muted">{{ $user->email }}</div>
                    </div>
                    <div class="actions">
                        <span class="pill">Role: {{ ucfirst($user->role) }}</span>
                    </div>
                </div>

                <div class="stack">
                    <div class="muted">Payment Status: {{ ucfirst($user->effectivePaymentStatus()) }}</div>
                    <div class="muted">Registered: {{ $user->created_at->format('Y-m-d H:i') }}</div>
                    <div class="muted">Last Login: {{ $user->created_at->format('Y-m-d H:i') }}</div>
                </div>

                <div class="section-actions">
                    <div class="actions">
                        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                            @csrf
                            <button class="btn" type="submit" onclick="return confirm('Reset password for this user?')">Reset Password</button>
                        </form>
                        @if ($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn secondary" type="submit" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                            </form>
                        @else
                            <button class="btn secondary" type="button" disabled style="opacity: 0.5; cursor: not-allowed;">Delete</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="panel">
                <strong>No Users</strong>
                <p class="muted" style="margin-bottom: 0;">There are no registered users yet.</p>
            </div>
        @endforelse
    </div>
@endsection