@extends('layouts.admin')

@section('title', 'Create an account')

@section('content')
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
            <h1 class="text-2xl font-bold text-slate-900 text-center">Create your account</h1>
            <p class="mt-1 text-sm text-slate-500 text-center">Book seats and manage your trips across Zambia.</p>

            <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1">Full name</label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required autofocus
                           class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('full_name') border-red-400 @enderror">
                    @error('full_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone_number" class="block text-sm font-medium text-slate-700 mb-1">Phone number</label>
                    <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required
                           placeholder="e.g. 097XXXXXXX"
                           class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('phone_number') border-red-400 @enderror">
                    @error('phone_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="preferred_language" class="block text-sm font-medium text-slate-700 mb-1">Preferred language</label>
                    <select name="preferred_language" id="preferred_language"
                            class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
                        <option value="en" @selected(old('preferred_language', 'en') === 'en')>English</option>
                        <option value="ny" @selected(old('preferred_language') === 'ny')>Nyanja</option>
                        <option value="bem" @selected(old('preferred_language') === 'bem')>Bemba</option>
                        <option value="loz" @selected(old('preferred_language') === 'loz')>Lozi</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('password') border-red-400 @enderror">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
                </div>

                <button type="submit" class="w-full inline-flex justify-center px-4 py-2.5 rounded-lg bg-emerald-700 text-white text-sm font-semibold hover:bg-emerald-800">
                    Create account
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-emerald-700 hover:text-emerald-800">Log in</a>
            </p>
        </div>
    </div>
@endsection
