<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\Program;
use App\Models\Registration;
use App\Models\User;
use App\Models\WeekendConvo;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'programs' => Program::count(),
            'events' => Event::count(),
            'convos' => WeekendConvo::count(),
            'books' => Book::count(),
            'registrations' => Registration::count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
            'users' => User::count(),
        ];

        $recentRegistrations = Registration::latest()->limit(5)->get();
        $recentMessages = ContactMessage::latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentRegistrations', 'recentMessages'));
    }
}
