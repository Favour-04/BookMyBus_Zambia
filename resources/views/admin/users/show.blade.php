@extends('layouts.admin')

@section('title', $user->full_name)
@section('page_title', 'Traveler Profile')

@section('content')
    <a href="{{ route('admin.users.index', ['status' => request('status', 'all')]) }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-primary mb-6">
        <span class="material-symbols-outlined text-base">arrow_back</span>Back to Travelers
    </a>

    <!-- Profile header -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-2xl bg-primary/20 flex items-center justify-center text-primary font-headline font-extrabold text-2xl">{{ strtoupper(substr($user->full_name ?? 'T', 0, 1)) }}</div>
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="font-headline font-extrabold text-2xl">{{ $user->full_name }}</h3>
                        @if($user->isActive())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/10 text-tertiary">Suspended</span>
                        @endif
                    </div>
                    <p class="text-sm text-on-surface-variant">{{ $user->email }} · {{ $user->phone_number }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if($user->isActive())
                    <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}" onsubmit="return confirm('Suspend {{ addslashes($user->full_name) }}\'s account? They will no longer be able to log in.');">
                        @csrf
                        <button type="submit" class="flex items-center gap-1 px-4 py-2 rounded-xl border border-error/30 text-error text-sm font-bold hover:bg-error-container/30">
                            <span class="material-symbols-outlined text-base">block</span>Suspend
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.activate', $user->id) }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-1 px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-container">
                            <span class="material-symbols-outlined text-base">check_circle</span>Activate
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Remove {{ addslashes($user->full_name) }}\'s account? Their booking records are soft-deleted and preserved.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center gap-1 px-4 py-2 rounded-xl border border-error/30 text-error text-sm font-bold hover:bg-error-container/30">
                        <span class="material-symbols-outlined text-base">delete</span>Remove
                    </button>
                </form>
            </div>
        </div>
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
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Spent</p>
            <p class="font-headline font-extrabold text-2xl mt-2 leading-tight">ZMW {{ number_format($stats['total_spent']) }}</p>
        </div>
    </div>
<!-- Account details + history -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Account Details</h3>
            <dl class="grid grid-cols-1 gap-y-4 text-sm">
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Full Name</dt><dd class="mt-1 font-semibold">{{ $user->full_name }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Email</dt><dd class="mt-1 font-semibold">{{ $user->email }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Phone Number</dt><dd class="mt-1 font-semibold">{{ $user->phone_number }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Preferred Language</dt><dd class="mt-1 font-semibold capitalize">{{ $user->preferred_language ?? 'en' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Role</dt><dd class="mt-1 font-semibold capitalize">{{ $user->role }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Joined</dt><dd class="mt-1 font-semibold">{{ $user->created_at->format('d M Y H:i') }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Account Status</dt>
                    <dd class="mt-1">
                        @if($user->isActive())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/10 text-tertiary">Suspended</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Booking History</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-outline-variant/20">
                            <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Reference</th>
                            <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Route</th>
                            <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Amount</th>
                            <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                            <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Booked</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                            <td class="py-3 px-2 font-mono text-xs">{{ $booking->reference_id }}</td>
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
                            <td class="py-3 px-2 text-xs text-on-surface-variant">{{ $booking->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-6 text-center text-on-surface-variant">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection