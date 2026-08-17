<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeekendConvo;
use Illuminate\Http\Request;

class WeekendConvoController extends Controller
{
    public function index()
    {
        $convos = WeekendConvo::orderByDesc('event_date')->orderByDesc('id')->get();

        return view('admin.convos.index', compact('convos'));
    }

    public function create()
    {
        return view('admin.convos.form', ['convo' => new WeekendConvo()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        WeekendConvo::create($data);

        return redirect()->route('admin.convos.index')
            ->with('success', __('admin.saved'));
    }

    public function edit(WeekendConvo $convo)
    {
        return view('admin.convos.form', compact('convo'));
    }

    public function update(Request $request, WeekendConvo $convo)
    {
        $data = $this->validated($request);

        $convo->update($data);

        return redirect()->route('admin.convos.index')
            ->with('success', __('admin.saved'));
    }

    public function toggle(WeekendConvo $convo)
    {
        $convo->update(['is_published' => ! $convo->is_published]);

        return back()->with('success', __('admin.status_changed'));
    }

    public function destroy(WeekendConvo $convo)
    {
        $convo->delete();

        return back()->with('success', __('admin.deleted'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title_en' => ['required', 'string', 'max:255'],
            'title_sw' => ['nullable', 'string', 'max:255'],
            'description_en' => ['required', 'string', 'max:3000'],
            'description_sw' => ['nullable', 'string', 'max:3000'],
            'topics_en' => ['nullable', 'string', 'max:2000'],
            'topics_sw' => ['nullable', 'string', 'max:2000'],
            'event_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'string', 'max:100'],
            'platform_en' => ['nullable', 'string', 'max:255'],
            'platform_sw' => ['nullable', 'string', 'max:255'],
            'speaker_en' => ['nullable', 'string', 'max:255'],
            'speaker_sw' => ['nullable', 'string', 'max:255'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
