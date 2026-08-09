<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $booking->reference_id }} | BookMyBus Zambia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            color: #1a1c1e;
            background: #ffffff;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header { text-align: center; margin-bottom: 32px; border-bottom: 2px solid #004614; padding-bottom: 20px; }
        .header h1 { font-family: 'Manrope', sans-serif; font-size: 24px; font-weight: 800; color: #004614; text-transform: uppercase; letter-spacing: -1px; }
        .header p { color: #40493e; font-size: 12px; margin-top: 4px; }
        .header .receipt-title { font-size: 14px; font-weight: 700; color: #40493e; text-transform: uppercase; letter-spacing: 2px; margin-top: 8px; }
        .section { margin-bottom: 24px; }
        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #40493e; margin-bottom: 12px; border-bottom: 1px dashed #bfcaba; padding-bottom: 6px; }
        .row { display: flex; justify-content: space-between; padding: 6px 0; }
        .row .label { font-size: 13px; color: #40493e; }
        .row .value { font-size: 13px; font-weight: 600; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .route-display { display: flex; align-items: center; justify-content: center; gap: 16px; padding: 16px; background: #f3f3f6; border-radius: 12px; margin-bottom: 16px; }
        .route-display .city { font-family: 'Manrope', sans-serif; font-size: 20px; font-weight: 800; }
        .route-display .arrow { font-size: 24px; color: #6f7a6c; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-confirmed, .status-successful { background: #00461410; color: #004614; }
        .status-pending { background: #7c040010; color: #7c0400; }
        .status-cancelled, .status-failed { background: #ba1a1a20; color: #ba1a1a; }
        .footer { text-align: center; margin-top: 32px; padding-top: 16px; border-top: 1px dashed #bfcaba; font-size: 11px; color: #6f7a6c; }
        .footer strong { color: #1a1c1e; }
        .amount-large { font-family: 'Manrope', sans-serif; font-size: 28px; font-weight: 800; color: #004614; text-align: center; padding: 16px; background: #f3f3f6; border-radius: 12px; }
        .divider { border: none; border-top: 1px dashed #bfcaba; margin: 16px 0; }
        @media print { body { padding: 20px; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:right; margin-bottom:16px;">
        <button onclick="window.print()" style="padding:8px 20px; background:#004614; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
            🖨️ Print Receipt
        </button>
    </div>
    <div class="header">
        <h1>{{ $operator->company_name ?? 'BookMyBus Zambia' }}</h1>
        <p>Operator Portal • Official Booking Receipt</p>
        <div class="receipt-title">Payment Receipt</div>
    </div>
    <div class="section">
        <div class="row">
            <span class="label">Reference ID</span>
            <span class="value" style="font-family: monospace; font-size:16px;">{{ $booking->reference_id }}</span>
        </div>
        <div class="row">
            <span class="label">Status</span>
            <span class="value">
                <span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
            </span>
        </div>
        <div class="row">
            <span class="label">Booking Date</span>
            <span class="value">{{ $booking->created_at->format('d M Y H:i') }}</span>
        </div>
    </div>
    <hr class="divider">
    <div class="section">
        <div class="section-title">Trip Details</div>
        <div class="route-display">
            <div class="city">{{ $booking->route->origin }}</div>
            <div class="arrow">→</div>
            <div class="city">{{ $booking->route->destination }}</div>
        </div>
        <div class="grid-3">
            <div>
                <div class="label">Travel Date</div>
                <div class="value" style="font-size:15px;">{{ $booking->route->travel_date instanceof \Carbon\Carbon ? $booking->route->travel_date->format('d M Y') : \Carbon\Carbon::parse($booking->route->travel_date)->format('d M Y') }}</div>
            </div>
            <div>
                <div class="label">Departure Time</div>
                <div class="value" style="font-size:15px;">{{ $booking->route->departure_time }}</div>
            </div>
            <div>
                <div class="label">Bus</div>
                <div class="value" style="font-size:15px;">{{ $booking->route->bus->registration_number ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    <hr class="divider">
    <div class="section">
        <div class="section-title">Passenger Information</div>
        <div class="grid-2">
            <div>
                <div class="label">Name</div>
                <div class="value" style="font-size:15px;">{{ $booking->passenger_name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="label">Phone Number</div>
                <div class="value" style="font-size:15px;">{{ $booking->phone_number ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="label">ID Number</div>
                <div class="value" style="font-size:15px;">{{ $booking->id_number ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="label">Seat Number</div>
                <div class="value" style="font-size:15px;">#{{ $booking->seat_number }}</div>
            </div>
        </div>
    </div>
    <hr class="divider">
    <div class="section">
        <div class="section-title">Payment Information</div>
        @if($booking->payment)
            <div class="grid-2">
                <div>
                    <div class="label">Payment Method</div>
                    <div class="value" style="font-size:15px;">{{ str_replace('_', ' ', ucfirst($booking->payment->payment_method)) }}</div>
                </div>
                <div>
                    <div class="label">Transaction Ref</div>
                    <div class="value" style="font-size:15px; font-family:monospace;">{{ $booking->payment->transaction_reference ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="label">Payment Status</div>
                    <div class="value" style="font-size:15px;">
                        <span class="status-badge status-{{ $booking->payment->status }}">{{ ucfirst($booking->payment->status) }}</span>
                    </div>
                </div>
                <div>
                    <div class="label">Paid At</div>
                    <div class="value" style="font-size:15px;">{{ $booking->payment->paid_at ? $booking->payment->paid_at->format('d M Y H:i') : 'N/A' }}</div>
                </div>
            </div>
        @else
            <div class="label">No payment record found.</div>
        @endif
    </div>
    <hr class="divider">
    <div class="section">
        <div class="amount-large">ZMW {{ number_format($booking->amount, 2) }}</div>
    </div>
    <div class="footer">
        <p><strong>{{ $operator->company_name ?? 'BookMyBus Zambia' }}</strong></p>
        <p>This is an official booking receipt. Generated on {{ now()->format('d M Y H:i') }}</p>
        <p style="margin-top:8px; font-size:10px;">BookMyBus Zambia © {{ date('Y') }} — Smart Travel Solutions</p>
    </div>
</body>
</html>