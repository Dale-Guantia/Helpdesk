<x-mail::message>
# Your Ticket Has Been Resolved!

Dear {{ $ticket->creator->name ?? 'Ticket Creator' }},

Your ticket #{{ $ticket->reference_id }} has been marked as "Resolved".

**Description:** {{ Str::limit($ticket->description, 150) }}
**Status:** {{ $ticket->status->name ?? 'N/A' }}
**Resolved By:** {{ $ticket->resolvedByUser->name ?? 'N/A' }}

<x-mail::panel>
If you have further concerns or the issue is not fully resolved, you can re-open this ticket within the system.
</x-mail::panel>

<x-mail::button :url="url('/admin/tickets/' . $ticket->id)">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
