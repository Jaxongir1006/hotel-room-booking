<x-mail::message>
# Reservation received, {{ $user->name }}

Thank you for choosing **Aurelia Stay**. We have received your reservation and our concierge team will confirm it shortly.

**Reference:** {{ $booking->reference }}

<x-mail::panel>
**{{ $room->name }}** ({{ $room->type->label() }}) — Floor {{ $room->floor }}

- Check-in: {{ $booking->check_in->toFormattedDateString() }}
- Check-out: {{ $booking->check_out->toFormattedDateString() }}
- Nights: {{ $booking->nights }}
- Total: ${{ number_format((float) $booking->total_price, 2) }}
</x-mail::panel>

@if ($booking->notes)
**Your note to us:** {{ $booking->notes }}
@endif

If you need to make any changes, visit your bookings dashboard or reply to this email.

Warm regards,
The Aurelia Stay team
</x-mail::message>
