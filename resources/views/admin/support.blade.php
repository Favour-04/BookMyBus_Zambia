@extends('layouts.admin')

@section('title', 'Help &amp; Support')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Help Desk &amp; Support</h1>
        <p class="mt-1 text-sm text-slate-500">Get answers to common questions or reach our support team directly.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- FAQ --}}
        <div class="lg:col-span-2 space-y-3">
            <h2 class="font-semibold text-slate-900 mb-2">Frequently asked questions</h2>

            <details class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 group">
                <summary class="cursor-pointer font-medium text-slate-800 list-none flex justify-between items-center">
                    How long is my seat held before payment?
                    <span class="text-slate-400 group-open:rotate-45 transition">+</span>
                </summary>
                <p class="mt-2 text-sm text-slate-600">Once you select a seat, it is held for 10 minutes while you complete Mobile Money or card payment. If the hold expires, the seat is released back to other travelers.</p>
            </details>

            <details class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 group">
                <summary class="cursor-pointer font-medium text-slate-800 list-none flex justify-between items-center">
                    Which payment methods are supported?
                    <span class="text-slate-400 group-open:rotate-45 transition">+</span>
                </summary>
                <p class="mt-2 text-sm text-slate-600">We accept MTN Mobile Money, Airtel Money, Zamtel Kwacha, and debit/credit cards. Mobile money payments are confirmed instantly via a phone prompt.</p>
            </details>

            <details class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 group">
                <summary class="cursor-pointer font-medium text-slate-800 list-none flex justify-between items-center">
                    Can I cancel or change a confirmed booking?
                    <span class="text-slate-400 group-open:rotate-45 transition">+</span>
                </summary>
                <p class="mt-2 text-sm text-slate-600">Pending bookings can be cancelled before payment. Confirmed bookings cannot be self-cancelled &mdash; please contact support with your booking reference and we'll assist you.</p>
            </details>

            <details class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 group">
                <summary class="cursor-pointer font-medium text-slate-800 list-none flex justify-between items-center">
                    I paid but did not receive my ticket. What should I do?
                    <span class="text-slate-400 group-open:rotate-45 transition">+</span>
                </summary>
                <p class="mt-2 text-sm text-slate-600">Tickets are issued automatically once payment is confirmed. If a few minutes have passed and you still don't see it, submit a support request below with your booking reference and transaction details.</p>
            </details>
        </div>

        {{-- Contact form + info --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h2 class="font-semibold text-slate-900 mb-4">Contact us</h2>

                <form method="POST" action="{{ route('admin.support.submit') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                        <select name="category" id="category" required
                                class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('category') border-red-400 @enderror">
                            <option value="">Select a category</option>
                            <option value="booking" @selected(old('category') === 'booking')>Booking issue</option>
                            <option value="payment" @selected(old('category') === 'payment')>Payment issue</option>
                            <option value="refund" @selected(old('category') === 'refund')>Refund request</option>
                            <option value="account" @selected(old('category') === 'account')>Account issue</option>
                            <option value="other" @selected(old('category') === 'other')>Other</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-slate-700 mb-1">Subject</label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                               class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('subject') border-red-400 @enderror">
                        @error('subject')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="reference_id" class="block text-sm font-medium text-slate-700 mb-1">Booking reference <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input type="text" name="reference_id" id="reference_id" value="{{ old('reference_id') }}" placeholder="e.g. BMZ-A3F9K2"
                               class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-slate-700 mb-1">Message</label>
                        <textarea name="message" id="message" rows="4" required
                                  class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full inline-flex justify-center px-4 py-2.5 rounded-lg bg-emerald-700 text-white text-sm font-semibold hover:bg-emerald-800">
                        Submit request
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 text-sm text-slate-600 space-y-2">
                <h3 class="font-semibold text-slate-900 mb-2">Other ways to reach us</h3>
                <p>&#128222; +260 97 000 0000</p>
                <p>&#9993; support@bookmybus.co.zm</p>
                <p>&#128337; Mon &ndash; Sat, 07:00 &ndash; 19:00 (CAT)</p>
            </div>
        </div>
    </div>
@endsection
