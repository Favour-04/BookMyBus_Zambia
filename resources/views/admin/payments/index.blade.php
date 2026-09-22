@extends('layouts.admin')

@section('title', 'Payments')
@section('page_title', 'System-Wide Payments')

@section('content')
    <!-- Summary strip -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Successful</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-primary">{{ number_format($stats['successful']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Failed</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-error">{{ number_format($stats['failed']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Pending</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-tertiary">{{ number_format($stats['pending']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Collected Revenue</p>
            <p class="font-headline font-extrabold text-2xl mt-2 leading-tight">ZMW {{ number_format($stats['revenue']) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4 mb-6">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-3" role="search" aria-label="Filter payments">
            <label for="pay-status" class="sr-only">Filter by payment status</label>
            <select id="pay-status" name="status" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
                <option value="all">All statuses</option>
                @foreach(['successful', 'failed', 'pending'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <label for="pay-method" class="sr-only">Filter by payment method</label>
            <select id="pay-method" name="method" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
                <option value="">All methods</option>
                @foreach($methods as $m)
                    <option value="{{ $m }}" {{ request('method') === $m ? 'selected' : '' }}>{{ is_string($m) ? ucwords(str_replace('_', ' ', $m)) : $m }}</option>
                @endforeach
            </select>
            <label for="pay-operator" class="sr-only">Filter by operator</label>
            <select id="pay-operator" name="operator_id" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
                <option value="">All operators</option>
                @foreach($operators as $operator)
                    <option value="{{ $operator->id }}" {{ request('operator_id') == $operator->id ? 'selected' : '' }}>{{ $operator->company_name }}</option>
                @endforeach
            </select>
            <label for="pay-from" class="sr-only">Filter from date</label>
            <input id="pay-from" type="date" name="from" value="{{ request('from') }}" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
            <label for="pay-to" class="sr-only">Filter to date</label>
            <input id="pay-to" type="date" name="to" value="{{ request('to') }}" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
            <label for="pay-search" class="sr-only">Search by transaction reference or booking</label>
            <input id="pay-search" type="text" name="search" value="{{ request('search') }}" placeholder="Txn ref / booking..."
                class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
            <div class="flex gap-2 col-span-2 md:col-span-1">
                <button type="submit" class="flex-1 flex items-center justify-center gap-1 px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold">
                    <span class="material-symbols-outlined text-base">filter_alt</span>Filter
                </button>
                @if(request('status') !== 'all' || request('method') || request('operator_id') || request('from') || request('to') || request('search'))
                    <a href="{{ route('admin.payments.index') }}" class="flex items-center px-3 py-2 rounded-xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant">Clear</a>
                @endif
            </div>
        </form>
    </div>
<!-- Payments table -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-outline-variant/20 bg-surface-container-low">
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Reference</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Booking</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Operator</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Method</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Amount</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Paid</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        @php
                            $statusClass = match($payment->status) {
                                'successful' => 'bg-primary/10 text-primary',
                                'failed' => 'bg-error/10 text-error',
                                'pending' => 'bg-tertiary/10 text-tertiary',
                                default => 'bg-surface-container-high text-on-surface-variant',
                            };
                        @endphp
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                        <td class="py-3 px-4 font-mono text-xs">{{ $payment->transaction_reference ?? '—' }}</td>
                        <td class="py-3 px-4">
                            @if($payment->booking)
                                <a href="{{ route('admin.bookings.show', $payment->booking->id) }}" class="font-mono text-xs font-bold hover:text-primary">{{ $payment->booking->reference_id }}</a>
                            @else —
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs">{{ $payment->booking?->route?->operator?->company_name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 capitalize">{{ $payment->payment_method ? ucwords(str_replace('_', ' ', $payment->payment_method)) : '—' }}</td>
                        <td class="py-3 px-4 font-bold">{{ $payment->currency ?? 'ZMW' }} {{ number_format($payment->amount, 2) }}</td>
                        <td class="py-3 px-4"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusClass }}">{{ ucfirst($payment->status) }}</span></td>
                        <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $payment->paid_at?->format('d M Y H:i') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-10 text-center text-on-surface-variant">No payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-outline-variant/15 flex flex-wrap items-center justify-between gap-2">
            <span class="text-sm text-on-surface-variant">Showing {{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }}</span>
            {{ $payments->links() }}
        </div>
    </div>
@endsection