<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['member.user', 'user'])->latest();

        if ($request->filled('type')) {
            $query->where('payment_type', $request->query('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $term = trim($request->query('search'));
            $query->where(function ($q) use ($term) {
                $q->where('transaction_reference', 'like', "%{$term}%")
                    ->orWhere('provider_reference', 'like', "%{$term}%")
                    ->orWhereHas('member.user', fn ($u) => $u->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        $totals = [
            'total' => Payment::count(),
            'paid' => Payment::where('status', Payment::STATUS_PAID)->count(),
            'pending' => Payment::where('status', Payment::STATUS_PENDING)->count(),
            'failed' => Payment::where('status', Payment::STATUS_FAILED)->count(),
            'revenue' => Payment::where('status', Payment::STATUS_PAID)->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'totals'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['member.user', 'user', 'book']);

        return view('admin.payments.show', compact('payment'));
    }
}
