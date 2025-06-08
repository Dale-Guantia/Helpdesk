<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Helpdesk Ticketing Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1, h2, h3 {
            text-align: center;
            color: #ffffff;
            background-color: #4169e1;
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
        .charts {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        .chart {
            width: 48%;
            height: auto;
        }
    </style>
</head>
<body>

    <h1>HELPDESK TICKETING REPORT</h1>

    {{-- <div class="charts">
        <div class="chart">
            <img src="{{ $barChart }}" style="width: 100%;">
            <h3 style="text-align: center; color: black;">TICKETS PER MONTH ({{ date('Y') }})</h3>
        </div>
        <div class="chart">
            <img src="{{ $pieChart }}" style="width: 100%;">
            <h3 style="text-align: center; color: black;">TOTAL TICKETS PER DIVISION ({{ date('Y') }})</h3>
        </div>
    </div> --}}

    <h3 class="section-title">USERS ACTIVITY</h3>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Division</th>
                <th>Resolved Tickets</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userActivities as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->department->department_name ?? 'N/A' }}</td>
                    <td>{{ $user->office->office_name ?? 'N/A' }}</td>
                    <td>{{ $user->resolved_tickets_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @foreach($divisionsData as $division)
        <h3 class="section-title">TICKETS OVERVIEW ({{ strtoupper($division['division_name']) }} DIVISION)</h3>
        <table>
            <thead>
                <tr>
                    <th>Issue name</th>
                    <th>Total Tickets</th>
                    <th>Average Resolve Time per Ticket</th>
                </tr>
            </thead>
            <tbody>
                @forelse($division['issueName_and_totalTickets'] as $report)
                    <tr>
                        <td>{{ $report['category_name'] }}</td>
                        <td>{{ $report['total_tickets'] }}</td>
                        <td>—</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">No tickets for this division.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

</body>
</html>
