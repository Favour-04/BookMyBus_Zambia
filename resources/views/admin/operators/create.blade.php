@extends('layouts.admin')

@section('title', 'Add Operator')
@section('page_title', 'Add Operator')

@section('content')
    <a href="{{ route('admin.operators.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-primary mb-6">
        <span class="material-symbols-outlined text-base">arrow_back</span>Back to Operators
    </a>

    <div class="max-w-3xl bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
        <h3 class="font-headline font-bold text-lg mb-4">Onboard a New Bus Operator</h3>
        <form method="POST" action="{{ route('admin.operators.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="company_name">Company Name *</label>
                <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="email">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="phone_number">Phone Number *</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="password">Password *</label>
                <input type="password" name="password" id="password" minlength="8"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="password_confirmation">Confirm Password *</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary" required>
            </div>
<div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="tpin">TPIN</label>
                <input type="text" name="tpin" id="tpin" value="{{ old('tpin') }}" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="business_type">Business Type</label>
                <input type="text" name="business_type" id="business_type" value="{{ old('business_type') }}" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="business_registration_number">Registration Number</label>
                <input type="text" name="business_registration_number" id="business_registration_number" value="{{ old('business_registration_number') }}" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="business_registration_date">Registration Date</label>
                <input type="date" name="business_registration_date" id="business_registration_date" value="{{ old('business_registration_date') }}" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="contact_person_name">Contact Person</label>
                <input type="text" name="contact_person_name" id="contact_person_name" value="{{ old('contact_person_name') }}" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="contact_person_title">Contact Person Title</label>
                <input type="text" name="contact_person_title" id="contact_person_title" value="{{ old('contact_person_title') }}" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="address">Address</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
            </div>
<div class="md:col-span-2">
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="description">Description</label>
                <textarea name="description" id="description" rows="3" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2.5 text-sm focus:outline-none focus:border-primary">{{ old('description') }}</textarea>
            </div>

            <label class="md:col-span-2 flex items-center gap-2 text-sm text-on-surface-variant cursor-pointer">
                <input type="checkbox" name="mark_verified" value="1" class="rounded border-outline-variant text-primary focus:ring-primary">
                Mark as verified immediately (skip pending verification)
            </label>

            <div class="md:col-span-2 flex gap-2 pt-2">
                <button type="submit" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-container">
                    <span class="material-symbols-outlined text-base">person_add</span>Create Operator</button>
                <a href="{{ route('admin.operators.index') }}" class="flex items-center px-4 py-2.5 rounded-xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant">Cancel</a>
            </div>
        </form>
    </div>
@endsection