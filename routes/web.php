<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use Filament\Notifications\Notification;

Route::get('/', function () {
    return view('index');
    // return redirect()->route('filament.ticketing.auth.login');
})->name('index');



Route::get('/submit_ticket', [TicketController::class, 'create'])->name('ticket_create');
Route::post('/submit_ticket', [TicketController::class, 'store'])->name('ticket_store');
Route::get('/problem_categories/{office}', [TicketController::class, 'getCategories']);



// Route::get('test', function () {
//    Notification::make()
//        ->title('Saved successfully')
//        ->sendToDatabase(auth()->user());
//        ->broadcast($recipient);
//        dd('success');
//});
