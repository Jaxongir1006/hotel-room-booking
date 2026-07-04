<x-mail::message>
# New reservation: {{ $booking->reference }}

A new reservation has been placed and is awaiting confirmation.

<x-mail::panel>
**Guest:** {{ $guest->name }} ({{ $guest->email }})

**Room:** {{ $room->name }} — {{ $room->type->label() }}, Floor {{ $room->floor }}

**Dates:** {{ $booking->check_in->toFormattedDateString() }} → {{ $booking->check_out->toFormattedDateString() }}

**Nights:** {{ $booking->nights }} · **Total:** ${{ number_format((float) $booking->total_price, 2) }}
</x-mail::panel>

Review and confirm this reservation in the admin dashboard.
</x-mail::message>
