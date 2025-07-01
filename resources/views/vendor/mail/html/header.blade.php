@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('images/PrimaryLogo.png') }}" alt="{{ config('app.name') }} logo" style="width: 100%; height: 100px; max-width: 100%; max-height: 100px;">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
