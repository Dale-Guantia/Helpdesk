<div>
    @if (!empty($attachments))
        <ul>
            @foreach ($attachments as $attachment)
                <li>
                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" target="_blank" class="text-sm underline" style="color: #118bf0">
                        {{ basename($attachment) }}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>No attachments available.</p>
    @endif
</div>
