<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\MembershipService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = $user->member;

        $recentPayments = $member
            ? $member->payments()->latest()->limit(5)->get()
            : collect();

        $canPayMonthly = $member ? app(MembershipService::class)->canPayMonthly($member) : false;

        $libraryBooks = $user->bookPurchases()
            ->with('book')
            ->latest('purchased_at')
            ->get()
            ->pluck('book')
            ->filter()
            ->values();

        return view('member.dashboard', compact('member', 'recentPayments', 'canPayMonthly', 'libraryBooks'));
    }
}
