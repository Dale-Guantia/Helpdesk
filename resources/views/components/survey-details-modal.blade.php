<div class="p-4">
    <p class="text-md mb-2">Survey Details for: <strong>{{ $record->name }}</strong></p>
    <p class="text-md mb-6">Total Surveys: <span class="font-semibold">{{ $counts['total'] }}</span></p>
    <br/>

    <table class="w-full text-sm text-left">
        <thead class="border-b">
            <tr>
                <th class="p-2 text-left font-bold"></th>
                <th class="p-2 text-center font-bold">
                    <x-filament::badge color="danger">
                        Very Dissatisfied
                    </x-filament::badge>
                </th>
                <th class="p-2 text-center font-bold">
                    <x-filament::badge color="warning">
                        Dissatisfied
                    </x-filament::badge>
                </th>
                <th class="p-2 text-center font-bold">
                    <x-filament::badge color="primary">
                        Satisfied
                    </x-filament::badge>
                </th>
                <th class="p-2 text-center font-bold">
                    <x-filament::badge color="success">
                        Very Satisfied
                    </x-filament::badge>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b">
                <td class="p-2 font-bold">Responsiveness</td>
                <td class="p-2 text-center">{{ $counts['responsiveness']['Very Dissatisfied'] }}</td>
                <td class="p-2 text-center">{{ $counts['responsiveness']['Dissatisfied'] }}</td>
                <td class="p-2 text-center">{{ $counts['responsiveness']['Satisfied'] }}</td>
                <td class="p-2 text-center">{{ $counts['responsiveness']['Very Satisfied'] }}</td>
            </tr>
            <tr class="border-b">
                <td class="p-2 font-bold">Timeliness</td>
                <td class="p-2 text-center">{{ $counts['timeliness']['Very Dissatisfied'] }}</td>
                <td class="p-2 text-center">{{ $counts['timeliness']['Dissatisfied'] }}</td>
                <td class="p-2 text-center">{{ $counts['timeliness']['Satisfied'] }}</td>
                <td class="p-2 text-center">{{ $counts['timeliness']['Very Satisfied'] }}</td>
            </tr>
            <tr class="border-b">
                <td class="p-2 font-bold">Communication</td>
                <td class="p-2 text-center">{{ $counts['communication']['Very Dissatisfied'] }}</td>
                <td class="p-2 text-center">{{ $counts['communication']['Dissatisfied'] }}</td>
                <td class="p-2 text-center">{{ $counts['communication']['Satisfied'] }}</td>
                <td class="p-2 text-center">{{ $counts['communication']['Very Satisfied'] }}</td>
            </tr>
            <tr>
                <td class="p-2 font-bold">Total Count:</td>
                <td class="p-2 text-center font-bold">{{ $counts['total_counts']['Very Dissatisfied'] }}</td>
                <td class="p-2 text-center font-bold">{{ $counts['total_counts']['Dissatisfied'] }}</td>
                <td class="p-2 text-center font-bold">{{ $counts['total_counts']['Satisfied'] }}</td>
                <td class="p-2 text-center font-bold">{{ $counts['total_counts']['Very Satisfied'] }}</td>
            </tr>
            </tbody>
    </table>
</div>
