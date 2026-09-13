@extends('layouts.admin')

@section('title', 'Search Results')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Available Buses</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $routes->total() }} route{{ $routes->total() === 1 ? '' : 's' }} found
                @if (!empty($filters['origin'])) from <span class="font-medium text-slate-700">{{ $filters['origin'] }}</span> @endif
                @if (!empty($filters['destination'])) to <span class="font-medium text-slate-700">{{ $filters['destination'] }}</span> @endif
                @if (!empty($filters['travel_date'])) on <span class="font-medium text-slate-700">{{ \Illuminate\Support\Carbon::parse($filters['travel_date'])->format('d M Y') }}</span> @endif
            </p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">&larr; New search</a>
    </div>

    {{-- Refine filters --}}
    <form method="GET" action="{{ route('admin.search.results') }}" class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-5 gap-3">
        <input type="text" name="origin" value="{{ $filters['origin'] ?? '' }}" placeholder="Origin"
               class="rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
        <input type="text" name="destination" value="{{ $filters['destination'] ?? '' }}" placeholder="Destination"
               class="rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
        <input type="date" name="travel_date" value="{{ $filters['travel_date'] ?? '' }}"
               class="rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
        <select name="bus_class" class="rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
            <option value="">Any class</option>
            <option value="economy" @selected(($filters['bus_class'] ?? '') === 'economy')>Economy</option>
            <option value="business" @selected(($filters['bus_class'] ?? '') === 'business')>Business</option>
            <option value="luxury" @selected(($filters['bus_class'] ?? '') === 'luxury')>Luxury</option>
        </select>
        <button type="submit" class="rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900">Apply filters</button>
    </form>

    {{-- Fare matrix --}}
    @if ($fareMatrix->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-200">
                <h2 class="font-semibold text-slate-900">Fare matrix by bus class</h2>
            </div>
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-2 text-left font-medium text-slate-500">Class</th>
                        <th class="px-5 py-2 text-left font-medium text-slate-500">Trips</th>
                        <th class="px-5 py-2 text-left font-medium text-slate-500">Lowest fare</th>
                        <th class="px-5 py-2 text-left font-medium text-slate-500">Average fare</th>
                        <th class="px-5 py-2 text-left font-medium text-slate-500">Highest fare</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($fareMatrix as $row)
                        <tr>
                            <td class="px-5 py-2 capitalize font-medium text-slate-800">{{ $row->bus_class }}</td>
                            <td class="px-5 py-2 text-slate-600">{{ $row->trip_count }}</td>
                            <td class="px-5 py-2 text-slate-600">K{{ number_format($row->min_fare, 2) }}</td>
                            <td class="px-5 py-2 text-slate-600">K{{ number_format($row->avg_fare, 2) }}</td>
                            <td class="px-5 py-2 text-slate-600">K{{ number_format($row->max_fare, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Results table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-slate-500">Route</th>
                    <th class="px-5 py-3 text-left font-medium text-slate-500">Date &amp; time</th>
                    <th class="px-5 py-3 text-left font-medium text-slate-500">Operator / Bus</th>
                    <th class="px-5 py-3 text-left font-medium text-slate-500">Class</th>
                    <th class="px-5 py-3 text-left font-medium text-slate-500">Seats left</th>
                    <th class="px-5 py-3 text-left font-medium text-slate-500">Fare</th>
                    <th class="px-5 py-3 text-right font-medium text-slate-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($routes as $route)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $route->origin }} &rarr; {{ $route->destination }}</td>
                        <td class="px-5 py-3 text-slate-600">
                            {{ \Illuminate\Support\Carbon::parse($route->travel_date)->format('d M Y') }}<br>
                            <span class="text-xs text-slate-400">{{ \Illuminate\Support\Carbon::parse($route->departure_time)->format('H:i') }}
                                @if ($route->arrival_time) &rarr; {{ \Illuminate\Support\Carbon::parse($route->arrival_time)->format('H:i') }} @endif
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-600">
                            {{ $route->operator->company_name ?? 'N/A' }}<br>
                            <span class="text-xs text-slate-400">{{ $route->bus->registration_number ?? '' }}</span>
                        </td>
                        <td class="px-5 py-3 capitalize text-slate-600">{{ $route->bus->bus_class ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $route->availableSeatsCount() > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                {{ $route->availableSeatsCount() }} / {{ $route->bus->seat_capacity ?? 0 }}
                            </span>
                        </td>
                        <td class="px-5 py-3 font-semibold text-slate-900">K{{ number_format($route->fare, 2) }}</td>
                        <td class="px-5 py-3 text-right">
                            @if ($route->availableSeatsCount() > 0)
                                <a href="{{ route('admin.booking', $route) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-700 text-white text-xs font-semibold hover:bg-emerald-800">
                                    Select seat
                                </a>
                            @else
                                <span class="text-xs text-slate-400">Fully booked</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                            No routes match your search. Try a different origin, destination or date.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $routes->links() }}
    </div>
@endsection
