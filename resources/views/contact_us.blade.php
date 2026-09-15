@extends('layouts.app')

@section('title', 'Contact Us — BookMyBus Zambia')
@section('main-class', 'pb-20 max-w-5xl mx-auto px-6')

@push('styles')
<style>
    /* Theme-aware shadow token — deeper in dark mode so it's visible on dark surfaces */
    :root {
        --color-shadow: rgba(26, 28, 30, 0.05);
    }
    .dark {
        --color-shadow: rgba(0, 0, 0, 0.45);
    }

    .hero-gradient {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-container) 100%);
    }

    .card-hover {
        transition: box-shadow 0.2s ease;
    }
    .card-hover:hover {
        box-shadow: 0 10px 15px -3px var(--color-shadow), 0 4px 6px -4px var(--color-shadow);
    }

        /* SVG icon color — theme-aware. SVGs using stroke="currentColor" or
           fill="currentColor" inherit this automatically. */
        .icon {
            color: var(--color-on-surface);
            transition: color 0.2s ease;
        }
        .icon-primary { color: var(--color-primary); }
        .icon-secondary { color: var(--color-secondary); }
        .icon-muted { color: var(--color-on-surface-variant); }
        .icon-error { color: var(--color-error); }
        .icon-on-primary { color: var(--color-on-primary); }
</style>
@endpush

@section('content')
<div class="mb-10 text-center">
    <span class="text-secondary font-bold tracking-widest text-xs uppercase">Get in Touch</span>
    <h1 class="font-headline text-4xl md:text-5xl font-extrabold tracking-tighter mt-2">Contact Us</h1>
    <p class="text-on-surface-variant mt-3 max-w-2xl mx-auto">
        We're here to help. Reach out to our team for any questions, feedback, or support.
    </p>
</div>

<!-- Contact Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 text-center card-hover">
        <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-on-primary-container">call</span>
        </div>
        <h3 class="font-headline text-lg font-bold mb-2">Call Us</h3>
        <p class="text-on-surface-variant text-sm mb-3">Available 24/7 for emergencies</p>
        <p class="font-bold">+260 97 1234567</p>
        <p class="font-bold">+260 96 7654321</p>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 text-center card-hover">
        <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-on-primary-container">mail</span>
        </div>
        <h3 class="font-headline text-lg font-bold mb-2">Email Us</h3>
        <p class="text-on-surface-variant text-sm mb-3">Response within 24 hours</p>
        <p class="font-bold">support@bookmybus.co.zm</p>
        <p class="font-bold">info@bookmybus.co.zm</p>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 text-center card-hover">
        <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-on-primary-container">location_on</span>
        </div>
        <h3 class="font-headline text-lg font-bold mb-2">Visit Us</h3>
        <p class="text-on-surface-variant text-sm mb-3">Head Office</p>
        <p class="font-bold">Plot 123, Cairo Road</p>
        <p class="font-bold">Lusaka, Zambia</p>
    </div>
</div>

<!-- Contact Form -->
<div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
    <h2 class="font-headline text-2xl font-bold mb-6">Send us a message</h2>
    <form class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Your Name</label>
                <input type="text" placeholder="Enter your full name"
                    class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Email Address</label>
                <input type="email" placeholder="Enter your email"
                    class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
            </div>
        </div>
        <div>
            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Subject</label>
            <select class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
                <option>Booking Issue</option>
                <option>Payment Problem</option>
                <option>Cancellation Request</option>
                <option>General Inquiry</option>
                <option>Feedback</option>
                <option>Partnership</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Message</label>
            <textarea rows="5" placeholder="Describe your issue or question..."
                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"></textarea>
        </div>
        <button type="submit"
            class="hero-gradient text-on-primary font-headline font-bold text-sm px-8 py-3.5 rounded-xl hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 w-full md:w-auto">
            <span class="material-symbols-outlined text-lg">send</span>
            Send Message
        </button>
    </form>
</div>
@endsection