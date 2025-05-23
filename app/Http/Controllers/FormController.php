<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    public function Form()
    {
        return view('form');
    }

    public function Index()
    {
        return view('index');
    }
}
