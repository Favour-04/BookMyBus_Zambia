@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Bus Search Dashboard</h1>
        <p class="mt-1 text-sm text-slate-500">Find and manage available routes across Zambia.</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <p class="text-sm text-slate-500">Routes running today</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['routes_today'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <p class="text-sm text-slate-500">Active buses</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['active_buses'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <p class="text-sm text-slate-500">Pending bookings</p>
            <p class="mt-1 text-3xl font-bold text-amber-600">{{ $stats['pending_bookings'] }}</p>
        </div>
    </div>

    {{-- Search form --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Search for a route</h2>

        {{-- GET request: no CSRF token required for retrieving data --}}
        <form method="GET" action="{{ route('admin.search.results') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="origin" class="block text-sm font-medium text-slate-700 mb-1">Origin</label>
                <input list="origin-list" type="text" name="origin" id="origin" value="{{ old('origin') }}"
                       placeholder="e.g. Lusaka"
                       class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
                <datalist id="origin-list">
                    @foreach ($origins as $origin)
                        <option value="{{ $origin }}"></option>
                    @endforeach
                </datalist>
            </div>

            <div>
                <label for="destination" class="block text-sm font-medium text-slate-700 mb-1">Destination</label>
                <input list="destination-list" type="text" name="destination" id="destination" value="{{ old('destination') }}"
                       placeholder="e.g. Ndola"
                       class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
                <datalist id="destination-list">
                    @foreach ($destinations as $destination)
                        <option value="{{ $destination }}"></option>
                    @endforeach
                </datalist>
            </div>

            <div>
                <label for="travel_date" class="block text-sm font-medium text-slate-700 mb-1">Travel date</label>
                <input type="date" name="travel_date" id="travel_date" value="{{ old('travel_date') }}"
                       class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
            </div>

            <div>
                <label for="bus_class" class="block text-sm font-medium text-slate-700 mb-1">Bus class</label>
                <select name="bus_class" id="bus_class" class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm">
                    <option value="">Any class</option>
                    <option value="economy" @selected(old('bus_class') === 'economy')>Economy</option>
                    <option value="business" @selected(old('bus_class') === 'business')>Business</option>
                    <option value="luxury" @selected(old('bus_class') === 'luxury')>Luxury</option>
                </select>
            </div>

            <div class="md:col-span-4 flex justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">Reset</a>
                <button type="submit" class="inline-flex items-center px-5 py-2 rounded-lg bg-emerald-700 text-white text-sm font-semibold hover:bg-emerald-800">
                    Search buses
                </button>
            </div>
        </form>
    </div>
@endsection
