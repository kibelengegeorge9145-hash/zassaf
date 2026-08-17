<?php

namespace App\Http\Controllers;

class ContactPageController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }
}
