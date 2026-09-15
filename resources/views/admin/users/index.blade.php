@extends('layouts.admin')

@section('title', 'Travelers')
@section('page_title', 'User Management')

@section('content')
    <!-- Summary strip -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Travelers</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($counts['all']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Active</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-primary">{{ number_format($counts['active']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Suspended</p>
            <p class="font-headline font-extrabold text-3xl mt-2 {{ $counts['suspended'] > 0 ? 'text-tertiary' : '' }}">{{ number_format($counts['suspended']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">New This Month</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($counts['new_month']) }}</p>
        </div>
    </div>

    <!-- Filters + Search -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="inline-flex rounded-xl border border-outline-variant/20 bg-surface-container-lowest p-1" role="group" aria-label="Filter travelers by status">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ $status === 'all' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container-high' }}">All</a>
            <a href="{{ route('admin.users.index', ['status' => 'active']) }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ $status === 'active' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container-high' }}">Active</a>
            <a href="{{ route('admin.users.index', ['status' => 'suspended']) }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ $status === 'suspended' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container-high' }}">Suspended</a>
        </div>
        <form method="GET" class="flex gap-2" role="search">
            <input type="hidden" name="status" value="{{ $status }}">
            <label for="user-search" class="sr-only">Search travelers by name, email, or phone</label>
            <input id="user-search" type="text" name="search" value="{{ $search }}" placeholder="Search name, email, phone..."
                class="rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2 text-sm focus:outline-none focus:border-primary">
            <button type="submit" class="flex items-center gap-1 px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold">
                <span class="material-symbols-outlined text-base">search</span>Search
            </button>
            @if($search !== '')
                <a href="{{ route('admin.users.index', ['status' => $status]) }}" class="flex items-center px-3 py-2 rounded-xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant">Clear</a>
            @endif
        </form>
    </div>
<!-- Travelers table -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-outline-variant/20 bg-surface-container-low">
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Traveler</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Contact</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Bookings</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Joined</th>
                        <th class="text-right py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">{{ strtoupper(substr($user->full_name ?? 'T', 0, 1)) }}</div>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="font-bold hover:text-primary">{{ $user->full_name }}</a>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <p class="text-xs">{{ $user->email }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $user->phone_number }}</p>
                        </td>
                        <td class="py-3 px-4">{{ $user->bookings_count }}</td>
                        <td class="py-3 px-4">
                            @if($user->isActive())
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/10 text-tertiary">Suspended</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold border border-outline-variant/30 hover:border-primary hover:text-primary">
                                    <span class="material-symbols-outlined text-sm">visibility</span>View
                                </a>
                                @if($user->isActive())
                                    <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}" onsubmit="return confirm('Suspend {{ addslashes($user->full_name) }}\'s account? They will no longer be able to log in.');">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold border border-error/30 text-error hover:bg-error-container/30">
                                            <span class="material-symbols-outlined text-sm">block</span>Suspend
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.activate', $user->id) }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold bg-primary text-white hover:bg-primary-container">
                                            <span class="material-symbols-outlined text-sm">check_circle</span>Activate
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-10 text-center text-on-surface-variant">No travelers found{{ $search !== '' ? " matching \"{$search}\"" : '' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-outline-variant/15 flex flex-wrap items-center justify-between gap-2">
            <span class="text-sm text-on-surface-variant">Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }}</span>
            {{ $users->links() }}
        </div>
    </div>
@endsection