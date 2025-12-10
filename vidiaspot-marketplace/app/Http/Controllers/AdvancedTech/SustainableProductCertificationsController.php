<?php

namespace App\Http\Controllers;

use App\Services\SustainableProductCertificationsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SustainableProductCertificationsController extends Controller
{
    private SustainableProductCertificationsService $certificationsService;

    public function __construct()
    {
        $this->certificationsService = new SustainableProductCertificationsService();
    }

    /**
     * Get all available certifications.
     */
    public function getCertifications()
    {
        $certifications = $this->certificationsService->getCertifications();

        return response()->json([
            'certifications' => $certifications,
            'message' => 'Sustainable product certifications retrieved successfully'
        ]);
    }

    /**
     * Get a specific certification by ID.
     */
    public function getCertification(Request $request, string $certId)
    {
        $certification = $this->certificationsService->getCertification($certId);

        if (!$certification) {
            return response()->json([
                'error' => 'Certification not found'
            ], 404);
        }

        return response()->json([
            'certification' => $certification,
            'message' => 'Certification retrieved successfully'
        ]);
    }

    /**
     * Get certification levels for a specific certification.
     */
    public function getCertificationLevels(Request $request, string $certId)
    {
        $levels = $this->certificationsService->getCertificationLevels($certId);

        return response()->json([
            'levels' => $levels,
            'certification_id' => $certId,
            'message' => 'Certification levels retrieved successfully'
        ]);
    }

    /**
     * Validate a product for certification.
     */
    public function validateProductForCertification(Request $request, string $certId)
    {
        $request->validate([
            'product_name' => 'required|string',
            'category' => 'required|string',
            'company_name' => 'required|string',
            'description' => 'string',
            'fair_trade_practices' => 'boolean',
            'organic_ingredients' => 'numeric|min:0|max:100',
            'cruelty_free' => 'boolean',
            'fsc_materials' => 'boolean',
            'energy_efficiency_rating' => 'numeric|min:0|max:1',
            'supply_chain_transparency' => 'boolean',
            'organic_farming_practices' => 'boolean',
            'chain_of_custody' => 'boolean',
            'material_health' => 'boolean',
            'renewable_energy_usage' => 'boolean',
        ]);

        $validation = $this->certificationsService->validateProductForCertification($certId, $request->all());

        return response()->json([
            'validation' => $validation,
            'message' => 'Product validation completed'
        ]);
    }

    /**
     * Apply for a certification.
     */
    public function applyForCertification(Request $request, string $certId)
    {
        $request->validate([
            'product_name' => 'required|string',
            'category' => 'required|string',
            'company_name' => 'required|string',
            'description' => 'string',
            'contact_name' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'business_address' => 'required|array',
            'fair_trade_practices' => 'boolean',
            'organic_ingredients' => 'numeric|min:0|max:100',
            'cruelty_free' => 'boolean',
            'fsc_materials' => 'boolean',
            'energy_efficiency_rating' => 'numeric|min:0|max:1',
        ]);

        $userId = Auth::id();
        $application = $this->certificationsService->applyForCertification($certId, $request->all(), $userId);

        if (!$application['success']) {
            return response()->json([
                'success' => false,
                'validation' => $application['validation'],
                'message' => $application['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'application' => $application['application'],
            'validation' => $application['validation'],
            'message' => $application['message']
        ]);
    }

    /**
     * Get user's certification applications.
     */
    public function getUserApplications()
    {
        $userId = Auth::id();
        $applications = $this->certificationsService->getUserApplications($userId);

        return response()->json([
            'applications' => $applications,
            'message' => 'User certification applications retrieved successfully'
        ]);
    }

    /**
     * Get certified products for a specific certification.
     */
    public function getCertifiedProducts(Request $request, string $certId)
    {
        $request->validate([
            'category' => 'string',
            'company' => 'string',
            'sort_by' => 'string|in:name,date,category',
        ]);

        $filters = [
            'category' => $request->category,
            'company' => $request->company,
            'sort_by' => $request->sort_by,
        ];

        $products = $this->certificationsService->getCertifiedProducts($certId, array_filter($filters));

        return response()->json([
            'products' => $products,
            'message' => 'Certified products retrieved successfully'
        ]);
    }

    /**
     * Verify a certification.
     */
    public function verifyCertification(Request $request)
    {
        $request->validate([
            'certification_id' => 'required|string',
            'product_id' => 'required|string',
        ]);

        $verification = $this->certificationsService->verifyCertification($request->certification_id, $request->product_id);

        if (!$verification['valid']) {
            return response()->json([
                'valid' => false,
                'error' => $verification['error']
            ], 400);
        }

        return response()->json([
            'verification' => $verification,
            'message' => 'Certification verified successfully'
        ]);
    }

    /**
     * Get sustainability impact of certified products.
     */
    public function getSustainabilityImpact(Request $request)
    {
        $request->validate([
            'certified_products' => 'required|array|min:1',
            'certified_products.*.id' => 'required|string',
            'certified_products.*.certification' => 'required|string',
        ]);

        $impact = $this->certificationsService->getSustainabilityImpact($request->certified_products);

        return response()->json([
            'impact' => $impact,
            'message' => 'Sustainability impact calculated successfully'
        ]);
    }

    /**
     * Get certification guide for a product category.
     */
    public function getCertificationGuide(Request $request, string $category)
    {
        $guide = $this->certificationsService->getCertificationGuide($category);

        return response()->json([
            'guide' => $guide,
            'message' => 'Certification guide retrieved successfully'
        ]);
    }
}