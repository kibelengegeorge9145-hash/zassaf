<?php

namespace App\Http\Controllers;

use App\Models\WeekendConvo;

class WeekendConvoController extends Controller
{
    public function index()
    {
        $upcoming = WeekendConvo::published()->upcoming()->get();
        $past = WeekendConvo::published()->past()->limit(8)->get();

        return view('pages.weekend-convo', compact('upcoming', 'past'));
    }
}
