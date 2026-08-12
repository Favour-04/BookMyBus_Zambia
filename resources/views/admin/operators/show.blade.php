@extends('layouts.admin')

@section('title', $operator->company_name)
@section('page_title', 'Operator Profile')

@section('content')
    <a href="{{ route('admin.operators.index', ['status' => request('status', 'all')]) }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-primary mb-6">
        <span class="material-symbols-outlined text-base">arrow_back</span>Back to Operators
    </a>

    <!-- Header card -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($operator->logo_path)
                    <img src="{{ asset('storage/' . $operator->logo_path) }}" class="h-16 w-16 rounded-2xl object-cover bg-surface-container-high" alt="">
                @else
                    <div class="h-16 w-16 rounded-2xl bg-primary/20 flex items-center justify-center text-primary font-headline font-extrabold text-2xl">{{ strtoupper(substr($operator->company_name ?? 'O', 0, 1)) }}</div>
                @endif
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="font-headline font-extrabold text-2xl">{{ $operator->company_name }}</h3>
                        @if($operator->is_verified)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">Verified</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/10 text-tertiary">Pending</span>
                        @endif
                    </div>
                    <p class="text-sm text-on-surface-variant">{{ $operator->email }} · {{ $operator->phone_number }} · TPIN {{ $operator->tpin ?? '—' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if(!$operator->is_verified)
                    <form method="POST" action="{{ route('admin.operators.verify', $operator->id) }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-1 px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-container">
                            <span class="material-symbols-outlined text-base">verified_user</span>Verify & Approve
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.operators.suspend', $operator->id) }}" onsubmit="return confirm('Suspend this operator? It will no longer be authorised to sell tickets.');">
                        @csrf
                        <button type="submit" class="flex items-center gap-1 px-4 py-2 rounded-xl border border-error/30 text-error text-sm font-bold hover:bg-error-container/30">
                            <span class="material-symbols-outlined text-base">block</span>Suspend
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.operators.destroy', $operator->id) }}" onsubmit="return confirm('Remove {{ addslashes($operator->company_name) }}? Its buses, routes and bookings are soft-deleted and preserved.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center gap-1 px-4 py-2 rounded-xl border border-error/30 text-error text-sm font-bold hover:bg-error-container/30">
                        <span class="material-symbols-outlined text-base">delete</span>Remove
                    </button>
                </form>
            </div>
        </div>
        @if($operator->verified_at)
            <p class="text-xs text-on-surface-variant mt-4">
                Verified {{ $operator->verified_at->format('d M Y H:i') }}
                @if($operator->verifiedBy)
                    by {{ $operator->verifiedBy->full_name ?? $operator->verifiedBy->email }}
                @endif
            </p>
        @endif
    </div>
<!-- Stats cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Bookings</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($stats['total_bookings']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Confirmed</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-primary">{{ number_format($stats['confirmed']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Pending</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-tertiary">{{ number_format($stats['pending']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Cancelled</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-error">{{ number_format($stats['cancelled']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Revenue</p>
            <p class="font-headline font-extrabold text-2xl mt-2 leading-tight">ZMW {{ number_format($stats['revenue']) }}</p>
        </div>
    </div>
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        <!-- Company details -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Company Details</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Contact Person</dt><dd class="mt-1 font-semibold">{{ $operator->contact_person_name ?? '—' }} {{ $operator->contact_person_title ? '(' . $operator->contact_person_title . ')' : '' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Business Type</dt><dd class="mt-1 font-semibold">{{ $operator->business_type ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Registration No.</dt><dd class="mt-1 font-semibold">{{ $operator->business_registration_number ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Registered</dt><dd class="mt-1 font-semibold">{{ $operator->business_registration_date?->format('d M Y') ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Address</dt><dd class="mt-1 font-semibold">{{ $operator->address ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Website</dt><dd class="mt-1 font-semibold">{{ $operator->website ?? '—' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Description</dt><dd class="mt-1">{{ $operator->description ?? '—' }}</dd></div>
            </dl>
        </div>

        <!-- Compliance documents -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Compliance Documents</h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-outline-variant/15">
                    <span class="font-semibold">Business License</span>
                    @if($operator->business_license_path)
                        <span class="flex items-center gap-2">
                            <a href="{{ asset('storage/' . $operator->business_license_path) }}" target="_blank" class="text-primary font-bold underline">View</a>
                            @if($operator->business_license_verified_at)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">Verified</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/10 text-tertiary">Not verified</span>
                            @endif
                        </span>
                    @else
                        <span class="text-on-surface-variant">Not uploaded</span>
                    @endif
                </div>
                <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-outline-variant/15">
                    <span class="font-semibold">Tax ID</span>
                    @if($operator->tax_id_path)
                        <span class="flex items-center gap-2">
                            <a href="{{ asset('storage/' . $operator->tax_id_path) }}" target="_blank" class="text-primary font-bold underline">View</a>
                            @if($operator->tax_id_verified_at)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">Verified</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/10 text-tertiary">Not verified</span>
                            @endif
                        </span>
                    @else
                        <span class="text-on-surface-variant">Not uploaded</span>
                    @endif
                </div>
                <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-outline-variant/15">
                    <span class="font-semibold">Insurance Certificate</span>
                    @if($operator->insurance_certificate_path)
                        <a href="{{ asset('storage/' . $operator->insurance_certificate_path) }}" target="_blank" class="text-primary font-bold underline">View</a>
                    @else
                        <span class="text-on-surface-variant">Not uploaded</span>
                    @endif
                </div>
                @if($operator->insurance_expiry_date)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-outline-variant/15">
                        <span class="font-semibold">Insurance Expiry</span>
                        <span class="font-bold {{ $operator->insurance_expiry_date->isPast() ? 'text-error' : 'text-primary' }}">{{ $operator->insurance_expiry_date->format('d M Y') }}</span>
                    </div>
                @endif
                <p class="text-xs text-on-surface-variant pt-1">
                    Fleet: {{ $operator->buses_count }} buses · {{ $operator->routes_count }} routes ({{ $operator->active_routes_count }} active)
                </p>
            </div>
        </div>
    </div>
<!-- Recent bookings -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
        <h3 class="font-headline font-bold text-lg mb-4">Recent Bookings</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-outline-variant/20">
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Reference</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Passenger</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Route</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Amount</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                        <td class="py-3 px-2 font-mono text-xs">{{ $booking->reference_id }}</td>
                        <td class="py-3 px-2">{{ $booking->passenger_name ?? $booking->user->full_name ?? 'Guest' }}</td>
                        <td class="py-3 px-2">{{ $booking->route->origin ?? 'N/A' }} → {{ $booking->route->destination ?? 'N/A' }}</td>
                        <td class="py-3 px-2 font-bold">ZMW {{ number_format($booking->amount, 2) }}</td>
                        <td class="py-3 px-2">
                            @php
                                $badgeClass = match($booking->status) {
                                    'confirmed' => 'bg-primary/10 text-primary',
                                    'pending' => 'bg-tertiary/10 text-tertiary',
                                    'cancelled' => 'bg-error/10 text-error',
                                    default => 'bg-surface-container-high text-on-surface-variant',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeClass }}">{{ ucfirst($booking->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-6 text-center text-on-surface-variant">No bookings for this operator yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection