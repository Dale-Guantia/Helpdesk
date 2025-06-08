<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    public function report()
    {
        $userActivities = User::with(['department', 'office'])->withCount('resolvedTickets')->get();

        // Get all divisions in department 1
        $divisions = Office::where('department_id', 1)->get();

        // Preload tickets for all users belonging to those offices
        $tickets = Ticket::with(['problemCategory', 'user.office'])
            ->whereHas('user.office', fn($q) => $q->where('department_id', 1))
            ->get();

        // Build structured array
        $divisionsData = $divisions->map(function ($division) use ($tickets) {
            // Filter tickets for this division
            $divisionTickets = $tickets->filter(function ($ticket) use ($division) {
                return optional($ticket->user->office)->id === $division->id;
            });

            // Group by problem category
            $issueStats = $divisionTickets
                ->groupBy('problem_category_id')
                ->map(function ($ticketsInCategory) {
                    return [
                        'category_name' => optional($ticketsInCategory->first()->problemCategory)->category_name ?? 'N/A',
                        'total_tickets' => $ticketsInCategory->count(),
                    ];
                })->values();

            return [
                'division_name' => $division->office_name,
                'issueName_and_totalTickets' => $issueStats,
            ];
        });

        // Send to view
        $pdf = Pdf::loadView('filament.reports.report', [
            'userActivities' => $userActivities,
            'divisionsData' => $divisionsData,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('helpdesk-ticketing-report.pdf');
    }

}
