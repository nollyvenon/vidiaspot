<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HowItWorksStep;

class StaticPageController extends Controller
{
    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function help()
    {
        return view('help');
    }

    public function safety()
    {
        return view('safety');
    }

    public function howItWorks()
    {
        $howItWorksSteps = HowItWorksStep::active()->ordered()->get();
        return view('how-it-works', compact('howItWorksSteps'));
    }
}