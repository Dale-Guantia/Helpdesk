<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function report()
    {
        // Get user activity (already correct)
        $userActivities = User::with(['department', 'office'])
            ->where('department_id', 1)
            ->where('role', '!=', 4)
            ->get();

        // Get all average resolve times per problem category in one query
        $avgResolveTimes = Ticket::select([
                'problem_category_id',
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, tickets.created_at, tickets.resolved_at)) as avg_resolve_seconds')
            ])
            ->whereNotNull('resolved_at')
            ->groupBy('problem_category_id')
            ->pluck('avg_resolve_seconds', 'problem_category_id'); // returns [id => seconds]

        // Get divisions and their problem categories
        $divisions = Office::where('department_id', 1)
            ->with(['problemCategories' => function ($query) {
                $query->withCount('tickets');
            }])
            ->get();

        // Attach avg_resolve_time to each problem category
        foreach ($divisions as $division) {
            foreach ($division->problemCategories as $category) {
                $seconds = $avgResolveTimes[$category->id] ?? null;

                if ($seconds) {
                    $hours = floor($seconds / 3600);
                    $minutes = floor(($seconds % 3600) / 60);
                    $category->average_resolve_time = "{$hours}h {$minutes}m";
                } else {
                    $category->average_resolve_time = 'N/A';
                }
            }
        }

        // Return PDF view
        $pdf = Pdf::loadView('filament.reports.report_pdf_template', [
            'userActivities' => $userActivities,
            'divisions' => $divisions,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('report.pdf');
    }
}
