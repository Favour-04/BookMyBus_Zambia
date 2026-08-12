@extends('layouts.admin')

@section('title', 'Operators')
@section('page_title', 'Operator Management')

@section('content')
    <!-- Summary strip -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Operators</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($counts['all']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Verified</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-primary">{{ number_format($counts['verified']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Pending</p>
            <p class="font-headline font-extrabold text-3xl mt-2 {{ $counts['pending'] > 0 ? 'text-tertiary' : '' }}">{{ number_format($counts['pending']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Fleet (Buses)</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($fleetTotal) }}</p>
        </div>
    </div>

    <!-- Filters + Search -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="inline-flex rounded-xl border border-outline-variant/20 bg-surface-container-lowest p-1">
            <a href="{{ route('admin.operators.index') }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ $status === 'all' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container-high' }}">All</a>
            <a href="{{ route('admin.operators.index', ['status' => 'verified']) }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ $status === 'verified' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container-high' }}">Verified</a>
            <a href="{{ route('admin.operators.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ $status === 'pending' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container-high' }}">Pending</a>
        </div>
        <form method="GET" class="flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search company, email, TPIN..."
                class="rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2 text-sm focus:outline-none focus:border-primary">
            <button type="submit" class="flex items-center gap-1 px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold">
                <span class="material-symbols-outlined text-base">search</span>Search
            </button>
            @if($search !== '')
                <a href="{{ route('admin.operators.index', ['status' => $status]) }}" class="flex items-center px-3 py-2 rounded-xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant">Clear</a>
            @endif
        </form>
    </div>
<!-- Operators table -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-outline-variant/20 bg-surface-container-low">
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Company</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Contact</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Fleet</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Routes</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Joined</th>
                        <th class="text-right py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operators as $operator)
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                @if($operator->logo_path)
                                    <img src="{{ asset('storage/' . $operator->logo_path) }}" class="h-9 w-9 rounded-full object-cover bg-surface-container-high" alt="">
                                @else
                                    <div class="h-9 w-9 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">{{ strtoupper(substr($operator->company_name ?? 'O', 0, 1)) }}</div>
                                @endif
                                <div>
                                    <a href="{{ route('admin.operators.show', $operator->id) }}" class="font-bold hover:text-primary">{{ $operator->company_name }}</a>
                                    <p class="text-xs text-on-surface-variant">{{ $operator->tpin ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <p class="text-xs">{{ $operator->email }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $operator->phone_number }}</p>
                        </td>
                        <td class="py-3 px-4">{{ $operator->buses_count }}</td>
                        <td class="py-3 px-4">
                            <span>{{ $operator->routes_count }}</span>
                            <span class="text-xs text-on-surface-variant">({{ $operator->active_routes_count }} active)</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($operator->is_verified)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">Verified</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/10 text-tertiary">Pending</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $operator->created_at->format('d M Y') }}</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.operators.show', $operator->id) }}" class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold border border-outline-variant/30 hover:border-primary hover:text-primary">
                                    <span class="material-symbols-outlined text-sm">visibility</span>View
                                </a>
                                @if(!$operator->is_verified)
                                    <form method="POST" action="{{ route('admin.operators.verify', $operator->id) }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold bg-primary text-white hover:bg-primary-container">
                                            <span class="material-symbols-outlined text-sm">verified_user</span>Verify
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-on-surface-variant">
                            No operators found{{ $search !== '' ? " matching \"{$search}\"" : '' }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($operators->hasPages())
            <div class="p-4 border-t border-outline-variant/15">
                {{ $operators->links() }}
            </div>
        @endif
    </div>
@endsection