<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display settings management page.
     */
    public function index(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Setting::query();

        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('key', 'LIKE', '%' . $request->search . '%');
        }

        $settings = $query->orderBy('section')->orderBy('order')->orderBy('name')->paginate(25);

        $sections = Setting::distinct('section')->pluck('section');

        return $this->adminView('admin.settings.index', [
            'settings' => $settings,
            'sections' => $sections,
        ]);
    }

    /**
     * Store a new setting.
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'name' => 'required|string|max:255',
            'value' => 'required',
            'type' => 'required|in:string,text,boolean,integer,array,json',
            'section' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $setting = Setting::create([
            'key' => $request->key,
            'name' => $request->name,
            'value' => $request->value,
            'type' => $request->type,
            'section' => $request->section,
            'description' => $request->description,
            'is_public' => $request->is_public ?? false,
            'is_active' => $request->is_active ?? true,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Setting created successfully',
            'setting' => $setting,
        ], 201);
    }

    /**
     * Update a setting.
     */
    public function update(Request $request, Setting $setting): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required',
            'type' => 'required|in:string,text,boolean,integer,array,json',
            'section' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $setting->update([
            'name' => $request->name,
            'value' => $request->value,
            'type' => $request->type,
            'section' => $request->section,
            'description' => $request->description,
            'is_public' => $request->is_public ?? false,
            'is_active' => $request->is_active ?? true,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Setting updated successfully',
            'setting' => $setting->refresh(),
        ]);
    }

    /**
     * Delete a setting.
     */
    public function destroy(Setting $setting): JsonResponse
    {
        $this->checkAdminAccess();

        $setting->delete();

        return response()->json([
            'message' => 'Setting deleted successfully',
        ]);
    }

    /**
     * Update mobile app configuration settings.
     */
    public function updateMobileConfig(Request $request): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_logo' => 'nullable|string|max:500',
            'app_icon' => 'nullable|string|max:500',
            'primary_color' => 'nullable|string|max:7', // Hex color
            'secondary_color' => 'nullable|string|max:7', // Hex color
            'accent_color' => 'nullable|string|max:7', // Hex color
        ]);

        $settings = [
            'app_name' => $request->app_name,
            'app_logo' => $request->app_logo,
            'app_icon' => $request->app_icon,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'accent_color' => $request->accent_color,
        ];

        foreach ($settings as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(
                    ['key' => "mobile.{$key}"],
                    [
                        'name' => ucfirst(str_replace('_', ' ', $key)),
                        'value' => $value,
                        'type' => 'string',
                        'section' => 'mobile',
                        'is_public' => true,
                        'is_active' => true,
                        'updated_by' => auth()->id(),
                    ]
                );
            }
        }

        return response()->json([
            'message' => 'Mobile configuration updated successfully',
        ]);
    }

    /**
     * Get mobile configuration settings.
     */
    public function getMobileConfig(): JsonResponse
    {
        $this->checkAdminAccess();

        $mobileSettings = Setting::where('section', 'mobile')->get();

        return response()->json([
            'mobile_config' => $mobileSettings,
        ]);
    }
}