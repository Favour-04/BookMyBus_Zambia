@extends('layouts.app')

@section('title', 'Carrier Partners')

@push('head')
<style>
.hero-gradient {
            background: linear-gradient(135deg, #00601f 0%, #197b30 100%);
        }
</style>
@endpush

@section('content')


    <!-- Top Navigation -->
    

    <!-- Main Content -->
    
        <div class="mb-10 text-center">
            <span class="text-secondary font-bold tracking-widest text-xs uppercase">Our Network</span>
            <h1 class="font-headline text-4xl md:text-5xl font-extrabold tracking-tighter mt-2">Carrier Partners</h1>
            <p class="text-on-surface-variant mt-3 max-w-2xl mx-auto">
                We partner with Zambia's most trusted and verified bus operators to bring you safe, comfortable, and reliable travel across the nation.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Partner 1 -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-primary-container text-2xl">directions_bus</span>
                    </div>
                    <div>
                        <h3 class="font-headline text-xl font-bold">Euro-Trans</h3>
                        <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-1 rounded-full">Verified Partner</span>
                    </div>
                </div>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    Premium inter-city coach services connecting Lusaka, Kitwe, Ndola, and Livingstone with modern fleet and professional drivers.
                </p>
                <div class="mt-4 flex items-center gap-2 text-xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">star</span>
                    <span class="font-bold">4.8</span> · 2,300+ trips completed
                </div>
            </div>

            <!-- Partner 2 -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-primary-container text-2xl">commute</span>
                    </div>
                    <div>
                        <h3 class="font-headline text-xl font-bold">Power Tools Bus</h3>
                        <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-1 rounded-full">Verified Partner</span>
                    </div>
                </div>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    Reliable daily departures on the Copperbelt corridor with comfortable seating and on-time performance.
                </p>
                <div class="mt-4 flex items-center gap-2 text-xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">star</span>
                    <span class="font-bold">4.6</span> · 1,800+ trips completed
                </div>
            </div>

            <!-- Partner 3 -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-primary-container text-2xl">airport_shuttle</span>
                    </div>
                    <div>
                        <h3 class="font-headline text-xl font-bold">Mazhandu Family Bus</h3>
                        <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-1 rounded-full">Verified Partner</span>
                    </div>
                </div>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    Zambia's largest bus operator serving all major routes nationwide with a focus on safety and comfort.
                </p>
                <div class="mt-4 flex items-center gap-2 text-xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">star</span>
                    <span class="font-bold">4.7</span> · 5,100+ trips completed
                </div>
            </div>

            <!-- Partner 4 -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-primary-container text-2xl">electric_bolt</span>
                    </div>
                    <div>
                        <h3 class="font-headline text-xl font-bold">FM Traveller</h3>
                        <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-1 rounded-full">Verified Partner</span>
                    </div>
                </div>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    Express services between Lusaka and the Copperbelt with modern amenities and competitive fares.
                </p>
                <div class="mt-4 flex items-center gap-2 text-xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">star</span>
                    <span class="font-bold">4.5</span> · 1,200+ trips completed
                </div>
            </div>
        </div>

        <!-- Become a Partner CTA -->
        <div class="mt-12 bg-primary rounded-2xl p-10 text-center text-white">
            <h2 class="font-headline text-3xl font-extrabold mb-3">Are you a bus operator?</h2>
            <p class="text-primary-fixed max-w-xl mx-auto mb-6">
                Join our platform and reach thousands of passengers across Zambia. Manage your fleet, trips, and bookings from one dashboard.
            </p>
            <a href="{{ route('operator.login') }}"
                class="inline-block bg-white text-primary font-bold px-8 py-3 rounded-xl hover:bg-primary-fixed transition-colors">
                Get Started
            </a>
        </div>
    

    <!-- Footer -->
@endsection
