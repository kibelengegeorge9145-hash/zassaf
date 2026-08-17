<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderByDesc('event_date')->orderByDesc('id')->get();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.form', ['event' => new Event()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Event::create($data);

        return redirect()->route('admin.events.index')
            ->with('success', __('admin.saved'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.form', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validated($request);

        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('success', __('admin.saved'));
    }

    public function toggle(Event $event)
    {
        $event->update(['is_published' => ! $event->is_published]);

        return back()->with('success', __('admin.status_changed'));
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return back()->with('success', __('admin.deleted'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title_en' => ['required', 'string', 'max:255'],
            'title_sw' => ['nullable', 'string', 'max:255'],
            'description_en' => ['required', 'string', 'max:3000'],
            'description_sw' => ['nullable', 'string', 'max:3000'],
            'event_date' => ['required', 'date'],
            'event_time' => ['nullable', 'string', 'max:100'],
            'location_en' => ['nullable', 'string', 'max:255'],
            'location_sw' => ['nullable', 'string', 'max:255'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
