<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Priority;
use App\Models\ProblemCategory;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{

    public function create()
    {
        $offices = Office::all();
        $problem_categories = ProblemCategory::all();
        $priorities = Priority::all();
        return view('form', compact( 'problem_categories', 'priorities', 'offices'));
    }

    public function getCategories($officeId)
    {
        $categories = ProblemCategory::where('office_id', $officeId)->get();
        return response()->json($categories);
    }


    public function store(Request $request)
    {
        $request->validate([
            'guest_firstName' => 'required|string|max:255',
            'guest_lastName' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'office_id' => 'required|exists:offices,id',
            'priority_id' => 'required|exists:priorities,id',
            'problem_category_id' => 'required|exists:problem_categories,id',
        ]);

        Ticket::create([
            'guest_firstName' => $request->input('guest_firstName'),
            'guest_middleName' => $request->input('guest_middleName'),
            'guest_lastName' => $request->input('guest_lastName'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'office_id' => $request->input('office_id'),
            'priority_id' => $request->input('priority_id'),
            'problem_category_id' => $request->input('problem_category_id'),
            // 'attachment' => $request->file('attachment') ? $request->file('attachment')->store('attachments', 'public') : null,
        ]);

        return redirect()->route('index')->with('success', 'Ticket submitted successfully!');
    }
}
