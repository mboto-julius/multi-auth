<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function home()
    {
        return view('front.home');
    }

    public function about()
    {
        return view('front.about');
    }
}
