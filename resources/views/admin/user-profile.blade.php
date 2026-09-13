@extends('layouts.admin')

@section('title', 'My Profile')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">My Profile</h1>
        <p class="mt-1 text-sm text-slate-500">Update your account details and review your recent bookings.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile form --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-4">Account details</h2>

            <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1">Full name</label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $user->full_name) }}" required
                           class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('full_name') border-red-400 @enderror">
                    @error('full_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-slate-700 mb-1">Phone number</label>
                        <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required
                               class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('phone_number') border-red-400 @enderror">
                        @error('phone_number')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="preferred_language" class="block text-sm font-medium text-slate-700 mb-1">Preferred language</label>
                    <select name="preferred_language" id="preferred_language"
                            class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
                        <option value="en" @selected(old('preferred_language', $user->preferred_language) === 'en')>English</option>
                        <option value="ny" @selected(old('preferred_language', $user->preferred_language) === 'ny')>Nyanja</option>
                        <option value="bem" @selected(old('preferred_language', $user->preferred_language) === 'bem')>Bemba</option>
                        <option value="loz" @selected(old('preferred_language', $user->preferred_language) === 'loz')>Lozi</option>
                    </select>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <p class="text-sm font-medium text-slate-700 mb-3">Change password <span class="text-slate-400 font-normal">(optional)</span></p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">New password</label>
                            <input type="password" name="password" id="password"
                                   class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('password') border-red-400 @enderror">
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm new password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-lg bg-emerald-700 text-white text-sm font-semibold hover:bg-emerald-800">
                        Save changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Recent bookings --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 h-fit">
            <h2 class="font-semibold text-slate-900 mb-4">Recent bookings</h2>

            @forelse ($bookings as $booking)
                <div class="py-3 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $booking->route->origin ?? '-' }} &rarr; {{ $booking->route->destination ?? '-' }}</p>
                            <p class="text-xs text-slate-400 font-mono">{{ $booking->reference_id }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ match($booking->status) {
                                'confirmed' => 'bg-emerald-50 text-emerald-700',
                                'pending' => 'bg-amber-50 text-amber-700',
                                'cancelled', 'expired' => 'bg-red-50 text-red-700',
                                default => 'bg-slate-100 text-slate-600',
                            } }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">You have no bookings yet.</p>
            @endforelse

            <a href="{{ route('admin.search.results') }}" class="mt-4 inline-block text-sm font-medium text-emerald-700 hover:text-emerald-800">
                Book a new trip &rarr;
            </a>
        </div>
    </div>
@endsection
