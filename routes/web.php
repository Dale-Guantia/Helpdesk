<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

Route::get('/', function () {
    return view('index');
});

Route::get('/submit-ticket', [FormController::class, 'Form'])->name('submit_ticket');
