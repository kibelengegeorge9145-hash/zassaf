<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Event;
use App\Models\MembershipPlan;
use App\Models\Program;
use App\Models\WeekendConvo;

class HomeController extends Controller
{
    public function index()
    {
        $programs = Program::published()
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        $upcomingConvo = WeekendConvo::published()->upcoming()->first();

        $featuredBook = Book::where('is_featured', true)->first()
            ?? Book::published()->latest('id')->first();

        $upcomingEvents = Event::published()->upcoming()->limit(3)->get();

        $plans = MembershipPlan::active()->get();

        return view('pages.home', compact('programs', 'upcomingConvo', 'featuredBook', 'upcomingEvents', 'plans'));
    }
}
