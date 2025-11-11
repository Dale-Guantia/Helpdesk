<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\Ticket;
use App\Models\Survey;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ReportController extends Controller
{
    public function report(Request $request)
    {
        // Get start and end dates from the request query parameters
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $includeZeroTickets = $request->query('include_zero_tickets') === 'true';

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

        // Filter out users who have no resolved tickets in the selected date range

        if (!$includeZeroTickets) {
            $userActivities = $userActivities
            ->filter(fn ($user) => $user->resolved_tickets_count > 0)
            ->values();
        }

        // 3. Filter Divisions and their Problem Categories (Total Tickets Count)
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

        if (!$includeZeroTickets) {
            $divisions = $divisions
            ->filter(fn ($division) => $division->tickets_count > 0)
            ->values();
        }

        // Get all average resolve times per problem category in one query
        // 4. Filter Average Resolve Times per Problem Category
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

        // Attach avg_resolve_time to each problem category (this logic remains the same)
        foreach ($divisions as $division) {
            if (!$includeZeroTickets) {
                $division->setRelation('problemCategories',
                $division->problemCategories->filter(fn ($category) => $category->tickets_count > 0)
                );
            }
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

        return $pdf->stream('employeecare_report_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Generates and streams the Customer Satisfactory Survey (CSS) Report PDF.
     * Uses the exact query logic from SurveyRateCounter Livewire component.
     */
    public function cssReport(Request $request)
    {
        // 1. Get date range and filter flag from the request
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $includeZeroSurveys = $request->query('include_zero_surveys') === 'true';

        // Parse dates with Carbon for easier manipulation and ensure full day coverage
        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        // Determine the date range string for the report header (You don't use this variable in the PDF load, but it's good practice)
        if ($startDate && $endDate) {
            $reportDateRange = "Report for: {$startDate->format('F j, Y')} to {$endDate->format('F j, Y')}";
        } else {
            // Fallback if dates are somehow missing
            $reportDateRange = "Report as of: " . Carbon::now()->format('F j, Y');
        }


        // 2. Get the list of users eligible for the report, matching the getTableQuery logic.
        $eligibleUsers = User::query()
            ->where(function (Builder $query) {
                $query->where('role', User::ROLE_STAFF)
                    ->orWhere(function (Builder $query) {
                        $query->where('role', User::ROLE_DIVISION_HEAD)
                            ->where('department_id', 1); // Only for a specific department
                    });
            })
            ->pluck('id');

        // 3. Fetch the aggregated survey data using DB::raw (complex aggregation)
        $surveyAggregatesQuery = Survey::selectRaw("
                user_id,
                COUNT(DISTINCT id) AS total_surveys_completed,
                SUM(CASE WHEN responsiveness_rating = 'Very Dissatisfied' THEN 1 ELSE 0 END +
                    CASE WHEN timeliness_rating = 'Very Dissatisfied' THEN 1 ELSE 0 END +
                    CASE WHEN communication_rating = 'Very Dissatisfied' THEN 1 ELSE 0 END) AS very_dissatisfied_count,

                SUM(CASE WHEN responsiveness_rating = 'Dissatisfied' THEN 1 ELSE 0 END +
                    CASE WHEN timeliness_rating = 'Dissatisfied' THEN 1 ELSE 0 END +
                    CASE WHEN communication_rating = 'Dissatisfied' THEN 1 ELSE 0 END) AS dissatisfied_count,

                SUM(CASE WHEN responsiveness_rating = 'Satisfied' THEN 1 ELSE 0 END +
                    CASE WHEN timeliness_rating = 'Satisfied' THEN 1 ELSE 0 END +
                    CASE WHEN communication_rating = 'Satisfied' THEN 1 ELSE 0 END) AS satisfied_count,

                SUM(CASE WHEN responsiveness_rating = 'Very Satisfied' THEN 1 ELSE 0 END +
                    CASE WHEN timeliness_rating = 'Very Satisfied' THEN 1 ELSE 0 END +
                    CASE WHEN communication_rating = 'Very Satisfied' THEN 1 ELSE 0 END) AS very_satisfied_count,

                COUNT(*) * 3 AS total_ratings_given -- Total number of individual ratings (3 columns * number of surveys)
            ")
            ->whereIn('user_id', $eligibleUsers); // Only include surveys for eligible staff

        // Apply date filtering to the surveys based on their creation date
        $surveyAggregatesQuery->when($startDate, fn($query) => $query->where('created_at', '>=', $startDate))
                              ->when($endDate, fn($query) => $query->where('created_at', '<=', $endDate));

        $surveyAggregates = $surveyAggregatesQuery->groupBy('user_id')
            ->get()
            ->keyBy('user_id');


        // 4. Combine with User data for names and total surveys
        $surveyRatings = User::whereIn('id', $eligibleUsers)
            ->orderBy('name')
            ->get()
            ->map(function ($user) use ($surveyAggregates) {
                $aggregate = $surveyAggregates->get($user->id);

                // If there's no survey data, initialize counts to zero
                if (!$aggregate) {
                    return (object)[
                        'id' => $user->id, // Include user ID in the mapped object
                        'name' => $user->name,
                        'total_surveys' => 0,
                        'very_dissatisfied_count' => 0,
                        'dissatisfied_count' => 0,
                        'satisfied_count' => 0,
                        'very_satisfied_count' => 0,
                    ];
                }

                return (object)[
                    'id' => $user->id, // Include user ID in the mapped object
                    'name' => $user->name,
                    'total_surveys' => $aggregate->total_surveys_completed ?? 0,
                    'very_dissatisfied_count' => $aggregate->very_dissatisfied_count ?? 0,
                    'dissatisfied_count' => $aggregate->dissatisfied_count ?? 0,
                    'satisfied_count' => $aggregate->satisfied_count ?? 0,
                    'very_satisfied_count' => $aggregate->very_satisfied_count ?? 0,
                ];
            });

        // Conditionally filter out users with zero completed surveys
        if (!$includeZeroSurveys) {
            // FIX: Filter based on the 'total_surveys' property that was just added
            // to the custom stdClass object during the map operation above.
            $surveyRatings = $surveyRatings
                ->filter(fn ($user) => $user->total_surveys > 0)
                ->values();
        } else {
            $surveyRatings = $surveyRatings->values(); // Ensure re-indexed collection
        }

        // 5. Generate the PDF
        $pdf = Pdf::loadView('filament.reports.css_report_pdf_template', [
            'surveyRatings' => $surveyRatings,
            'reportStartDate' => $startDate ? $startDate->format('F j, Y') : null,
            'reportEndDate' => $endDate ? $endDate->format('F j, Y') : null,
        ])->setPaper('a4', 'portrait');

        // 6. Stream the PDF for download
        return $pdf->stream('css_report_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }
}
