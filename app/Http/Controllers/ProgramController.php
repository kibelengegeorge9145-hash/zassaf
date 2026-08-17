<?php

namespace App\Http\Controllers;

use App\Models\Program;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::published()
            ->orderBy('sort_order')
            ->get();

        return view('pages.programs', compact('programs'));
    }
}
