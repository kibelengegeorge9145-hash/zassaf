<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::orderBy('sort_order')->get();

        return view('admin.programs.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.programs.form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Program::create($data);

        return redirect()->route('admin.programs.index')
            ->with('success', __('admin.saved'));
    }

    public function edit(Program $program)
    {
        return view('admin.programs.form', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $data = $this->validated($request);

        $program->update($data);

        return redirect()->route('admin.programs.index')
            ->with('success', __('admin.saved'));
    }

    public function toggle(Program $program)
    {
        $program->update(['is_published' => ! $program->is_published]);

        return back()->with('success', __('admin.status_changed'));
    }

    public function destroy(Program $program)
    {
        $program->delete();

        return back()->with('success', __('admin.deleted'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title_en' => ['required', 'string', 'max:255'],
            'title_sw' => ['nullable', 'string', 'max:255'],
            'description_en' => ['required', 'string', 'max:2000'],
            'description_sw' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['icon'] = $data['icon'] ?: 'sparkles';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
