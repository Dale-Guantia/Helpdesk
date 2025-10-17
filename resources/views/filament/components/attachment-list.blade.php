<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @php
            $attachments = $getState() ?? [];
        @endphp

        @foreach($attachments as $state)
            @if(is_string($state))
                <p>
                    <a href="{{ asset('storage/' . ltrim($state, '/')) }}"
                       target="_blank"
                       class="text-sm underline">
                        {{ basename($state) }}
                    </a>
                </p>
            @endif
        @endforeach
    </div>
</x-dynamic-component>
