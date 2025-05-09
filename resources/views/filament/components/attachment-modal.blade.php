<div>
    @if (!empty($attachments))
        <ul>
            @foreach ($attachments as $attachment)
                <li>
                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-blue-500 hover:underline">
                        {{ basename($attachment) }}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>No attachments available.</p>
    @endif
</div>
