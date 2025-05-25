<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

Route::get('/', function () {
    return view('index');
    // return redirect()->route('filament.ticketing.auth.login');
})->name('index');

// Route::get('/', [FormController::class, 'Index'])->name('index');
Route::get('/submit_ticket', [FormController::class, 'Form'])->name('submit_ticket');
