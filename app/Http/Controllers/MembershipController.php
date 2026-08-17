<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\User;
use App\Services\Payments\Contracts\PaymentServiceInterface;
use App\Services\Payments\Exceptions\PaymentException;
use App\Services\Payments\PaymentTransactionService;
use App\Support\MembershipConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MembershipController extends Controller
{
    public function index()
    {
        $config = new MembershipConfig();
        $plans = MembershipPlan::active()->get();

        return view('pages.membership', compact('config', 'plans'));
    }

    public function create()
    {
        if (! MembershipConfig::registrationOpen()) {
            return redirect()->route('membership')
                ->with('error', __('membership.unavailable'));
        }

        return view('pages.membership-register', ['config' => new MembershipConfig()]);
    }

    public function store(Request $request)
    {
        if (! MembershipConfig::registrationOpen()) {
            return back()->with('error', __('membership.unavailable'));
        }

        if (! MembershipConfig::paymentEnabled()) {
            return back()->with('error', __('membership.payments_disabled'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'location' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user?->isMember()) {
            Auth::login($user);

            return redirect()->route('member.dashboard')
                ->with('error', __('membership.already_member', ['email' => $validated['email']]));
        }

        if ($user) {
            $user->forceFill([
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'location' => $validated['location'] ?? null,
            ])->save();
        } else {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'location' => $validated['location'] ?? null,
                'password' => Hash::make(Str::random(32)),
                'role' => User::ROLE_MEMBER,
            ]);
        }

        $member = Member::create([
            'user_id' => $user->id,
            'status' => Member::STATUS_PENDING,
        ]);

        Auth::login($user);

        try {
            $payment = app(PaymentTransactionService::class)
                ->createRegistrationPayment($member, $validated['payment_method']);

            $initiation = app(PaymentServiceInterface::class)->initiate($payment, $validated['payment_method']);
        } catch (PaymentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->away($initiation->checkoutUrl);
    }
}
