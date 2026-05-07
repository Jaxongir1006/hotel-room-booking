<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $booking->reference }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #1a2744; padding: 40px; }
        h1 { font-size: 28px; margin: 0; color: #1a2744; }
        .gold { color: #c9a84c; letter-spacing: 4px; text-transform: uppercase; font-size: 11px; }
        .panel { border: 1px solid #e2e8f0; padding: 18px 22px; margin-top: 18px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { text-align: left; padding: 10px 0; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        .total { font-size: 18px; font-weight: bold; }
        .muted { color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
    <p class="gold">Aurelia Stay · Reservation Invoice</p>
    <h1>Invoice {{ $booking->reference }}</h1>
    <p class="muted">Issued: {{ now()->toFormattedDateString() }}</p>

    <div class="panel">
        <strong>Guest</strong><br>
        {{ $user->name }}<br>
        <span class="muted">{{ $user->email }}</span>
    </div>

    <div class="panel">
        <strong>Room</strong><br>
        {{ $room->name }} — {{ $room->type->label() }}, Floor {{ $room->floor }}
    </div>

    <table>
        <tr>
            <th>Check-in</th>
            <td>{{ $booking->check_in->toFormattedDateString() }}</td>
        </tr>
        <tr>
            <th>Check-out</th>
            <td>{{ $booking->check_out->toFormattedDateString() }}</td>
        </tr>
        <tr>
            <th>Nights</th>
            <td>{{ $booking->nights }}</td>
        </tr>
        <tr>
            <th>Rate / night</th>
            <td>${{ number_format((float) $room->price_per_night, 2) }}</td>
        </tr>
        <tr>
            <th class="total">Total</th>
            <td class="total">${{ number_format((float) $booking->total_price, 2) }}</td>
        </tr>
    </table>

    <p class="muted" style="margin-top: 40px">Thank you for staying with us.</p>
</body>
</html>
