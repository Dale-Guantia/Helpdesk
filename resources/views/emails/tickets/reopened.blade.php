<x-mail::message>
# Ticket Reopened

Dear {{ $reopenedToUser->name ?? 'User' }},

Ticket #{{ $ticket->reference_id }} has been reopened by the creator.

**Title:** {{ $ticket->title }}
**Description:** {{ Str::limit($ticket->description, 150) }}
**Current Status:** {{ $ticket->status->name ?? 'N/A' }}
**Reopened By:** {{ $ticket->creator->name ?? 'Ticket Creator' }}

Please review the ticket and take necessary actions.

<x-mail::button :url="url('/admin/tickets/' . $ticket->id)">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
