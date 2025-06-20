<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Helpdesk Ticketing Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h3 {
            text-align: center;
            color: #ffffff;
            background-color: #0e2f66;
            padding: 10px;
            margin: 0;
        }
        .section-title {
            font-weight: bold;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #777;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #efefef;
        }
    </style>
</head>
<body>

    <h1 style="text-align: center;">TICKETING REPORT</h1>

    <h3 class="section-title">Number of Tickets per Division as of (DATE RANGE)</h3>
    <table>
        <thead>
            <tr>
                <th>Division</th>
                <th>Number of tickets</th>
            </tr>
        </thead>
        <tbody>
            @forelse($divisions as $division)
                <tr>
                    <td>{{ $division->office_name }}</td>
                    <td>{{ $division->tickets_count }}</td> {{-- Display the new count here --}}
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center;">No divisions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="section-title">User Activity</h3>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Division</th>
                <th>Resolved Tickets</th>
            </tr>
        </thead>
        <tbody>
            @forelse($userActivities as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->office->office_name ?? 'N/A' }}</td>
                    <td>{{ $user->resolved_tickets_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No Users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2 style="text-align: center;">Ticket Overview per Division</h2>

    {{-- UPDATED LOOP: Iterate over the Eloquent collection --}}
    @foreach($divisions as $division)
        <h3 class="section-title">{{ ucwords(strtolower($division->office_name)) }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Issue description</th>
                    <th>Total Tickets</th>
                    <th>Average Resolve Time per Ticket</th>
                </tr>
            </thead>
            <tbody>
                {{-- UPDATED INNER LOOP: Use the 'problemCategories' relationship --}}
                @forelse($division->problemCategories as $category)
                    <tr>
                        {{-- Access model properties directly --}}
                        <td>{{ $category->category_name }}</td>
                        <td>{{ $category->tickets_count }}</td> {{-- Use the count from withCount() --}}
                        <td>{{ $category->average_resolve_time }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">No issues assigned to this division.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

</body>
</html>
