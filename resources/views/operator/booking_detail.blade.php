@extends('layouts.operator')

@section('title')Booking #{{ $booking->reference_id }}@endsection
@section('page_title')Booking #{{ $booking->reference_id }}@endsection

@section('content')


            <!-- Back link -->
            <a href="{{ route('operator.bookings.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline mb-6">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Back to All Bookings
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left Column: Booking Info -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Status & Reference Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <p class="text-xs font-bold uppercase text-on-surface-variant tracking-wider">Reference ID</p>
                                <p class="font-headline text-2xl font-extrabold mt-1">{{ $booking->reference_id }}</p>
                            </div>
                            @php
                                $statusColor = match($booking->status) {
                                    'confirmed' => 'bg-primary/10 text-primary',
                                    'pending' => 'bg-tertiary/10 text-tertiary',
                                    'cancelled' => 'bg-error-container/20 text-error',
                                    'expired' => 'bg-surface-container-high text-on-surface-variant',
                                    default => 'bg-surface-container text-on-surface-variant',
                                };
                            @endphp
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $statusColor }}">
                                {{ $booking->status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 pt-6 border-t border-dashed border-outline-variant/20">
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Seat Number</p>
                                <p class="font-headline font-extrabold text-xl mt-1">#{{ $booking->seat_number }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Amount</p>
                                <p class="font-headline font-extrabold text-xl mt-1">ZMW {{ number_format($booking->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Booked On</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Held Until</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->held_until ? $booking->held_until->format('d M Y H:i') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger Info Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Passenger Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Name</p>
                                <p class="font-bold text-base">{{ $booking->passenger_name ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Phone Number</p>
                                <p class="font-bold text-base">{{ $booking->phone_number ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">ID Number</p>
                                <p class="font-bold text-base">{{ $booking->id_number ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Trip Info Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Trip Information</h3>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex-1">
                                <p class="text-xs font-bold uppercase text-on-surface-variant tracking-wider">From</p>
                                <p class="font-headline font-extrabold text-xl mt-1">{{ $booking->route->origin }}</p>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="material-symbols-outlined text-outline">arrow_forward</span>
                            </div>
                            <div class="flex-1 text-right">
                                <p class="text-xs font-bold uppercase text-on-surface-variant tracking-wider">To</p>
                                <p class="font-headline font-extrabold text-xl mt-1">{{ $booking->route->destination }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-6 border-t border-dashed border-outline-variant/20">
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Travel Date</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->route->travel_date instanceof \Carbon\Carbon ? $booking->route->travel_date->format('d M Y') : \Carbon\Carbon::parse($booking->route->travel_date)->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Departure</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->route->departure_time }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Operator</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->route->operator->company_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Bus</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->route->bus->registration_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Boarding Status Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Boarding Status</h3>
                        @if($booking->isBoarded())
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary">check_circle</span>
                                </div>
                                <div>
                                    <p class="font-bold text-primary text-lg">Boarded</p>
                                    <p class="text-xs text-on-surface-variant">
                                        Boarded at {{ $booking->boarded_at->format('d M Y H:i') }}
                                        @if($booking->boarded_by)
                                            by {{ $booking->boarded_by }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-surface-container-highest flex items-center justify-center">
                                    <span class="material-symbols-outlined text-on-surface-variant">hourglass_empty</span>
                                </div>
                                <div>
                                    <p class="font-bold text-on-surface-variant">Not Boarded Yet</p>
                                    <p class="text-xs text-on-surface-variant">Passenger has not been checked in.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Notes Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Booking Notes</h3>
                        <form method="POST" action="{{ route('operator.bookings.notes', $booking->id) }}">
                            @csrf
                            @method('PATCH')
                            <textarea name="notes" rows="3"
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none"
                                placeholder="Add internal notes for this booking...">{{ old('notes', $booking->notes) }}</textarea>
                            <div class="flex justify-end mt-2">
                                <button type="submit"
                                    class="px-4 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-xs font-bold text-on-surface-variant hover:bg-surface-container transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">save</span>
                                    Save Notes
                                </button>
                            </div>
                        </form>
                        @if($booking->notes)
                            <div class="mt-3 pt-3 border-t border-dashed border-outline-variant/20">
                                <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider mb-1">Saved Notes</p>
                                <p class="text-sm text-on-surface bg-surface-container-low p-3 rounded-xl">{{ $booking->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Fare Breakdown Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Fare Breakdown</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant">Base Fare</span>
                                <span class="font-bold">ZMW {{ number_format($booking->base_fare ?? $booking->amount, 2) }}</span>
                            </div>
                            @if($booking->service_fee_total > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant">Service Fees</span>
                                <span class="font-bold">ZMW {{ number_format($booking->service_fee_total, 2) }}</span>
                            </div>
                            @endif
                            @if($booking->discount_amount > 0)
                            <div class="flex justify-between text-sm text-primary">
                                <span>Discount @if($booking->promoCode)({{ $booking->promoCode->code }})@endif</span>
                                <span class="font-bold">-ZMW {{ number_format($booking->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="border-t border-dashed border-outline-variant/20 pt-3 flex justify-between font-headline font-extrabold text-lg">
                                <span>Total</span>
                                <span class="text-primary">ZMW {{ number_format($booking->amount, 2) }}</span>
                            </div>
                            @if($booking->status === 'cancelled' && $booking->refund_amount > 0)
                            <div class="border-t border-dashed border-outline-variant/20 pt-3 flex justify-between text-sm">
                                <span class="text-on-surface-variant">Refund Amount</span>
                                <span class="font-bold text-primary">ZMW {{ number_format($booking->refund_amount, 2) }}</span>
                            </div>
                            @if($booking->cancellationRule)
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant">Refund Policy</span>
                                <span class="font-bold">{{ $booking->cancellationRule->name }} ({{ $booking->cancellationRule->refund_percentage }}%)</span>
                            </div>
                            @endif
                            @if($booking->cancelled_at)
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant">Cancelled On</span>
                                <span class="font-bold">{{ $booking->cancelled_at->format('d M Y H:i') }}</span>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>

                    <!-- Payment Info Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Payment Information</h3>
                        @if($booking->payment)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Payment Method</p>
                                    <p class="font-bold text-base">{{ str_replace('_', ' ', ucfirst($booking->payment->payment_method)) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Transaction Reference</p>
                                    <p class="font-mono font-bold text-sm">{{ $booking->payment->transaction_reference ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Payment Status</p>
                                    @php
                                        $paymentStatusColor = match($booking->payment->status) {
                                            'successful' => 'bg-primary/10 text-primary',
                                            'pending' => 'bg-tertiary/10 text-tertiary',
                                            'failed' => 'bg-error-container/20 text-error',
                                            'refunded' => 'bg-surface-container-high text-on-surface-variant',
                                            default => 'bg-surface-container text-on-surface-variant',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $paymentStatusColor }}">
                                        {{ ucfirst($booking->payment->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Paid At</p>
                                    <p class="font-bold text-sm">{{ $booking->payment->paid_at ? $booking->payment->paid_at->format('d M Y H:i') : 'N/A' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 py-4">
                                <span class="material-symbols-outlined text-outline-variant">payments</span>
                                <p class="text-on-surface-variant">No payment record found for this booking.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Actions -->
                <div class="space-y-6">

                    <!-- Actions Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('operator.bookings.edit', $booking->id) }}"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-surface-container-low rounded-xl text-sm font-bold text-on-surface hover:bg-surface-container-highest transition-colors">
                            <span class="material-symbols-outlined text-primary">edit</span>
                            Edit Booking
                        </a>

                        <!-- Board / Undo Board button -->
                        @if($booking->status === 'confirmed')
                            @if($booking->isBoarded())
                            <form method="POST" action="{{ route('operator.bookings.undo-board', $booking->id) }}" onsubmit="return confirm('Remove boarded status for this passenger?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="flex items-center gap-3 w-full px-4 py-3 bg-tertiary/10 rounded-xl text-sm font-bold text-tertiary hover:bg-tertiary/20 transition-colors">
                                    <span class="material-symbols-outlined text-tertiary">undo</span>
                                    Undo Boarded
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('operator.bookings.board', $booking->id) }}" onsubmit="return confirm('Mark this passenger as boarded?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="flex items-center gap-3 w-full px-4 py-3 bg-primary/10 rounded-xl text-sm font-bold text-primary hover:bg-primary/20 transition-colors">
                                    <span class="material-symbols-outlined text-primary">check_circle</span>
                                    Mark as Boarded
                                </button>
                            </form>
                            @endif
                        @endif

                        <a href="{{ route('operator.bookings.receipt', $booking->id) }}" target="_blank"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-surface-container-low rounded-xl text-sm font-bold text-on-surface hover:bg-surface-container-highest transition-colors">
                            <span class="material-symbols-outlined text-primary">receipt_long</span>
                            Print Receipt
                        </a>
                        <a href="{{ route('operator.trips.bookings', $booking->route_id) }}"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-surface-container-low rounded-xl text-sm font-bold text-on-surface hover:bg-surface-container-highest transition-colors">
                            <span class="material-symbols-outlined text-primary">group</span>
                            View All Trip Bookings
                        </a>
                        <a href="{{ route('operator.trips.seat-map', $booking->route_id) }}"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-surface-container-low rounded-xl text-sm font-bold text-on-surface hover:bg-surface-container-highest transition-colors">
                            <span class="material-symbols-outlined text-primary">event_seat</span>
                            View Seat Map
                        </a>
                            @if($booking->status === 'confirmed' && !$booking->isBoarded())
                            <form method="POST" action="{{ route('operator.bookings.process-refund', $booking->id) }}" onsubmit="return confirm('Process refund for this confirmed booking? This will cancel the booking and calculate a refund based on your cancellation rules.')">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-3 w-full px-4 py-3 bg-tertiary/10 rounded-xl text-sm font-bold text-tertiary hover:bg-tertiary/20 transition-colors">
                                    <span class="material-symbols-outlined text-tertiary">currency_exchange</span>
                                    Process Refund
                                </button>
                            </form>
                            @endif

                            @if($booking->status === 'pending' && !$booking->isExpired())
                            <form method="POST" action="{{ route('operator.bookings.cancel', $booking->id) }}" onsubmit="return confirm('Cancel this booking? This cannot be undone.')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="flex items-center gap-3 w-full px-4 py-3 bg-error-container/10 rounded-xl text-sm font-bold text-error hover:bg-error-container/20 transition-colors">
                                    <span class="material-symbols-outlined text-error">cancel</span>
                                    Cancel Booking
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Info Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Quick Info</h3>
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Reference</span>
                                <span class="font-mono font-bold">{{ $booking->reference_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Status</span>
                                <span class="font-bold">{{ ucfirst($booking->status) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Seat</span>
                                <span class="font-bold">#{{ $booking->seat_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Amount</span>
                                <span class="font-bold text-primary">ZMW {{ number_format($booking->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Age</span>
                                <span class="font-bold">{{ $booking->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
@endsection
