@extends('layouts.admin')

@section('title', 'Audit Log Entry')
@section('page_title', 'Audit Log Detail')

@section('content')
    <a href="{{ route('admin.audit-log.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-primary mb-6">
        <span class="material-symbols-outlined text-base">arrow_back</span>Back to Audit Log
    </a>

    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-2xl bg-primary/20 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-2xl">history</span>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">{{ $log->eventLabel() }}</span>
                        <span class="text-xs font-mono bg-surface-container-low px-2 py-0.5 rounded">{{ $log->event }}</span>
                    </div>
                    <p class="text-sm text-on-surface-variant mt-2">{{ $log->description ?? '—' }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">When</p>
                <p class="font-headline font-bold text-lg">{{ $log->created_at->format('d M Y H:i') }}</p>
                <p class="text-xs text-on-surface-variant">{{ $log->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Who / metadata -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Metadata</h3>
            <dl class="grid grid-cols-1 gap-y-4 text-sm">
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Admin</dt><dd class="mt-1 font-semibold">{{ $log->admin?->full_name ?? $log->admin?->email ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Target</dt><dd class="mt-1 font-semibold">{{ class_basename($log->auditable_type ?? '') }}{{ $log->auditable_id ? ' #' . $log->auditable_id : '' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">IP Address</dt><dd class="mt-1 font-mono text-xs">{{ $log->ip_address ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">User Agent</dt><dd class="mt-1 text-xs break-words">{{ $log->user_agent ?? '—' }}</dd></div>
            </dl>
        </div>

        <!-- State changes -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Recorded State</h3>

            <p class="text-xs font-bold uppercase text-on-surface-variant tracking-wider mb-1">Old Values</p>
            <div class="bg-surface-container-low rounded-xl p-3 font-mono text-xs whitespace-pre-wrap mb-5">
                @if($log->old_values)
                    {{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                @else
                    <span class="text-on-surface-variant">— none recorded —</span>
                @endif
            </div>

            <p class="text-xs font-bold uppercase text-on-surface-variant tracking-wider mb-1">New Values</p>
            <div class="bg-surface-container-low rounded-xl p-3 font-mono text-xs whitespace-pre-wrap">
                @if($log->new_values)
                    {{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                @else
                    <span class="text-on-surface-variant">— none recorded —</span>
                @endif
            </div>
        </div>
    </div>
@endsection