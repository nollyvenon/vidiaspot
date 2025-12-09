<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HowItWorksStep;
use Illuminate\Http\Request;

class HowItWorksController extends Controller
{
    public function index()
    {
        $steps = HowItWorksStep::orderBy('step_order')->get();
        return view('admin.how-it-works.index', compact('steps'));
    }

    public function create()
    {
        return view('admin.how-it-works.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon_class' => 'required|string|max:255',
            'step_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        HowItWorksStep::create([
            'title' => $request->title,
            'description' => $request->description,
            'icon_class' => $request->icon_class,
            'step_order' => $request->step_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.how-it-works.index')->with('success', 'How It Works step created successfully.');
    }

    public function edit(HowItWorksStep $step)
    {
        return view('admin.how-it-works.edit', compact('step'));
    }

    public function update(Request $request, HowItWorksStep $step)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon_class' => 'required|string|max:255',
            'step_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $step->update([
            'title' => $request->title,
            'description' => $request->description,
            'icon_class' => $request->icon_class,
            'step_order' => $request->step_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.how-it-works.index')->with('success', 'How It Works step updated successfully.');
    }

    public function destroy(HowItWorksStep $step)
    {
        $step->delete();

        return redirect()->route('admin.how-it-works.index')->with('success', 'How It Works step deleted successfully.');
    }
}