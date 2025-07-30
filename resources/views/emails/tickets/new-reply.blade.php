<x-mail::message>
# New Reply on Ticket #{{ $comment->ticket->reference_id }}

Hello **{{ $recipient->name }}**,

A new reply has been posted on a ticket you are following.

**Ticket Title:** *{{ $comment->ticket->title }}*

---

<div style="margin-bottom: 16px;">
    <strong>{{ $comment->user->name }}</strong> wrote:
</div>

<x-mail::panel>
{!! nl2br(e($comment->comment)) !!}
</x-mail::panel>

You can view the full conversation and reply by clicking the button below.

<x-mail::button :url="route('filament.ticketing.resources.tickets.view', $comment->ticket)">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
