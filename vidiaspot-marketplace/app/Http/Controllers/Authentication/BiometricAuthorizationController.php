<?php

namespace App\Http\Controllers;

use App\Services\BiometricAuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiometricAuthorizationController extends Controller
{
    private BiometricAuthorizationService $biometricService;

    public function __construct()
    {
        $this->biometricService = new BiometricAuthorizationService();
    }

    /**
     * Register a new biometric template for the authenticated user.
     */
    public function registerTemplate(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'biometric_data' => 'required|string',
            'template_type' => 'required|in:fingerprint,face,iris,voice',
        ]);

        $result = $this->biometricService->registerBiometricTemplate(
            $user,
            $request->biometric_data,
            $request->template_type
        );

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    /**
     * Verify biometric data for the authenticated user.
     */
    public function verifyBiometric(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'biometric_data' => 'required|string',
            'template_type' => 'required|in:fingerprint,face,iris,voice',
        ]);

        $result = $this->biometricService->verifyBiometricData(
            $user,
            $request->biometric_data,
            $request->template_type
        );

        if ($result['verified']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    /**
     * Authorize a transaction using biometric verification.
     */
    public function authorizeTransaction(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'biometric_data' => 'required|string',
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric',
            'type' => 'required|string',
        ]);

        $transactionData = [
            'id' => $request->transaction_id,
            'amount' => $request->amount,
            'type' => $request->type,
        ];

        $result = $this->biometricService->authorizeTransaction(
            $user,
            $request->biometric_data,
            $transactionData
        );

        if ($result['authorized']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    /**
     * Get all biometric templates for the authenticated user.
     */
    public function getUserTemplates()
    {
        $user = Auth::user();
        $templates = $this->biometricService->getUserBiometricTemplates($user);

        return response()->json([
            'templates' => array_values($templates),
            'count' => count($templates),
        ]);
    }

    /**
     * Update a biometric template's status.
     */
    public function updateTemplateStatus(Request $request, string $templateId)
    {
        $user = Auth::user();
        
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $success = $this->biometricService->updateTemplateStatus(
            $user,
            $templateId,
            $request->is_active
        );

        if ($success) {
            return response()->json([
                'message' => 'Template status updated successfully',
                'template_id' => $templateId,
                'is_active' => $request->is_active,
            ]);
        }

        return response()->json(['error' => 'Template not found'], 404);
    }

    /**
     * Delete a biometric template.
     */
    public function deleteTemplate(string $templateId)
    {
        $user = Auth::user();
        
        $success = $this->biometricService->deleteTemplate($user, $templateId);

        if ($success) {
            return response()->json([
                'message' => 'Biometric template deleted successfully',
                'template_id' => $templateId,
            ]);
        }

        return response()->json(['error' => 'Template not found'], 404);
    }

    /**
     * Get biometric verification history for the authenticated user.
     */
    public function getVerificationHistory()
    {
        $user = Auth::user();
        $cacheKey = "biometric_verifications_{$user->id}";
        $verifications = \Cache::get($cacheKey, []);

        return response()->json([
            'verifications' => $verifications,
            'count' => count($verifications),
        ]);
    }
}