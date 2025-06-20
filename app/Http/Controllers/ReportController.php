<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function report(Request $request)
    {
        // Get start and end dates from the request query parameters
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Parse dates with Carbon for easier manipulation and ensure full day coverage
        // If a date is not provided, it will remain null.
        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        // 2. Filter User Activities (Resolved Tickets Count)
        // We use withCount on the 'resolvedTickets' relationship, applying the date filter directly to it.
        $userActivities = User::with(['department', 'office'])
            ->where('department_id', 1)
            ->where('role', '!=', 4) // Assuming role 4 is not an agent
            ->withCount(['resolvedTickets' => function ($query) use ($startDate, $endDate) {
                // Filter the tickets by their 'resolved_at' timestamp within the given range
                $query->when($startDate, fn($q) => $q->where('resolved_at', '>=', $startDate))
                      ->when($endDate, fn($q) => $q->where('resolved_at', '<=', $endDate));
            }])
            ->get();

        // Get all average resolve times per problem category in one query
        // 3. Filter Average Resolve Times per Problem Category
        // The average resolve time is calculated only for tickets that have been resolved
        // and fall within the specified date range based on their 'resolved_at' timestamp.
        $avgResolveTimesQuery = Ticket::select([
                'problem_category_id',
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, tickets.created_at, tickets.resolved_at)) as avg_resolve_seconds')
            ])
            ->whereNotNull('resolved_at');

        // Apply date filtering to the tickets used for average resolve time calculation
        $avgResolveTimesQuery->when($startDate, fn($query) => $query->where('resolved_at', '>=', $startDate))
                             ->when($endDate, fn($query) => $query->where('resolved_at', '<=', $endDate));

        $avgResolveTimes = $avgResolveTimesQuery->groupBy('problem_category_id')
                                                ->pluck('avg_resolve_seconds', 'problem_category_id');


        // 4. Filter Divisions and their Problem Categories (Total Tickets Count)
        // The 'tickets_count' for each problem category will reflect tickets created within the range.
        $divisions = Office::where('department_id', 1)
            ->withCount(['tickets' => function ($query) use ($startDate, $endDate) {
                // Count all tickets for the division within the date range (based on created_at)
                $query->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
                      ->when($endDate, fn($q) => $q->where('created_at', '<=', $endDate));
            }])
            ->with(['problemCategories' => function ($query) use ($startDate, $endDate) {
                // Apply date filtering to the 'tickets' count within each problem category
                // Assuming 'tickets_count' means tickets CREATED within the range for this report.
                // If you want tickets RESOLVED within the range here, change 'created_at' to 'resolved_at'.
                $query->withCount(['tickets' => function ($ticketQuery) use ($startDate, $endDate) {
                    $ticketQuery->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
                                ->when($endDate, fn($q) => $q->where('created_at', '<=', $endDate));
                }]);
            }])
            ->get();

        // Attach avg_resolve_time to each problem category (this logic remains the same)
        foreach ($divisions as $division) {
            foreach ($division->problemCategories as $category) {
                $seconds = $avgResolveTimes[$category->id] ?? null;

                if ($seconds) {
                    $hours = floor($seconds / 3600);
                    $minutes = floor(($seconds % 3600) / 60);
                    $category->average_resolve_time = "{$hours}h {$minutes}m";
                } else {
                    $category->average_resolve_time = '0h 0m';
                }
            }
        }

        // Return PDF view
        $pdf = Pdf::loadView('filament.reports.report_pdf_template', [
            'userActivities' => $userActivities,
            'divisions' => $divisions,
            // Pass the selected dates to the template for display in the report header
            'reportStartDate' => $startDate ? $startDate->format('F j, Y') : null,
            'reportEndDate' => $endDate ? $endDate->format('F j, Y') : null,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('report.pdf');
    }
}
