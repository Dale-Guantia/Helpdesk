<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\TicketController;

Route::get('/', function () {
    return view('index');
    // return redirect()->route('filament.ticketing.auth.login');
})->name('index');


Route::get('/submit_ticket', [TicketController::class, 'create'])->name('ticket_create');
Route::post('/submit_ticket', [TicketController::class, 'store'])->name('ticket_store');
Route::get('/problem_categories/{office}', [TicketController::class, 'getCategories']);

