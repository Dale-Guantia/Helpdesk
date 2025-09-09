<x-mail::message>
# New Ticket Assigned To You

Dear {{ $staff->name }},

A ticket has been assigned to you.

**Ticket Reference ID:** {{ $ticket->reference_id }}
<br>
**Description:** {{ Str::limit($ticket->description, 150) }}
<br>
**Submitted by:** {{ $ticket->user->name ?? 'N/A' }}
<br>
**Priority:** {{ $ticket->priority->priority_name ?? 'N/A' }}
<br>
**Status:** {{ $ticket->status->name ?? 'N/A' }}

<x-mail::button :url="url('/admin/tickets/' . $ticket->id)">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
