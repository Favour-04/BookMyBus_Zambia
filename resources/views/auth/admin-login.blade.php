@extends('layouts.auth')

@section('title', 'Admin Sign In')

@push('head')
<style>
.auth-gradient { background: linear-gradient(135deg, #001a0a 0%, #004614 50%, #00601f 100%); }
        .auth-card { backdrop-filter: blur(20px); background: rgba(255, 255, 255, 0.95); }
        .input-field { transition: all 0.2s ease; background: #f3f3f6; border: 2px solid transparent; }
        .input-field:focus { background: #ffffff; border-color: #00601f; box-shadow: 0 0 0 4px rgba(0, 96, 31, 0.1); }
        .input-field.error { border-color: #ba1a1a; background: #fff5f5; }
        .btn-primary { background: linear-gradient(135deg, #00601f 0%, #197b30 100%); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0, 96, 31, 0.3); }
</style>
@endpush

@section('content')

    <div class="min-h-screen flex items-center justify-center p-4 auth-gradient relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        </div>
        <div class="w-full max-w-[420px] relative z-10">
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-sm px-6 py-3 rounded-2xl border border-white/20 mb-6">
                    <span class="material-symbols-outlined text-white text-3xl">admin_panel_settings</span>
                    <span class="text-white font-headline font-extrabold text-xl tracking-tight">Admin Portal</span>
                </div>
                <h1 class="text-white text-2xl font-headline font-extrabold tracking-tight">Welcome Back</h1>
                <p class="text-white/70 text-sm mt-1">Sign in to manage the platform</p>
            </div>
            <div class="auth-card rounded-3xl shadow-2xl p-6 md:p-8">
                <form action="{{ route('admin.login') }}" method="POST" class="space-y-5">
                    @csrf
                    @if($errors->any())
                        <div class="bg-error-container/20 border border-error/20 rounded-xl p-4 text-sm text-error">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    @if(session('status'))
                        <div class="bg-primary/10 border border-primary/20 rounded-xl p-4 text-sm text-primary">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="email">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="input-field w-full rounded-xl px-4 py-3 text-sm text-on-surface focus:outline-none"
                            placeholder="admin@bookmybus.zm" required autofocus />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="password">Password</label>
                        <input type="password" name="password" id="password"
                            class="input-field w-full rounded-xl px-4 py-3 text-sm text-on-surface focus:outline-none"
                            placeholder="••••••••" required />
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-on-surface-variant cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-outline-variant text-primary focus:ring-primary" />
                            Remember me
                        </label>
                    </div>
                    <button type="submit" class="btn-primary w-full py-3 rounded-xl text-white font-bold text-sm flex items-center justify-center gap-2">
                        <span>Sign In</span>
                        <span class="material-symbols-outlined text-lg">login</span>
                    </button>
                </form>
                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-on-surface-variant hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        Back to BookMyBus Zambia
                    </a>
                </div>
            </div>
            <div class="text-center mt-6">
                <p class="text-white/50 text-xs">© {{ date('Y') }} BookMyBus Zambia. Admin Panel.</p>
            </div>
        </div>
    </div>
@endsection
