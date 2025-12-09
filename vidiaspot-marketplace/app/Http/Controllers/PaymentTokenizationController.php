<?php

namespace App\Http\Controllers;

use App\Services\PaymentTokenizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentTokenizationController extends Controller
{
    private PaymentTokenizationService $tokenizationService;

    public function __construct()
    {
        $this->tokenizationService = new PaymentTokenizationService();
    }

    /**
     * Create a payment token from sensitive payment data.
     */
    public function createToken(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string',
            'cvv' => 'required|string',
            'expiry_date' => 'required|string',
            'cardholder_name' => 'required|string',
        ]);

        $token = $this->tokenizationService->tokenizePaymentData([
            'card_number' => $request->card_number,
            'cvv' => $request->cvv,
            'expiry_date' => $request->expiry_date,
            'cardholder_name' => $request->cardholder_name,
        ]);

        return response()->json([
            'token' => $token,
            'message' => 'Payment token created successfully',
            'expires_at' => now()->addDays(365) // Token expires in 1 year
        ]);
    }

    /**
     * Create a single-use payment token.
     */
    public function createSingleUseToken(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string',
            'cvv' => 'required|string',
            'expiry_date' => 'required|string',
            'cardholder_name' => 'required|string',
        ]);

        $token = $this->tokenizationService->tokenizeForSingleUse([
            'card_number' => $request->card_number,
            'cvv' => $request->cvv,
            'expiry_date' => $request->expiry_date,
            'cardholder_name' => $request->cardholder_name,
        ]);

        return response()->json([
            'token' => $token,
            'message' => 'Single-use payment token created successfully',
            'expires_at' => now()->addMinutes(10) // Token expires in 10 minutes
        ]);
    }

    /**
     * Retrieve payment data using a token.
     */
    public function retrievePaymentData(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        if (!$this->tokenizationService->isValidToken($request->token)) {
            return response()->json([
                'error' => 'Invalid token format'
            ], 400);
        }

        $paymentData = $this->tokenizationService->detokenizePaymentData($request->token);

        if (!$paymentData) {
            return response()->json([
                'error' => 'Invalid or expired token'
            ], 400);
        }

        return response()->json([
            'payment_data' => $paymentData,
            'message' => 'Payment data retrieved successfully'
        ]);
    }

    /**
     * Delete a payment token.
     */
    public function deleteToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        if (!$this->tokenizationService->isValidToken($request->token)) {
            return response()->json([
                'error' => 'Invalid token format'
            ], 400);
        }

        $deleted = $this->tokenizationService->deleteToken($request->token);

        if ($deleted) {
            return response()->json([
                'message' => 'Payment token deleted successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Token may have already been deleted or was invalid'
            ]);
        }
    }

    /**
     * Tokenize card data and return a masked version.
     */
    public function tokenizeCard(Request $request)
    {
        $request->validate([
            'number' => 'required|string',
            'cvv' => 'required|string',
            'exp_month' => 'required|string',
            'exp_year' => 'required|string',
            'name' => 'required|string',
        ]);

        $expiryDate = $request->exp_month . '/' . $request->exp_year;

        $tokenizedCard = $this->tokenizationService->tokenizeCardData([
            'number' => $request->number,
            'cvv' => $request->cvv,
            'expiry' => $expiryDate,
            'name' => $request->name,
        ]);

        return response()->json([
            'token' => $tokenizedCard['token'] ?? null,
            'display_number' => $tokenizedCard['display_number'] ?? null,
            'message' => 'Card data tokenized successfully'
        ]);
    }
}