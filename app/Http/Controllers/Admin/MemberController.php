<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\Payment;
use App\Services\MembershipService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with('user')->latest();

        if ($request->filled('status')) {
            $query->status($request->query('status'));
        }

        if ($request->filled('search')) {
            $term = trim($request->query('search'));
            $query->where(function ($q) use ($term) {
                $q->where('membership_number', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            });
        }

        $members = $query->paginate(20)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    public function show(Member $member)
    {
        $member->load('user', 'plan', 'payments');

        return view('admin.members.show', compact('member'));
    }

    public function updateStatus(Request $request, Member $member)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in([Member::STATUS_ACTIVE, Member::STATUS_SUSPENDED, Member::STATUS_CANCELLED])],
        ]);

        $old = $member->status;
        $member->update(['status' => $validated['status']]);

        AuditLog::log(
            AuditLog::ACTION_MEMBER_STATUS_CHANGED,
            __('admin.audit.member_status_changed', [
                'actor' => $request->user()->name,
                'name' => $member->user?->name ?? $member->membership_number,
                'from' => __('membership.statuses.member_'.$old),
                'to' => __('membership.statuses.member_'.$validated['status']),
            ]),
            $member
        );

        return back()->with('success', __('admin.members.status_changed'));
    }

    public function markExpired(Request $request, Member $member)
    {
        $old = $member->status;
        $member->update(['status' => Member::STATUS_EXPIRED]);

        AuditLog::log(
            AuditLog::ACTION_MEMBER_STATUS_CHANGED,
            __('admin.audit.member_status_changed', [
                'actor' => $request->user()->name,
                'name' => $member->user?->name ?? $member->membership_number,
                'from' => __('membership.statuses.member_'.$old),
                'to' => __('membership.statuses.member_expired'),
            ]),
            $member
        );

        return back()->with('success', __('admin.members.status_changed'));
    }

    public function recordPayment(Request $request, Member $member)
    {
        $validated = $request->validate([
            'payment_type' => ['required', 'string', Rule::in([Payment::TYPE_REGISTRATION, Payment::TYPE_MONTHLY])],
            'payment_method' => ['required', 'string', Rule::in(array_keys(Payment::METHODS))],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment = Payment::create([
            'member_id' => $member->id,
            'transaction_reference' => app(\App\Services\Payments\PaymentTransactionService::class)->generateTransactionReference(),
            'amount' => $validated['amount'],
            'payment_type' => $validated['payment_type'],
            'payment_method' => $validated['payment_method'],
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
            'failure_reason' => trim((string) $validated['note']),
        ]);

        app(MembershipService::class)
            ->{$validated['payment_type'] === Payment::TYPE_REGISTRATION ? 'activateForRegistration' : 'extendForMonthly'}($member, $payment);

        AuditLog::log(
            AuditLog::ACTION_MANUAL_PAYMENT_RECORDED,
            __('admin.audit.manual_payment_recorded', [
                'actor' => $request->user()->name,
                'name' => $member->user?->name ?? $member->membership_number,
                'amount' => number_format((float) $validated['amount'], 0),
            ]),
            $payment
        );

        return back()->with('success', __('admin.members.payment_recorded'));
    }
}
