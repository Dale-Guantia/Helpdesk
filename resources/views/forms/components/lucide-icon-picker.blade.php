@php
    $icons = $getIcons();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            open: false,
            search: '',
            displayLimit: 50, // 1. Start with 50 icons
            selected: @entangle($getStatePath()),
            icons: @js($icons),

            // 2. Logic to filter AND limit the results based on scroll position
            get filteredIcons() {
                let result = this.icons;

                // Filter by search if user is typing
                if (this.search !== '') {
                    result = result.filter(i =>
                        i.label.toLowerCase().includes(this.search.toLowerCase())
                    );
                }

                // Only return the number of icons allowed by the current limit
                return result.slice(0, this.displayLimit);
            },

            // 3. Function to load more icons
            loadMore() {
                this.displayLimit += 50;
            }
        }"
        class="relative"
        @click.outside="open = false"
    >

        <div
            @click="open = !open"
            class="fi-input-wrapper flex cursor-pointer items-center gap-3 overflow-hidden rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm ring-1 ring-gray-950/10 transition duration-75 hover:ring-gray-950/30 dark:border-white/10 dark:bg-gray-900 dark:ring-white/20 dark:hover:ring-white/30"
        >
            <div class="flex items-center gap-3 w-full">
                <template x-if="selected">
                    <div class="text-primary-600 dark:text-primary-400">
                        <span class="flex h-6 w-6 items-center justify-center"
                            x-html="icons.find(i => i.value === selected)?.svg">
                        </span>
                    </div>
                </template>

                <template x-if="!selected">
                    <span class="text-[14px] text-black-400 dark:text-black-500">Select an icon...</span>
                </template>

                <span x-text="selected ? (icons.find(i => i.value === selected)?.label) : ''" class="text-sm text-black-950 dark:text-white"></span>
            </div>

            <div
                x-show="selected"
                @click.stop="selected = null"
                class="mr-1 cursor-pointer text-gray-400 hover:text-red-500 transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </div>

            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </div>

        <div
            x-show="open"
            x-cloak
            class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-xl dark:border-white/10 dark:bg-gray-900"
        >
            <div class="p-3 border-b border-gray-100 dark:border-white/10">
                <input
                    type="text"
                    x-model="search"
                    placeholder="Search icons..."
                    class="fi-input block w-full rounded-lg border-gray-300 bg-white/5 py-2 px-3 text-sm transition duration-75 focus:border-primary-600 focus:ring-1 focus:ring-primary-600 dark:border-white/10 dark:bg-white/5 dark:text-white"
                />
            </div>

            <div
                class="max-h-72 overflow-y-auto p-2 custom-scrollbar"
                @scroll="if($el.scrollTop + $el.clientHeight >= $el.scrollHeight - 50) { loadMore() }"
            >

                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 gap-3">
                    <template x-for="icon in filteredIcons" :key="icon.value">
                        <div
                            @click="selected = icon.value; open = false"
                            class="group flex cursor-pointer flex-col items-center justify-center gap-2 rounded-md border border-transparent p-3 hover:bg-gray-100 hover:text-primary-600 dark:hover:bg-white/5 dark:hover:text-primary-400"
                            :class="selected === icon.value ? 'bg-primary-50 text-primary-600 dark:bg-white/10' : 'text-black-500 dark:text-black-400'"
                        >
                            <span class="h-9 w-9" x-html="icon.svg"></span>

                            <span x-text="icon.label" class="text-[14px] text-center leading-tight truncate w-full"></span>
                        </div>
                    </template>
                </div>

                <div x-show="filteredIcons.length === 0" class="py-4 text-center text-sm text-black-500">
                    No icons found
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
