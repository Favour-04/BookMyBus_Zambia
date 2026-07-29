@extends('layouts.landing')

@section('title', 'Customer Support - BookMyBus Zambia')

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
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-headline font-extrabold text-on-surface mb-4">Customer Support</h1>
            <p class="text-zinc-500">We're here to help you with any questions or concerns</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-8">
                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-2xl">call</span>
                </div>
                <h3 class="font-headline font-bold text-xl mb-2">Call Us</h3>
                <p class="text-zinc-500 text-sm mb-4">Mon-Fri 8am-6pm, Sat 9am-4pm</p>
                <p class="font-bold text-lg text-primary">+260 211 123 456</p>
            </div>
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-8">
                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-2xl">email</span>
                </div>
                <h3 class="font-headline font-bold text-xl mb-2">Email Support</h3>
                <p class="text-zinc-500 text-sm mb-4">We'll respond within 24 hours</p>
                <p class="font-bold text-lg text-primary">support@bookmybus.zm</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-8">
            <h2 class="font-headline font-bold text-2xl mb-6">Send us a message</h2>
            <form method="POST" action="{{ route('support.submit') }}" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-on-surface-variant mb-2">Your Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-3 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="John Doe">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface-variant mb-2">Email Address</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="you@example.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface-variant mb-2">Subject</label>
                    <select name="subject" class="w-full px-4 py-3 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                        <option value="booking">Booking Issue</option>
                        <option value="payment">Payment Problem</option>
                        <option value="refund">Refund Request</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface-variant mb-2">Message</label>
                    <textarea name="message" rows="5" required class="w-full px-4 py-3 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="How can we help?"></textarea>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:opacity-90 transition-all">
                    Send Message
                </button>
            </form>
        </div>
    </div>
</main>
@endsection
