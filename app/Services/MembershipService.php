<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\Payment;
use Illuminate\Support\Carbon;

/**
 * Membership lifecycle logic: activation, monthly extension and eligibility.
 */
class MembershipService
{
    public function activateForRegistration(Member $member, Payment $payment): Member
    {
        $member->plan_id = MembershipPlan::query()
            ->where('status', MembershipPlan::STATUS_ACTIVE)
            ->orWhere('is_active', true)
            ->first()?->id ?? $member->plan_id;

        if (blank($member->membership_number)) {
            $member->membership_number = $this->nextMembershipNumber($member);
        }

        $member->status = Member::STATUS_ACTIVE;
        $member->joined_at = $payment->paid_at ?? now();
        $member->expires_at = $this->expiryFor($payment);
        $member->save();

        return $member;
    }

    public function extendForMonthly(Member $member, Payment $payment): Member
    {
        $member->status = Member::STATUS_ACTIVE;
        $member->expires_at = $this->expiryFor($payment);
        $member->save();

        return $member;
    }

    /**
     * Membership period runs to the end of the paid month.
     */
    public function expiryFor(Payment $payment): Carbon
    {
        $paidAt = $payment->paid_at ?? now();

        return Carbon::parse($paidAt)->endOfMonth()->setTime(23, 59, 59);
    }

    public function canPayMonthly(Member $member): bool
    {
        if (! $member->canRenew()) {
            return false;
        }

        $duplicate = $member->payments()
            ->where('payment_type', Payment::TYPE_MONTHLY)
            ->where('status', Payment::STATUS_PAID)
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->exists();

        return ! $duplicate;
    }

    public function nextMembershipNumber(Member $member): string
    {
        $seed = $member->id ?? ((int) Member::query()->max('id')) + 1;

        return 'ZE-'.str_pad((string) $seed, 6, '0', STR_PAD_LEFT);
    }
}
