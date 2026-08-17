<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\MembershipPlan;
use App\Models\Program;

class CommunityController extends Controller
{
    public function index()
    {
        $programs = Program::published()->orderBy('sort_order')->get();
        $events = Event::published()->upcoming()->get();
        $plans = MembershipPlan::active()->get();

        return view('pages.community', compact('programs', 'events', 'plans'));
    }
}
