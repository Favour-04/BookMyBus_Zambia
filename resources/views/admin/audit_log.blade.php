@extends('layouts.admin')

@section('title', 'Audit Log')
@section('page_title', 'Admin Audit Log')

@section('content')
    <!-- Summary strip -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Actions</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($totalActions) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Actions Today</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($actionsToday) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Unique Events</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($uniqueEvents) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Last Activity</p>
            <p class="font-headline font-extrabold text-sm mt-2">{{ $lastActivity?->created_at?->diffForHumans() ?? '—' }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4 mb-6">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <select name="event" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
                <option value="">All events</option>
                @foreach($eventTypes as $type)
                    <option value="{{ $type }}" {{ request('event') === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
            <input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..."
                   class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
            <div class="flex gap-2">
                <button type="submit" class="flex-1 flex items-center justify-center gap-1 px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold">
                    <span class="material-symbols-outlined text-base">filter_alt</span>Filter
                </button>
                @if(request('event') || request('from') || request('to') || request('search'))
                    <a href="{{ route('admin.audit-log.index') }}" class="flex items-center px-3 py-2 rounded-xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant">Clear</a>
                @endif
            </div>
        </form>
    </div>
<!-- Logs table -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-outline-variant/20 bg-surface-container-low">
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Event</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Quick Summary</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Target</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Admin</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">When</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                        <td class="py-3 px-4">
                            <a href="{{ route('admin.audit-log.show', $log->id) }}">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">{{ $log->eventLabel() }}</span>
                            </a>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('admin.audit-log.show', $log->id) }}" class="hover:text-primary">{{ $log->description ?? '—' }}</a>
                        </td>
                        <td class="py-3 px-4 text-xs text-on-surface-variant">{{ class_basename($log->auditable_type ?? '') }}{{ $log->auditable_id ? ' #' . $log->auditable_id : '' }}</td>
                        <td class="py-3 px-4">{{ $log->admin?->full_name ?? $log->admin?->email ?? '—' }}</td>
                        <td class="py-3 px-4 text-xs text-on-surface-variant">
                            {{ $log->created_at->format('d M Y H:i') }}
                            <span class="block text-[10px] opacity-70">{{ $log->created_at->diffForHumans() }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-10 text-center text-on-surface-variant">No admin activity recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="p-4 border-t border-outline-variant/15">{{ $logs->links() }}</div>
        @endif
    </div>
@endsection