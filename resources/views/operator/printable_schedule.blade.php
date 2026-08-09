<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule {{ $dateFrom }} to {{ $dateTo }} | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Manrope:wght@700;800&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Manrope', sans-serif; }
        @media print {
            @page { margin: 12mm; size: landscape; }
            .no-print { display: none !important; }
            body { font-size: 10px; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-white text-gray-900">
    <div class="no-print max-w-7xl mx-auto p-4 flex justify-end">
        <button onclick="window.print()" class="px-6 py-3 bg-primary text-white font-bold rounded-xl hover:brightness-110 flex items-center gap-2">Print Schedule</button>
    </div>
    <div class="max-w-7xl mx-auto p-4">
        <div class="text-center border-b-2 border-gray-300 pb-4 mb-4">
            <h1 class="text-2xl font-extrabold uppercase">Trip Schedule</h1>
            <p class="text-sm text-gray-500">{{ $operator->company_name }} | {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
        </div>
        <div class="grid grid-cols-4 gap-3 mb-4">
            <div class="p-3 bg-blue-50 rounded-lg text-center"><p class="text-xl font-extrabold text-blue-700">{{ $stats['total'] }}</p><p class="text-[10px] font-bold uppercase text-blue-600">Total Trips</p></div>
            <div class="p-3 bg-green-50 rounded-lg text-center"><p class="text-xl font-extrabold text-green-700">{{ $stats['total_booked'] }}/{{ $stats['total_capacity'] }}</p><p class="text-[10px] font-bold uppercase text-green-600">Booked/Capacity</p></div>
            <div class="p-3 bg-teal-50 rounded-lg text-center"><p class="text-xl font-extrabold text-teal-700">{{ $stats['total_capacity'] - $stats['total_booked'] }}</p><p class="text-[10px] font-bold uppercase text-teal-600">Available Seats</p></div>
            <div class="p-3 bg-purple-50 rounded-lg text-center"><p class="text-xl font-extrabold text-purple-700">ZMW {{ number_format($stats['total_revenue'], 0) }}</p><p class="text-[10px] font-bold uppercase text-purple-600">Est. Revenue</p></div>
        </div>
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b-2 border-gray-300">
                    <th class="py-2 px-2 text-[10px] font-bold uppercase text-gray-500">Date</th>
                    <th class="py-2 px-2 text-[10px] font-bold uppercase text-gray-500">Departure</th>
                    <th class="py-2 px-2 text-[10px] font-bold uppercase text-gray-500">Route</th>
                    <th class="py-2 px-2 text-[10px] font-bold uppercase text-gray-500">Bus</th>
                    <th class="py-2 px-2 text-[10px] font-bold uppercase text-gray-500">Driver</th>
                    <th class="py-2 px-2 text-[10px] font-bold uppercase text-gray-500">Booked</th>
                    <th class="py-2 px-2 text-[10px] font-bold uppercase text-gray-500">Capacity</th>
                    <th class="py-2 px-2 text-[10px] font-bold uppercase text-gray-500">Occupancy</th>
                    <th class="py-2 px-2 text-[10px] font-bold uppercase text-gray-500">Fare</th>
                    <th class="py-2 px-2 text-[10px] font-bold uppercase text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trips as $trip)
                <tr class="border-b border-gray-200">
                    <td class="py-2 px-2 font-bold">{{ \Carbon\Carbon::parse($trip['date_raw'])->format('D d M') }}</td>
                    <td class="py-2 px-2">{{ $trip['departure'] }}</td>
                    <td class="py-2 px-2">{{ $trip['route_from'] }} → {{ $trip['route_to'] }}</td>
                    <td class="py-2 px-2">{{ $trip['bus'] }}</td>
                    <td class="py-2 px-2">{{ $trip['driver'] ?? '—' }}</td>
                    <td class="py-2 px-2 font-bold">{{ $trip['booked'] }}</td>
                    <td class="py-2 px-2">{{ $trip['capacity'] }}</td>
                    <td class="py-2 px-2">
                        <div class="flex items-center gap-1">
                            <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden"><div class="h-full rounded-full {{ $trip['occupancy_percentage'] > 80 ? 'bg-green-500' : ($trip['occupancy_percentage'] > 50 ? 'bg-yellow-500' : 'bg-gray-400') }}" style="width: {{ $trip['occupancy_percentage'] }}%"></div></div>
                            <span class="text-xs">{{ $trip['occupancy_percentage'] }}%</span>
                        </div>
                    </td>
                    <td class="py-2 px-2">ZMW {{ number_format($trip['fare'], 2) }}</td>
                    <td class="py-2 px-2"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $trip['status_type'] === 'cancelled' ? 'bg-red-100 text-red-800' : ($trip['status_type'] === 'completed' ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800') }}">{{ $trip['status'] }}</span></td>
                </tr>
                @empty
                <tr><td colspan="10" class="py-8 text-center text-gray-500">No trips scheduled for this period.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4 pt-4 border-t border-gray-300 text-center text-xs text-gray-400">
            <p>Generated {{ now()->format('d F Y H:i') }} | BookMyBus Zambia</p>
        </div>
    </div>
</body>
</html>