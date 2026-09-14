@extends('layouts.operator')

@section('title', 'Promo Codes')
@section('page_title', 'Promo Codes')

@push('head')
<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }

        #promo-drawer {
            transform: translateX(100%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #promo-drawer.open { transform: translateX(0); }
        #drawer-backdrop {
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
        }
        #drawer-backdrop.open { opacity: 1; pointer-events: auto; }
</style>
@endpush

@section('content')


            @if(session('success'))
            <div class="mb-6 p-4 bg-primary/10 border border-primary/20 rounded-xl text-body-sm text-primary font-medium">
                {{ session('success') }}
            </div>
            @endif

            <!-- Header + Add Button -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-body-sm text-on-surface-variant">{{ count($promoCodes) }} promo code(s) configured</p>
                </div>
                <button onclick="openDrawer()" class="flex items-center gap-2 px-5 py-2 bg-primary text-on-primary rounded-xl text-body-sm font-bold hover:brightness-110 transition-all">
                    <span class="material-symbols-outlined text-sm">add</span>
                    New Promo Code
                </button>
            </div>

            <!-- Promo Codes Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-low border-b border-outline-variant/15">
                        <tr>
                            <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Code</th>
                            <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Discount</th>
                            <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Usage</th>
                            <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Valid Period</th>
                            <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Status</th>
                            <th class="px-5 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        @forelse($promoCodes as $promo)
                        @php
                            $now = now();
                            $isExpired = $now->gt($promo->valid_until);
                            $isValid = $promo->is_active && !$isExpired && (!$promo->usage_limit || $promo->used_count < $promo->usage_limit);
                        @endphp
                        <tr class="hover:bg-surface-container-low/60 transition-colors">
                            <td class="px-5 py-4">
                                <span class="font-bold text-body-sm text-on-surface tracking-wide uppercase">{{ $promo->code }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-body-sm text-primary">
                                    @if($promo->discount_type === 'fixed')
                                        ZMW {{ number_format($promo->discount_value, 2) }}
                                    @else
                                        {{ $promo->discount_value }}%
                                        @if($promo->max_discount_amount)
                                            <span class="text-body-sm text-on-surface-variant">(max ZMW {{ number_format($promo->max_discount_amount, 2) }})</span>
                                        @endif
                                    @endif
                                </span>
                                @if($promo->min_booking_amount > 0)
                                <span class="block text-body-sm text-on-surface-variant">Min: ZMW {{ number_format($promo->min_booking_amount, 2) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-body-sm text-on-surface">
                                {{ $promo->used_count }} / {{ $promo->usage_limit ?? '∞' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-body-sm text-on-surface">{{ $promo->valid_from->format('d M Y') }}</span>
                                <span class="text-body-sm text-on-surface-variant"> → </span>
                                <span class="text-body-sm text-on-surface">{{ $promo->valid_until->format('d M Y') }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-label-sm font-bold px-2.5 py-1 rounded-full {{ $isValid ? 'bg-primary/10 text-primary' : 'bg-surface-container-high text-on-surface-variant' }}">
                                    {{ $isValid ? 'Active' : ($isExpired ? 'Expired' : 'Inactive') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <form action="{{ route('operator.promo-codes.destroy', $promo->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete promo code {{ $promo->code }}?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container/20 transition-colors" title="Delete">
                                        <span class="material-symbols-outlined" style="font-size:18px">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <span class="material-symbols-outlined text-4xl text-outline-variant block mb-2">confirmation_number</span>
                                <p class="font-body-md text-body-md text-on-surface-variant">No promo codes yet.</p>
                                <button onclick="openDrawer()" class="mt-3 text-primary font-bold text-body-sm hover:underline">Create your first promo code</button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Backdrop -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-on-surface/30 z-30" onclick="closeDrawer()"></div>

    <!-- Drawer: New Promo Code -->
    <aside id="promo-drawer" class="fixed top-0 right-0 h-full w-[440px] bg-surface-container-lowest border-l border-outline-variant/15 z-40 flex flex-col shadow-xl">
        <div class="px-6 py-5 border-b border-outline-variant/15 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface">New Promo Code</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Create a discount code for your passengers</p>
            </div>
            <button onclick="closeDrawer()" class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('operator.promo-codes.store') }}" method="POST" class="flex-grow overflow-y-auto custom-scrollbar px-6 py-6 space-y-5">
            @csrf

            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Promo Code</label>
                <input type="text" name="code" placeholder="e.g. WELCOME10" maxlength="20" required
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3 uppercase">
                <p class="text-body-sm text-on-surface-variant mt-1">Will be auto-converted to uppercase</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Discount Type</label>
                    <select name="discount_type" required class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed (ZMW)</option>
                    </select>
                </div>
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Discount Value</label>
                    <input type="number" name="discount_value" placeholder="e.g. 10" min="0" step="0.01" required
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Min. Booking Amount (ZMW)</label>
                    <input type="number" name="min_booking_amount" placeholder="0" min="0" step="0.01"
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Max Discount (ZMW) <span class="text-on-surface-variant/50">(optional)</span></label>
                    <input type="number" name="max_discount_amount" placeholder="For % discounts" min="0" step="0.01"
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
            </div>

            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Usage Limit <span class="text-on-surface-variant/50">(optional)</span></label>
                <input type="number" name="usage_limit" placeholder="Leave empty for unlimited" min="1"
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Valid From</label>
                    <input type="datetime-local" name="valid_from" required
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Valid Until</label>
                    <input type="datetime-local" name="valid_until" required
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-outline-variant/15">
                <button type="button" onclick="closeDrawer()" class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-body-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" class="flex-[2] py-3 rounded-xl bg-primary text-on-primary text-body-sm font-bold hover:brightness-110 transition-all">Create Promo Code</button>
            </div>
        </form>
    </aside>

@endsection

@push('scripts')
<script>
        function openDrawer() {
            document.getElementById('promo-drawer').classList.add('open');
            document.getElementById('drawer-backdrop').classList.add('open');
        }
        function closeDrawer() {
            document.getElementById('promo-drawer').classList.remove('open');
            document.getElementById('drawer-backdrop').classList.remove('open');
        }
        document.querySelectorAll('a, button').forEach(el => {
            el.addEventListener('mousedown', () => el.classList.add('scale-[0.98]'));
            el.addEventListener('mouseup',   () => el.classList.remove('scale-[0.98]'));
            el.addEventListener('mouseleave',() => el.classList.remove('scale-[0.98]'));
        });
    </script>
@endpush

