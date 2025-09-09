<x-mail::message>
# New Ticket for Your Division

Dear {{ $divisionHead->name }},

A new ticket has been created and is now visible to your division.

**Ticket Reference ID:** {{ $ticket->reference_id }}
<br>
**Message:** {{ Str::limit($ticket->description, 150) }}
<br>
**Submitted by:** {{ $ticket->user->name ?? 'N/A' }}

<x-mail::button :url="url('/admin/tickets/' . $ticket->id)">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
