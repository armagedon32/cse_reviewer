@extends('layouts.app')

@section('title', 'Admin Users')

@section('content')
    <div class="topbar">
        <div>
            <div class="brand">Admin Users</div>
            <p class="muted">List of registered administrators.</p>
        </div>
        <span class="pill">{{ $admins->count() }} Admin(s)</span>
    </div>

    <div class="stack-lg">
        @forelse ($admins as $admin)
            <div class="question-card">
                <div class="section-actions">
                    <div>
                        <strong>{{ $admin->name }}</strong>
                        <div class="muted">{{ $admin->email }}</div>
                    </div>
                    <div class="actions">
                        <span class="pill">Admin</span>
                    </div>
                </div>

                <div class="stack">
                    <div class="muted">Created: {{ $admin->created_at->format('Y-m-d H:i') }}</div>
                    <div class="muted">Last updated: {{ $admin->updated_at->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        @empty
            <div class="panel">
                <strong>No Admin Users</strong>
                <p class="muted" style="margin-bottom: 0;">There are no admin users registered.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 20px;">
        <a class="btn secondary" href="{{ route('admin.users.index') }}">Back to all users</a>
    </div>
@endsection
