<x-mail::message>
# New Ticket Assigned To You

Dear {{ $staff->name }},

A ticket has been assigned to you.

**Ticket Reference ID:** {{ $ticket->reference_id }}
**Title:** {{ $ticket->title }}
**Description:** {{ Str::limit($ticket->description, 150) }}
**Creator:** {{ $ticket->creator->name ?? 'N/A' }}
**Priority:** {{ $ticket->priority->name ?? 'N/A' }}
**Status:** {{ $ticket->status->name ?? 'N/A' }}
**Department:** {{ $ticket->department->department_name ?? 'N/A' }}
**Division:** {{ $ticket->office->office_name ?? 'N/A' }}

<x-mail::button :url="url('/admin/tickets/' . $ticket->id)">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
