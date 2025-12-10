<?php

namespace App\Http\Controllers;

use App\Services\CharityDonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharityDonationController extends Controller
{
    private CharityDonationService $donationService;

    public function __construct()
    {
        $this->donationService = new CharityDonationService();
    }

    /**
     * Get all available charity partners.
     */
    public function getCharityPartners()
    {
        $partners = $this->donationService->getCharityPartners();

        return response()->json([
            'charity_partners' => $partners,
            'message' => 'Charity partners retrieved successfully'
        ]);
    }

    /**
     * Get a specific charity partner.
     */
    public function getCharityPartner(Request $request, string $charityId)
    {
        $partner = $this->donationService->getCharityPartner($charityId);

        if (!$partner) {
            return response()->json([
                'error' => 'Charity partner not found'
            ], 404);
        }

        return response()->json([
            'charity_partner' => $partner,
            'message' => 'Charity partner retrieved successfully'
        ]);
    }

    /**
     * Get donation options.
     */
    public function getDonationOptions()
    {
        $options = $this->donationService->getDonationOptions();

        return response()->json([
            'donation_options' => $options,
            'message' => 'Donation options retrieved successfully'
        ]);
    }

    /**
     * Process a donation during checkout.
     */
    public function processDonation(Request $request)
    {
        $request->validate([
            'charity_id' => 'required|string',
            'amount' => 'required|numeric|min:1|max:10000',
            'currency' => 'string|size:3',
            'payment_method' => 'string',
            'payment_reference' => 'string',
        ]);

        try {
            $userId = Auth::id();
            $result = $this->donationService->processDonation($request->all(), $userId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Calculate suggested donation amounts based on purchase total.
     */
    public function calculateSuggestedDonationAmounts(Request $request)
    {
        $request->validate([
            'purchase_total' => 'required|numeric|min:0'
        ]);

        $suggestions = $this->donationService->calculateSuggestedDonationAmounts($request->purchase_total);

        return response()->json([
            'suggested_amounts' => $suggestions,
            'message' => 'Suggested donation amounts calculated'
        ]);
    }

    /**
     * Get user's donation history.
     */
    public function getUserDonationHistory()
    {
        $userId = Auth::id();
        $history = $this->donationService->getUserDonationHistory($userId);

        return response()->json([
            'donation_history' => $history,
            'message' => 'User donation history retrieved successfully'
        ]);
    }

    /**
     * Get charity donation statistics.
     */
    public function getCharityDonationStats(Request $request, string $charityId)
    {
        $stats = $this->donationService->getCharityDonationStats($charityId);

        return response()->json([
            'stats' => $stats,
            'charity_id' => $charityId,
            'message' => 'Charity donation statistics retrieved successfully'
        ]);
    }

    /**
     * Validate user's donation eligibility.
     */
    public function validateDonationEligibility(Request $request)
    {
        $request->validate([
            'user_data' => 'required|array',
        ]);

        $eligibility = $this->donationService->validateDonationEligibility($request->user_data);

        return response()->json([
            'eligibility' => $eligibility,
            'message' => 'Donation eligibility checked'
        ]);
    }

    /**
     * Get recommended charities for the user.
     */
    public function getRecommendedCharities(Request $request)
    {
        $request->validate([
            'preferred_categories' => 'array',
            'preferred_categories.*' => 'string',
        ]);

        $userId = Auth::id();
        $recommendations = $this->donationService->getRecommendedCharities($userId, $request->all());

        return response()->json([
            'recommendations' => $recommendations,
            'message' => 'Recommended charities retrieved successfully'
        ]);
    }

    /**
     * Get donation receipt.
     */
    public function getDonationReceipt(Request $request, string $donationId)
    {
        $receipt = $this->donationService->getDonationReceipt($donationId);

        if (!$receipt) {
            return response()->json([
                'error' => 'Donation receipt not found'
            ], 404);
        }

        return response()->json([
            'receipt' => $receipt,
            'message' => 'Donation receipt retrieved successfully'
        ]);
    }
}