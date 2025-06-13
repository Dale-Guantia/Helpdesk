<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportController;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// // Show verification notice
// Route::get('/email/verify', function () {
//     return view('auth.verify-email');
// })->middleware(['auth'])->name('verification.notice');

// // Handle email verification
// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();
//     return redirect()->intended('/'); // Or redirect to your Filament panel
// })->middleware(['auth', 'signed'])->name('verification.verify');

// // Resend verification link
// Route::post('/email/verification-notification', function (Request $request) {
//     $request->user()->sendEmailVerificationNotification();

//     return back()->with('message', 'Verification link sent!');
// })->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::get('/', function () {
    // return view('index');
    return redirect()->route('filament.ticketing.auth.login');
})->name('index');



Route::get('/submit_ticket', [TicketController::class, 'create'])->name('ticket_create');
Route::post('/submit_ticket', [TicketController::class, 'store'])->name('ticket_store');
Route::get('/problem_categories/{office}', [TicketController::class, 'getCategories']);
Route::get('/download/report', [ReportController::class, 'report'])->name('download_report');

// Route::get('test', function () {
//    Notification::make()
//        ->title('Saved successfully')
//        ->sendToDatabase(auth()->user());
//        ->broadcast($recipient);
//        dd('success');
//});
