<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @foreach($getState() as $state)
            <p><a href="{{ asset('storage/' . ltrim($state, '/')) }}" target="_blank" class="text-sm underline" style="color: #118bf0">{{ basename($state) }}</a></p>
        @endforeach
    </div>
</x-dynamic-component>
