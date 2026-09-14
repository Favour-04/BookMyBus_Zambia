@extends('layouts.auth')

@section('title', 'Forgot Password')

@push('head')
<style>
.hero-gradient {
            background: linear-gradient(135deg, #00601f 0%, #197b30 100%);
        }
        .panel-gradient {
            background: linear-gradient(160deg, #00601f 0%, #197b30 60%, #1a3d22 100%);
        }
</style>
@endpush

@section('content')


    <!-- Left decorative panel -->
    <div class="hidden lg:flex lg:w-1/2 panel-gradient flex-col justify-between p-12 relative overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>
        <!-- Decorative bus icon blob -->
        <div class="absolute -bottom-16 -right-16 w-72 h-72 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-8 -right-8 w-48 h-48 rounded-full bg-white/5"></div>

        <!-- Logo -->
        <div class="relative z-10">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="material-symbols-outlined text-white text-3xl">directions_bus</span>
                <span class="font-headline font-extrabold text-white text-xl tracking-tighter">BookMyBus Zambia</span>
            </a>
        </div>

        <!-- Center content -->
        <div class="relative z-10 flex-1 flex flex-col justify-center py-12">
            <span class="text-secondary-container font-bold tracking-widest text-xs uppercase mb-4">Account Recovery</span>
            <h1 class="font-headline text-5xl font-extrabold text-white tracking-tighter leading-tight mb-6">
                Don't worry,<br/>we've got you.
            </h1>
            <p class="text-white/70 font-body text-base max-w-sm leading-relaxed">
                Enter your email and we'll send you a secure link to reset your password and get you back on the road.
            </p>

            <!-- Feature pills -->
            <div class="flex flex-col gap-3 mt-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-sm">lock_reset</span>
                    </div>
                    <span class="text-white/80 text-sm font-body">Secure password reset link</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-sm">schedule</span>
                    </div>
                    <span class="text-white/80 text-sm font-body">Link expires in 60 minutes</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-sm">support_agent</span>
                    </div>
                    <span class="text-white/80 text-sm font-body">24/7 support if you need help</span>
                </div>
            </div>
        </div>

        <!-- Bottom links -->
        <div class="relative z-10">
            <p class="text-white/40 text-xs font-body">
                Remembered your password?
                <a href="{{ route('login') }}" class="text-secondary-container font-semibold hover:underline ml-1">Back to Sign In →</a>
            </p>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-20">

        <!-- Mobile logo -->
        <div class="lg:hidden mb-10">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-2xl">directions_bus</span>
                <span class="font-headline font-extrabold text-primary text-lg tracking-tighter">BookMyBus Zambia</span>
            </a>
        </div>

        <div class="max-w-sm w-full mx-auto lg:mx-0">

            <div class="mb-8">
                <div class="w-12 h-12 rounded-xl bg-primary-fixed/40 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-2xl">lock_reset</span>
                </div>
                <h2 class="font-headline text-3xl font-extrabold text-on-surface tracking-tight">Reset Password</h2>
                <p class="text-on-surface-variant font-body mt-2 text-sm">Enter the email address associated with your account.</p>
            </div>

            <!-- Status message -->
            @if(session('status'))
                <div class="mb-6 p-4 rounded-lg bg-primary-fixed/30 border border-primary-container/30 flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-sm mt-0.5">check_circle</span>
                    <p class="text-on-surface text-sm font-body">{{ session('status') }}</p>
                </div>
            @endif

            <!-- Validation errors -->
            @if($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-error-container border border-error/20 flex items-start gap-3">
                    <span class="material-symbols-outlined text-error text-sm mt-0.5">error</span>
                    <div>
                        @foreach($errors->all() as $error)
                            <p class="text-error text-sm font-body">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="flex flex-col gap-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Email Address</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">mail</span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            placeholder="you@example.com"
                            class="w-full pl-10 pr-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface font-body text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors placeholder:text-outline @error('email') border-error focus:border-error focus:ring-error @enderror"
                        />
                    </div>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full hero-gradient text-white font-headline font-extrabold py-3.5 rounded-lg hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 mt-1"
                >
                    <span>Send Reset Link</span>
                    <span class="material-symbols-outlined text-lg">send</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="mt-8 pt-6 border-t border-outline-variant lg:hidden">
                <p class="text-center text-xs text-outline font-body">
                    Remembered your password?
                    <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline ml-1">Sign in</a>
                </p>
            </div>
        </div>
    </div>
@endsection
