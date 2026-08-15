@extends('layouts.admin')

@section('title', 'Settings')
@section('page_title', 'Admin Settings')

@section('content')
    <!-- Account header -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-2xl bg-primary/20 flex items-center justify-center text-primary font-headline font-extrabold text-2xl">{{ strtoupper(substr($admin->full_name ?? 'A', 0, 1)) }}</div>
            <div>
                <h3 class="font-headline font-extrabold text-2xl">{{ $admin->full_name }}</h3>
                <p class="text-sm text-on-surface-variant">{{ $admin->email }} · {{ $admin->phone_number }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Account details form -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Account Details</h3>
            <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $admin->full_name) }}"
                        class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}"
                        class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="phone_number">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $admin->phone_number) }}"
                        class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="preferred_language">Preferred Language</label>
                    <select name="preferred_language" id="preferred_language" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
                        @php $lang = old('preferred_language', $admin->preferred_language ?? 'en'); @endphp
                        @foreach(['en' => 'English', 'ny' => 'Nyanja / Chewa', 'bem' => 'Bemba', 'to' => 'Tonga', 'loz' => 'Lozi'] as $code => $name)
                            <option value="{{ $code }}" {{ $lang === $code ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-container">
                    <span class="material-symbols-outlined text-base">save</span>Save Changes
                </button>
            </form>
        </div>
<!-- Password form -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Change Password</h3>
            <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="current_password">Current Password</label>
                    <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                        class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="password">New Password</label>
                    <input type="password" name="password" id="password" autocomplete="new-password" minlength="8"
                        class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
                    <p class="text-xs text-on-surface-variant mt-1">At least 8 characters.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="password_confirmation">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                        class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
                </div>
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-container">
                    <span class="material-symbols-outlined text-base">lock_reset</span>Update Password
                </button>
            </form>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-headline font-bold text-lg">Recent Activity</h3>
            <span class="text-xs text-on-surface-variant">Last {{ $activities->count() }} actions</span>
        </div>

        @forelse($activities as $activity)
            @php
                $icons = [
                    'login' => 'login', 'logout' => 'logout',
                    'profile.updated' => 'manage_accounts',
                    'profile.password_changed' => 'lock_reset',
                    'operator.verified' => 'verified', 'operator.suspended' => 'block',
                    'operator.created' => 'business', 'operator.deleted' => 'delete',
                    'operator.viewed' => 'visibility',
                    'user.suspended' => 'block', 'user.activated' => 'check_circle',
                    'user.created' => 'person_add', 'user.viewed' => 'visibility',
                    'user.deleted' => 'delete', 'booking.viewed' => 'receipt_long',
                ];
                $icon = $icons[$activity->event] ?? 'task_alt';
            @endphp
            <div class="flex items-start gap-3 py-3 border-b border-outline-variant/10 last:border-0">
                <div class="h-9 w-9 rounded-xl bg-primary/15 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary text-base">{{ $icon }}</span>
                </div>
                <div class="flex-grow min-w-0">
                    <p class="text-sm font-bold text-on-surface">{{ $activity->eventLabel() }}</p>
                    <p class="text-xs text-on-surface-variant">{{ $activity->description ?? $activity->event }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs text-on-surface-variant">{{ $activity->created_at->diffForHumans() }}</p>
                    @if($activity->ip_address)
                        <p class="text-[10px] text-on-surface-variant/70">{{ $activity->ip_address }}</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <span class="material-symbols-outlined text-4xl text-outline mb-2">history</span>
                <p class="text-sm text-on-surface-variant">No recent activity yet.</p>
            </div>
        @endforelse
    </div>
@endsection