<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\MembershipPlan;
use App\Services\MembershipService;

class MembershipController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = $user->member;

        $plan = $member?->plan ?? MembershipPlan::query()->where('is_active', true)->first();

        $canPayMonthly = $member ? app(MembershipService::class)->canPayMonthly($member) : false;

        return view('member.membership', compact('member', 'plan', 'canPayMonthly'));
    }
}
