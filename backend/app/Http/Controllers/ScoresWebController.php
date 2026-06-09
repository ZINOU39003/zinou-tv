<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ScoresWebController extends Controller
{
    public function index(): View
    {
        return view('scores.index');
    }
}
