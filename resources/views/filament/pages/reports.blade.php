<x-filament-panels::page>
    <div>
        <h2 style="padding: 10px; font-weight: bold">User Activity</h2>
        @livewire('user-activity')
    </div>

    <div>
        {{-- Check if a user is authenticated --}}
        @auth
            {{-- Get the authenticated user --}}
            @php
                $user = auth()->user();
            @endphp

            {{-- Conditional H1 text based on user role --}}
            <h1 style="padding: 10px; font-weight: bold">
                @if ($user->isSuperAdmin())
                    Summary of Tickets by Division (All Departments)
                @elseif ($user->isDepartmentHead())
                    Summary of Tickets ({{ ucwords(strtolower($user->department->department_name)) }} Department)
                @elseif ($user->isDivisionHead())
                    Summary of Tickets ({{ ucwords(strtolower($user->office->office_name)) }} Division)
                @endif
            </h1>
        @endauth

        {{-- The Livewire component remains the same --}}
        @livewire('ticket-overview')
    </div>
</x-filament-panels::page>
