@extends('layouts.admin')

@section('title', 'Select a seat')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.search.results') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">&larr; Back to results</a>
        <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ $route->origin }} &rarr; {{ $route->destination }}</h1>
        <p class="mt-1 text-sm text-slate-500">
            {{ \Illuminate\Support\Carbon::parse($route->travel_date)->format('l, d M Y') }} &middot;
            Departs {{ \Illuminate\Support\Carbon::parse($route->departure_time)->format('H:i') }}
            &middot; {{ $route->operator->company_name ?? 'Operator' }}
            &middot; <span class="capitalize">{{ $route->bus->bus_class ?? '' }}</span> class
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Seat map --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-semibold text-slate-900">Choose your seat</h2>
                <div class="flex items-center gap-4 text-xs text-slate-500">
                    <span class="flex items-center gap-1"><span class="h-3 w-3 rounded-sm bg-emerald-100 border border-emerald-300 inline-block"></span> Available</span>
                    <span class="flex items-center gap-1"><span class="h-3 w-3 rounded-sm bg-slate-300 inline-block"></span> Taken</span>
                    <span class="flex items-center gap-1"><span class="h-3 w-3 rounded-sm bg-emerald-700 inline-block"></span> Selected</span>
                </div>
            </div>

            <form id="seat-form" method="POST" action="{{ route('admin.booking.hold', $route) }}">
                @csrf
                <input type="hidden" name="seat_number" id="seat_number" value="">

                <div class="flex flex-col items-center gap-6">
                    {{-- Driver indicator --}}
                    <div class="w-full max-w-xs flex justify-end pr-2">
                        <span class="text-xs text-slate-400 border border-slate-200 rounded px-2 py-1">Driver</span>
                    </div>

                    <div class="grid grid-cols-4 gap-x-6 gap-y-3 w-full max-w-xs">
                        @php $capacity = $route->bus->seat_capacity ?? 0; @endphp
                        @for ($seat = 1; $seat <= $capacity; $seat++)
                            @php $isBooked = in_array($seat, $bookedSeats, true); @endphp
                            @if ($loop->index > 0 && $loop->index % 4 === 2)
                                {{-- aisle gap handled by grid gap; column 3 starts new pair --}}
                            @endif
                            <button type="button"
                                    data-seat="{{ $seat }}"
                                    {{ $isBooked ? 'disabled' : '' }}
                                    class="seat-btn h-11 w-11 rounded-lg text-xs font-semibold flex items-center justify-center border transition
                                        {{ $isBooked
                                            ? 'bg-slate-300 text-slate-500 border-slate-300 cursor-not-allowed'
                                            : 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-emerald-100' }}
                                        {{ $loop->iteration % 4 === 2 ? 'mr-4' : '' }}">
                                {{ $seat }}
                            </button>
                        @endfor
                    </div>
                </div>

                @error('seat_number')
                    <p class="mt-4 text-sm text-red-600 text-center">{{ $message }}</p>
                @enderror

                <div class="mt-8 flex items-center justify-between border-t border-slate-100 pt-6">
                    <p class="text-sm text-slate-600">
                        Selected seat: <span id="selected-seat-label" class="font-semibold text-slate-900">None</span>
                    </p>
                    <button type="submit" id="confirm-seat-btn" disabled
                            class="inline-flex items-center px-5 py-2.5 rounded-lg bg-emerald-700 text-white text-sm font-semibold hover:bg-emerald-800 disabled:opacity-40 disabled:cursor-not-allowed">
                        Hold seat &amp; continue
                    </button>
                </div>
            </form>
        </div>

        {{-- Trip summary --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 h-fit">
            <h2 class="font-semibold text-slate-900 mb-4">Trip summary</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Bus</dt>
                    <dd class="text-slate-800">{{ $route->bus->model ?? 'N/A' }} ({{ $route->bus->registration_number ?? '-' }})</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Class</dt>
                    <dd class="capitalize text-slate-800">{{ $route->bus->bus_class ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Seats available</dt>
                    <dd class="text-slate-800">{{ $route->availableSeatsCount() }} / {{ $route->bus->seat_capacity ?? 0 }}</dd>
                </div>
                <div class="flex justify-between border-t border-slate-100 pt-3">
                    <dt class="text-slate-500">Fare per seat</dt>
                    <dd class="font-semibold text-slate-900">K{{ number_format($route->fare, 2) }}</dd>
                </div>
            </dl>

            <p class="mt-4 text-xs text-slate-400">
                Your seat will be held for 10 minutes while you complete payment.
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const seatButtons = document.querySelectorAll('.seat-btn');
        const seatInput = document.getElementById('seat_number');
        const seatLabel = document.getElementById('selected-seat-label');
        const confirmBtn = document.getElementById('confirm-seat-btn');

        seatButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                seatButtons.forEach((b) => {
                    if (!b.disabled) {
                        b.classList.remove('bg-emerald-700', 'text-white', 'border-emerald-700');
                        b.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-300');
                    }
                });

                btn.classList.remove('bg-emerald-50', 'text-emerald-800', 'border-emerald-300');
                btn.classList.add('bg-emerald-700', 'text-white', 'border-emerald-700');

                const seatNumber = btn.dataset.seat;
                seatInput.value = seatNumber;
                seatLabel.textContent = 'Seat ' + seatNumber;
                confirmBtn.disabled = false;
            });
        });
    </script>
@endpush
