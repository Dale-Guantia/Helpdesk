<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

Route::get('/', function () {
    return redirect()->route('filament.ticketing.auth.login');
});

Route::get('/submit-ticket', [FormController::class, 'Form'])->name('submit_ticket');
