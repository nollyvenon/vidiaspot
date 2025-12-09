<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreTemplatesController extends Controller
{
    /**
     * Display a listing of store templates.
     */
    public function index()
    {
        $templates = StoreTemplate::orderBy('sort_order')
                                  ->orderBy('created_at')
                                  ->get();

        return response()->json([
            'success' => true,
            'templates' => $templates
        ]);
    }

    /**
     * Store a newly created store template.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|unique:store_templates,key|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'config' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $template = StoreTemplate::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Store template created successfully',
            'template' => $template
        ], 201);
    }

    /**
     * Display the specified store template.
     */
    public function show($id)
    {
        $template = StoreTemplate::findOrFail($id);

        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    /**
     * Update the specified store template.
     */
    public function update(Request $request, $id)
    {
        $template = StoreTemplate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|unique:store_templates,key,' . $id . '|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'config' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $template->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Store template updated successfully',
            'template' => $template
        ]);
    }

    /**
     * Remove the specified store template.
     */
    public function destroy($id)
    {
        $template = StoreTemplate::findOrFail($id);

        // Check if any stores are using this template before deleting
        if (\App\Models\VendorStore::where('theme', $template->key)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete template as it is currently used by one or more stores'
            ], 400);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Store template deleted successfully'
        ]);
    }

    /**
     * Toggle the active status of a store template.
     */
    public function toggleStatus($id)
    {
        $template = StoreTemplate::findOrFail($id);
        $template->is_active = !$template->is_active;
        $template->save();

        return response()->json([
            'success' => true,
            'message' => 'Template status updated successfully',
            'template' => $template
        ]);
    }

    /**
     * Get all active store templates for vendor selection.
     */
    public function getActiveTemplates()
    {
        $templates = StoreTemplate::where('is_active', true)
                                  ->orderBy('sort_order')
                                  ->orderBy('created_at')
                                  ->get();

        return response()->json([
            'success' => true,
            'templates' => $templates
        ]);
    }
}
