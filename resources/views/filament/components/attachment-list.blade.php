<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @foreach($getState() as $state)
            <p><a href="{{ asset('storage/' . ltrim($state, '/')) }}" target="_blank" class="text-primary underline" style="color: #fbbf24">{{ basename($state) }}</a></p>
        @endforeach
    </div>
</x-dynamic-component>
