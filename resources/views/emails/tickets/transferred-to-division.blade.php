<x-mail::message>
# Ticket Transferred to Your Division

Dear {{ $divisionHead->name }},

Ticket #{{ $ticket->reference_id }} has been transferred to your division.

@if($transferredBy)
Transferred By: {{ $transferredBy->name }} ({{ $transferredBy->email }})
@endif

**Ticket Reference ID:** {{ $ticket->reference_id }}
**Title:** {{ $ticket->title }}
**Description:** {{ Str::limit($ticket->description, 150) }}
**Creator:** {{ $ticket->creator->name ?? 'N/A' }}
**Priority:** {{ $ticket->priority->name ?? 'N/A' }}
**Status:** {{ $ticket->status->name ?? 'N/A' }}
**New Department:** {{ $ticket->department->department_name ?? 'N/A' }}
**New Division:** {{ $ticket->office->office_name ?? 'N/A' }}

<x-mail::button :url="url('/admin/tickets/' . $ticket->id)">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
