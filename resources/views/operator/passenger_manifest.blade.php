<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Manifest - {{ $route->origin }} → {{ $route->destination }} | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Manrope:wght@700;800&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Manrope', sans-serif; }
        @media print {
            @page { margin: 15mm; }
            .no-print { display: none !important; }
            body { font-size: 11px; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body class="bg-white text-gray-900">

    <!-- Print button (hidden when printing) -->
    <div class="no-print max-w-4xl mx-auto p-6 flex justify-end">
        <button onclick="window.print()" class="px-6 py-3 bg-primary text-white font-bold rounded-xl hover:brightness-110 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">print</span>
            Print Manifest
        </button>
    </div>

    <div class="max-w-4xl mx-auto p-6">

        <!-- Header -->
        <div class="text-center border-b-2 border-gray-300 pb-6 mb-6">
            <h1 class="text-2xl font-extrabold uppercase tracking-tight">Passenger Manifest</h1>
            <p class="text-sm text-gray-500 mt-1">BookMyBus Zambia — Operator Copy</p>
        </div>

        <!-- Trip Info -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div>
                <p class="text-[10px] font-bold uppercase text-gray-500 tracking-wider">Route</p>
                <p class="font-bold text-lg">{{ $route->origin }} → {{ $route->destination }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase text-gray-500 tracking-wider">Date</p>
                <p class="font-bold">{{ $route->travel_date instanceof \Carbon\Carbon ? $route->travel_date->format('l, d M Y') : \Carbon\Carbon::parse($route->travel_date)->format('l, d M Y') }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase text-gray-500 tracking-wider">Departure</p>
                <p class="font-bold">{{ $route->departure_time }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase text-gray-500 tracking-wider">Bus</p>
                <p class="font-bold">{{ $route->bus->registration_number ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-5 gap-3 mb-6">
            <div class="p-3 bg-green-50 rounded-lg text-center">
                <p class="text-xl font-extrabold text-green-700">{{ $stats['total'] }}</p>
                <p class="text-[10px] font-bold uppercase text-green-600">Total</p>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg text-center">
                <p class="text-xl font-extrabold text-blue-700">{{ $stats['confirmed'] }}</p>
                <p class="text-[10px] font-bold uppercase text-blue-600">Confirmed</p>
            </div>
            <div class="p-3 bg-orange-50 rounded-lg text-center">
                <p class="text-xl font-extrabold text-orange-700">{{ $stats['pending'] }}</p>
                <p class="text-[10px] font-bold uppercase text-orange-600">Pending</p>
            </div>
            <div class="p-3 bg-red-50 rounded-lg text-center">
                <p class="text-xl font-extrabold text-red-700">{{ $stats['cancelled'] }}</p>
                <p class="text-[10px] font-bold uppercase text-red-600">Cancelled</p>
            </div>
            <div class="p-3 bg-teal-50 rounded-lg text-center">
                <p class="text-xl font-extrabold text-teal-700">{{ $stats['boarded'] }}</p>
                <p class="text-[10px] font-bold uppercase text-teal-600">Boarded</p>
            </div>
        </div>

        <!-- Passenger Table -->
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-gray-300">
                    <th class="py-3 px-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">#</th>
                    <th class="py-3 px-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Seat</th>
                    <th class="py-3 px-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Passenger Name</th>
                    <th class="py-3 px-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Phone</th>
                    <th class="py-3 px-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">ID Number</th>
                    <th class="py-3 px-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Reference</th>
                    <th class="py-3 px-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Status</th>
                    <th class="py-3 px-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Boarded</th>
                    <th class="py-3 px-2 text-[10px] font-bold uppercase text-gray-500 tracking-wider">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $index => $booking)
                <tr class="border-b border-gray-200 {{ $booking->status === 'cancelled' ? 'text-gray-400 line-through' : '' }}">
                    <td class="py-2.5 px-2 text-sm">{{ $index + 1 }}</td>
                    <td class="py-2.5 px-2 font-bold text-sm">#{{ $booking->seat_number }}</td>
                    <td class="py-2.5 px-2 text-sm">{{ $booking->passenger_name ?? 'N/A' }}</td>
                    <td class="py-2.5 px-2 text-sm">{{ $booking->phone_number ?? '—' }}</td>
                    <td class="py-2.5 px-2 text-sm">{{ $booking->id_number ?? '—' }}</td>
                    <td class="py-2.5 px-2 font-mono text-xs">{{ $booking->reference_id }}</td>
                    <td class="py-2.5 px-2 text-sm">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : ($booking->status === 'pending' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="py-2.5 px-2 text-sm">
                        @if($booking->isBoarded())
                            <span class="text-green-600 font-bold">✓</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="py-2.5 px-2 text-sm font-bold">ZMW {{ number_format($booking->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-8 text-center text-gray-500">No bookings for this trip.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-300 font-bold">
                    <td colspan="8" class="py-3 px-2 text-right text-sm">Total Revenue:</td>
                    <td class="py-3 px-2 text-sm">ZMW {{ number_format($stats['total_revenue'], 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-300 text-center text-xs text-gray-400">
            <p>Generated on {{ now()->format('d F Y H:i') }} | BookMyBus Zambia</p>
            <p class="mt-1">This is an official passenger manifest document.</p>
        </div>

    </div>

</body>
</html>