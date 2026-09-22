@extends('layouts.admin')

@section('title', 'Add Account')
@section('page_title', 'Add Account')

@section('content')
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-primary mb-6">
        <span class="material-symbols-outlined text-base">arrow_back</span>Back to Travelers
    </a>

    <div class="max-w-2xl bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
        <h3 class="font-headline font-bold text-lg mb-4">Create a New Account</h3>
        <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="full_name">Full Name *</label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="email">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="phone_number">Phone Number *</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="password">Password *</label>
                <input type="password" name="password" id="password" minlength="8"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="password_confirmation">Confirm Password *</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="preferred_language">Preferred Language</label>
                <select name="preferred_language" id="preferred_language" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
                    @foreach(['en' => 'English', 'ny' => 'Nyanja / Chewa', 'bem' => 'Bemba', 'to' => 'Tonga', 'loz' => 'Lozi'] as $code => $name)
                        <option value="{{ $code }}" {{ old('preferred_language', 'en') === $code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex gap-2 pt-2">
                <button type="submit" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-container">
                    <span class="material-symbols-outlined text-base">person_add</span>Create Account</button>
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2.5 rounded-xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant">Cancel</a>
            </div>
        </form>
    </div>
@endsection
