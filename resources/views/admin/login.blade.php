@extends('layouts.admin')

@section('title', 'Log in')

@section('content')
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
            <h1 class="text-2xl font-bold text-slate-900 text-center">Welcome back</h1>
            <p class="mt-1 text-sm text-slate-500 text-center">Log in to manage your bookings or the bus schedule.</p>

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('password') border-red-400 @enderror">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-600">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="w-full inline-flex justify-center px-4 py-2.5 rounded-lg bg-emerald-700 text-white text-sm font-semibold hover:bg-emerald-800">
                    Log in
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-medium text-emerald-700 hover:text-emerald-800">Register here</a>
            </p>
        </div>
    </div>
@endsection
