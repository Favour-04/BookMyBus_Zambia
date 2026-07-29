@extends('layouts.landing')

@section('title', 'Login - BookMyBus Zambia')

@push('styles')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, .font-headline { font-family: 'Manrope', sans-serif; }
</style>
@endpush

@section('content')
<main class="pt-24 pb-12 px-6">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-headline font-extrabold text-on-surface mb-2">Welcome Back</h1>
            <p class="text-zinc-500">Sign in to your BookMyBus account</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-on-surface-variant mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface-variant mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-zinc-300">
                        <span class="text-sm text-on-surface-variant">Remember me</span>
                    </label>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:opacity-90 transition-all">
                    Sign In
                </button>
            </form>

            {{-- Registration disabled - controller not yet implemented --}}
            {{--
            <div class="mt-6 text-center">
                <p class="text-sm text-zinc-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-bold text-primary hover:underline">Sign up</a>
                </p>
            </div>
            --}}
        </div>
    </div>
</main>
@endsection
