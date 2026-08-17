<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Payment;
use App\Notifications\MembershipExpiring;
use Illuminate\Console\Command;

class ExpireMemberships extends Command
{
    protected $signature = 'membership:expire';

    protected $description = 'Mark expired memberships and send renewal reminders.';

    public function handle(): int
    {
        $expired = Member::query()
            ->where('status', Member::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => Member::STATUS_EXPIRED]);

        $this->info("Marked {$expired} membership(s) as expired.");

        $reminderDate = now()->addDays(3)->startOfDay();

        Member::query()
            ->where('status', Member::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', $reminderDate->toDateString())
            ->with('user')
            ->get()
            ->each(function (Member $member) {
                $coveredThisMonth = $member->payments()
                    ->where('payment_type', Payment::TYPE_MONTHLY)
                    ->where('status', Payment::STATUS_PAID)
                    ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->exists();

                if (! $coveredThisMonth && $member->user) {
                    $member->user->notify(new MembershipExpiring($member));
                    $this->info("Sent renewal reminder to {$member->user->email}.");
                }
            });

        return self::SUCCESS;
    }
}
