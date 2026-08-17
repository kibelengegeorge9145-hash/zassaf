<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $registrations = $query->get();

        return view('admin.registrations.index', compact('registrations'));
    }

    public function show(Registration $registration)
    {
        return view('admin.registrations.show', compact('registration'));
    }

    public function updateStatus(Request $request, Registration $registration)
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(Registration::STATUSES)],
        ]);

        $registration->update($data);

        return back()->with('success', __('admin.status_changed'));
    }

    public function destroy(Registration $registration)
    {
        $registration->delete();

        return back()->with('success', __('admin.deleted'));
    }
}
