<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\User;
use App\Models\Survey;
use App\Models\ProblemCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SurveyController extends Controller
{
    public function showForm()
    {
        $divisions = Office::where('department_id', 1)->get();
        $staffs = User::query()->where('role', User::ROLE_STAFF)
        ->orWhere(function ($query) {
            $query->where('role', User::ROLE_DIVISION_HEAD)
                ->where('department_id', 1);
        })->get();
        $services = ProblemCategory::all();
        return view('survey_form', compact('divisions', 'staffs', 'services'));
    }

    public function submitForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'submission_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'problem_category_id' => 'required|exists:problem_categories,id',
            'responsiveness_rating' => 'required|string',
            'timeliness_rating' => 'required|string',
            'communication_rating' => 'required|string',
            'suggestions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Survey::create([
            'user_id' => $request->input('user_id'),
            'problem_category_id' => $request->input('problem_category_id'),
            'responsiveness_rating' => $request->input('responsiveness_rating'),
            'timeliness_rating' => $request->input('timeliness_rating'),
            'communication_rating' => $request->input('communication_rating'),
            'suggestions' => $request->input('suggestions'),
        ]);

        return redirect()->to(url()->current() . '?thank_you=1')->with('success', 'Thank you for your feedback!');
    }
}
