<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function privacy()
    {
        return view('pages.legal', ['type' => 'privacy']);
    }

    public function terms()
    {
        return view('pages.legal', ['type' => 'terms']);
    }
}
