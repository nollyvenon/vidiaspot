<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $settings = PaymentSetting::orderBy('sort_order')->get();
        return view('admin.payment-settings.index', compact('settings'));
    }

    public function create()
    {
        return view('admin.payment-settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'feature_key' => 'required|unique:payment_settings,feature_key',
            'feature_name' => 'required',
            'feature_type' => 'required|in:payment_method,service,integration',
            'is_enabled' => 'boolean',
            'available_countries' => 'array',
            'description' => 'nullable|string',
            'sort_order' => 'integer',
        ]);

        PaymentSetting::create([
            'feature_key' => $request->feature_key,
            'feature_name' => $request->feature_name,
            'feature_type' => $request->feature_type,
            'is_enabled' => $request->is_enabled ?? false,
            'available_countries' => $request->available_countries ?? [],
            'configuration' => $request->configuration ?? [],
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.payment-settings.index')->with('success', 'Payment setting created successfully.');
    }

    public function edit($id)
    {
        $setting = PaymentSetting::findOrFail($id);
        return view('admin.payment-settings.edit', compact('setting'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'feature_key' => 'required|unique:payment_settings,feature_key,'.$id,
            'feature_name' => 'required',
            'feature_type' => 'required|in:payment_method,service,integration',
            'is_enabled' => 'boolean',
            'available_countries' => 'array',
            'description' => 'nullable|string',
            'sort_order' => 'integer',
        ]);

        $setting = PaymentSetting::findOrFail($id);
        $setting->update([
            'feature_key' => $request->feature_key,
            'feature_name' => $request->feature_name,
            'feature_type' => $request->feature_type,
            'is_enabled' => $request->is_enabled ?? false,
            'available_countries' => $request->available_countries ?? [],
            'configuration' => $request->configuration ?? [],
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.payment-settings.index')->with('success', 'Payment setting updated successfully.');
    }

    public function toggleStatus($id)
    {
        $setting = PaymentSetting::findOrFail($id);
        $newStatus = !$setting->is_enabled;
        $setting->update(['is_enabled' => $newStatus]);

        return response()->json([
            'success' => true,
            'is_enabled' => $setting->is_enabled,
            'message' => 'Status updated successfully.'
        ]);
    }
}