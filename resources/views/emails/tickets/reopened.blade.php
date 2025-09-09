<x-mail::message>
# Ticket Reopened

Dear {{ $reopenedToUser->name ?? 'User' }},

Ticket #{{ $ticket->reference_id }} has been reopened by the creator.

**Message:** {{ Str::limit($ticket->description, 150) }}
<br>
**Current Status:** {{ $ticket->status->status_name ?? 'N/A' }}
<br>
**Reopened By:** {{ $ticket->user->name ?? 'Ticket Creator' }}
<br>

Please review the ticket and take necessary actions.

<x-mail::button :url="url('/admin/tickets/' . $ticket->id)">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
