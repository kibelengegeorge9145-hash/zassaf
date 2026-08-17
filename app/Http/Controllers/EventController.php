<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::published()->orderBy('event_date', 'asc')->get();

        return view('pages.events', compact('events'));
    }
}
