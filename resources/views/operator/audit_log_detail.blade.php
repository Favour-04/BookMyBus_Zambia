@extends('layouts.operator')

@section('title', 'Audit Log Entry')
@section('page_title', 'Audit Log Entry')

@push('head')
<style>
.json-view { background: #f4f5f7; border-radius: 8px; padding: 16px; font-family: 'Cascadia Code', 'Fira Code', monospace; font-size: 13px; line-height: 1.6; overflow-x: auto; white-space: pre-wrap; word-break: break-word; }
</style>
@endpush

@section('content')

            <a href="{{ route('operator.audit-log.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline mb-6">
                <span class="material-symbols-outlined text-sm">arrow_back</span>Back to Audit Log
            </a>

            <!-- Entry Summary -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @php
                                $eventColor = match(true) {
                                    str_contains($log->event, 'login') || str_contains($log->event, 'logout') => 'bg-primary/10 text-primary',
                                    str_contains($log->event, 'cancelled') || str_contains($log->event, 'deleted') => 'bg-error-container/20 text-error',
                                    str_contains($log->event, 'created') => 'bg-tertiary/10 text-tertiary',
                                    str_contains($log->event, 'updated') || str_contains($log->event, 'changed') => 'bg-amber-100 text-amber-800',
                                    str_contains($log->event, 'viewed') || str_contains($log->event, 'exported') => 'bg-sky-100 text-sky-800',
                                    default => 'bg-surface-container text-on-surface-variant',
                                };
                            @endphp
                        ">{{ $log->eventLabel() }}</span>
                        <h3 class="font-headline text-xl font-extrabold mt-2">{{ $log->description }}</h3>
                    </div>
                    <div class="text-right text-sm text-on-surface-variant">
                        <p>{{ $log->created_at->format('l, d F Y') }}</p>
                        <p class="font-bold">{{ $log->created_at->format('H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <h4 class="font-headline font-bold text-sm text-on-surface-variant uppercase mb-4">Event Information</h4>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Event Key</dt>
                            <dd class="text-sm font-mono font-bold">{{ $log->event }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Operator</dt>
                            <dd class="text-sm font-bold">{{ $log->operator->company_name ?? 'Unknown' }} (#{{ $log->operator_id }})</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Record ID</dt>
                            <dd class="text-sm font-mono">#{{ $log->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Target Type</dt>
                            <dd class="text-sm font-mono">{{ $log->auditable_type ? class_basename($log->auditable_type) : '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Target ID</dt>
                            <dd class="text-sm font-mono">{{ $log->auditable_id ? '#' . $log->auditable_id : '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <h4 class="font-headline font-bold text-sm text-on-surface-variant uppercase mb-4">Request Information</h4>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">IP Address</dt>
                            <dd class="text-sm font-mono font-bold">{{ $log->ip_address ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">User Agent</dt>
                            <dd class="text-sm text-right max-w-[250px] truncate" title="{{ $log->user_agent ?? '' }}">{{ $log->user_agent ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Created At</dt>
                            <dd class="text-sm">{{ $log->created_at->format('d M Y H:i:s') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Updated At</dt>
                            <dd class="text-sm">{{ $log->updated_at->format('d M Y H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Old Values -->
            @if($log->old_values)
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
                <h4 class="font-headline font-bold text-sm text-on-surface-variant uppercase mb-3">Previous Values</h4>
                <div class="json-view">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
            </div>
            @endif

            <!-- New Values -->
            @if($log->new_values)
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                <h4 class="font-headline font-bold text-sm text-on-surface-variant uppercase mb-3">New Values</h4>
                <div class="json-view">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
            </div>
            @endif

            @if(!$log->old_values && !$log->new_values)
            <div class="bg-surface-container-low rounded-2xl border border-outline-variant/15 p-8 text-center">
                <span class="material-symbols-outlined text-4xl text-outline-variant block mb-2">info</span>
                <p class="text-on-surface-variant">No data payload recorded for this entry.</p>
            </div>
            @endif
@endsection
