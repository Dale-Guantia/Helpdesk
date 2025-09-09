<x-mail::message>
# Ticket Transferred to Your Division

Dear {{ $divisionHead->name }},

Ticket #{{ $ticket->reference_id }} has been transferred to your division.

@if($transferredBy)
Transferred By: {{ $transferredBy->name }} ({{ $transferredBy->email }})
@endif

**Ticket Reference ID:** {{ $ticket->reference_id }}
<br>
**Message:** {{ Str::limit($ticket->description, 150) }}
<br>
**Submitted by:** {{ $ticket->user->name ?? 'N/A' }}
<br>
**Priority:** {{ $ticket->priority->priority_name ?? 'N/A' }}
<br>
**Status:** {{ $ticket->status->name ?? 'N/A' }}
<br>
**New Department:** {{ $ticket->department->department_name ?? 'N/A' }}
<br>
**New Division:** {{ $ticket->office->office_name ?? 'N/A' }}

<x-mail::button :url="url('/admin/tickets/' . $ticket->id)">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
