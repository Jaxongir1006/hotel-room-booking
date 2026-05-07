<x-mail::message>
# Your reservation has been cancelled

Hello {{ $user->name }},

We have cancelled your reservation **{{ $booking->reference }}** for **{{ $room->name }}** ({{ $booking->check_in->toFormattedDateString() }} → {{ $booking->check_out->toFormattedDateString() }}).

If this was a mistake, please rebook through your dashboard. We hope to welcome you again soon.

Warm regards,
The Aurelia Stay team
</x-mail::message>
