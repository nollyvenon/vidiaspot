<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class CharityDonationService
{
    /**
     * Available charity partners and their information
     */
    private array $charityPartners = [
        'education_fund' => [
            'name' => 'Education Fund',
            'description' => 'Supports education for underprivileged children',
            'category' => 'education',
            'impact_statement' => 'Every $10 provides school supplies for one child',
            'verified' => true,
            'logo_url' => 'https://example.com/charity-logos/education-fund.png',
            'website' => 'https://educationfund.org',
        ],
        'environmental_clean_water' => [
            'name' => 'Clean Water Initiative',
            'description' => 'Provides clean water access to communities worldwide',
            'category' => 'environment',
            'impact_statement' => 'Every $20 provides clean water access for one person',
            'verified' => true,
            'logo_url' => 'https://example.com/charity-logos/water-initiative.png',
            'website' => 'https://waterinitiative.org',
        ],
        'healthcare_access' => [
            'name' => 'Healthcare Access Foundation',
            'description' => 'Improves healthcare access in underserved regions',
            'category' => 'health',
            'impact_statement' => 'Every $25 funds a basic health screening',
            'verified' => true,
            'logo_url' => 'https://example.com/charity-logos/healthcare-foundation.png',
            'website' => 'https://healthcarefoundation.org',
        ],
        'women_empowerment' => [
            'name' => 'Women Empowerment Center',
            'description' => 'Supports women entrepreneurs and education',
            'category' => 'social',
            'impact_statement' => 'Every $50 provides vocational training for one woman',
            'verified' => true,
            'logo_url' => 'https://example.com/charity-logos/women-center.png',
            'website' => 'https://womenempowerment.org',
        ],
        'local_food_bank' => [
            'name' => 'Local Food Bank',
            'description' => 'Provides meals to families in need',
            'category' => 'food_security',
            'impact_statement' => 'Every $5 feeds one family member for a day',
            'verified' => true,
            'logo_url' => 'https://example.com/charity-logos/local-food-bank.png',
            'website' => 'https://localfoodbank.org',
        ],
        'minority_business_fund' => [
            'name' => 'Minority Business Support',
            'description' => 'Provides grants and resources for minority entrepreneurs',
            'category' => 'economic',
            'impact_statement' => 'Every $100 supports minority-owned business growth',
            'verified' => true,
            'logo_url' => 'https://example.com/charity-logos/minority-fund.png',
            'website' => 'https://minorityfund.org',
        ],
    ];

    /**
     * Donation matching options
     */
    private array $donationOptions = [
        'fixed_amounts' => [
            1 => '1 USD',
            5 => '5 USD',
            10 => '10 USD',
            25 => '25 USD',
            50 => '50 USD',
        ],
        'percentage_based' => [
            '1_percent' => 'Round up to nearest dollar',
            '2_percent' => '2% of purchase',
            '5_percent' => '5% of purchase',
            '10_percent' => '10% of purchase',
        ],
        'custom_amount' => [
            'min' => 1,
            'max' => 1000,
        ],
    ];

    /**
     * Get all available charity partners
     */
    public function getCharityPartners(): array
    {
        return $this->charityPartners;
    }

    /**
     * Get a specific charity partner
     */
    public function getCharityPartner(string $charityId): ?array
    {
        return $this->charityPartners[$charityId] ?? null;
    }

    /**
     * Get donation options
     */
    public function getDonationOptions(): array
    {
        return $this->donationOptions;
    }

    /**
     * Process a donation during checkout
     */
    public function processDonation(array $donationData, string $userId): array
    {
        $required = ['charity_id', 'amount', 'currency'];
        foreach ($required as $field) {
            if (!isset($donationData[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        $charityId = $donationData['charity_id'];
        $amount = $donationData['amount'];
        $currency = $donationData['currency'] ?? 'USD';

        // Validate charity exists
        $charity = $this->getCharityPartner($charityId);
        if (!$charity) {
            throw new \InvalidArgumentException("Invalid charity ID: {$charityId}");
        }

        // Validate amount
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Donation amount must be greater than 0");
        }

        // In a real implementation, this would process the payment through the chosen payment gateway
        // For this implementation, we'll simulate the transaction
        
        $transactionId = 'donation-' . Str::uuid();
        $referenceId = $donationData['payment_reference'] ?? 'checkout-' . Str::random(10);

        // Create donation record
        $donation = [
            'id' => $transactionId,
            'user_id' => $userId,
            'charity_id' => $charityId,
            'charity_name' => $charity['name'],
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $donationData['payment_method'] ?? 'credit_card',
            'payment_reference' => $referenceId,
            'status' => 'completed', // Would be 'pending' in real implementation until payment clears
            'created_at' => now()->toISOString(),
            'impact_description' => $this->generateImpactDescription($charity, $amount),
            'receipt_sent' => false, // Would be sent in real implementation
        ];

        // Store donation in cache (in real implementation, this would be stored in a database)
        $cacheKey = "donation_{$transactionId}";
        \Cache::put($cacheKey, $donation, now()->addMonths(12));

        // Add to user's donation history
        $userDonationsKey = "user_donations_{$userId}";
        $donations = \Cache::get($userDonationsKey, []);
        $donations[] = $transactionId;
        \Cache::put($userDonationsKey, $donations, now()->addMonths(12));

        // Send donation confirmation to charity (in real implementation)
        $this->notifyCharity($donation);

        return [
            'success' => true,
            'donation' => $donation,
            'message' => 'Donation processed successfully',
            'receipt_url' => "/donations/{$transactionId}/receipt", // Receipt URL for download
        ];
    }

    /**
     * Generate impact description based on donation amount
     */
    private function generateImpactDescription(array $charity, float $amount): string
    {
        $impactStatement = $charity['impact_statement'] ?? 'Your donation helps make a difference';
        
        // Extract the impact metric from the statement
        $pattern = '/Every \$(\d+) (.+)/';
        if (preg_match($pattern, $impactStatement, $matches)) {
            $baseAmount = floatval($matches[1]);
            $impactAction = $matches[2];
            
            $units = $amount / $baseAmount;
            
            return "Your donation of \${$amount} will help {$units} {$impactAction}";
        }
        
        return $impactStatement;
    }

    /**
     * Notify charity about donation
     */
    private function notifyCharity(array $donation): void
    {
        // In a real implementation, this would send a notification to the charity
        // Could be an API call, email notification, etc.
        \Log::info('Charity donation notification', [
            'charity_id' => $donation['charity_id'],
            'donation_id' => $donation['id'],
            'amount' => $donation['amount'],
            'user_id' => $donation['user_id'],
        ]);
    }

    /**
     * Calculate suggested donation amounts based on purchase total
     */
    public function calculateSuggestedDonationAmounts(float $purchaseTotal): array
    {
        return [
            'round_up' => ceil($purchaseTotal) - $purchaseTotal,
            'two_percent' => round($purchaseTotal * 0.02, 2),
            'five_percent' => round($purchaseTotal * 0.05, 2),
            'custom_suggestions' => [
                1, 5, 10, 25, 50
            ],
        ];
    }

    /**
     * Get user's donation history
     */
    public function getUserDonationHistory(string $userId): array
    {
        $userDonationsKey = "user_donations_{$userId}";
        $donationIds = \Cache::get($userDonationsKey, []);

        $donations = [];
        foreach ($donationIds as $id) {
            $donation = \Cache::get("donation_{$id}");
            if ($donation) {
                $donations[] = $donation;
            }
        }

        // Sort by date (newest first)
        usort($donations, function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        return [
            'donations' => $donations,
            'total_donated' => array_sum(array_column($donations, 'amount')),
            'total_donations_count' => count($donations),
            'user_id' => $userId,
        ];
    }

    /**
     * Get donation statistics for a charity
     */
    public function getCharityDonationStats(string $charityId): array
    {
        // In a real implementation, this would calculate stats from database
        // For this example, we'll return sample data
        
        $totalDonations = mt_rand(500, 15000); // Random sample data
        $totalAmount = $totalDonations * 25; // Assuming average donation of $25

        return [
            'charity_id' => $charityId,
            'total_donations' => $totalDonations,
            'total_amount_raised' => $totalAmount,
            'average_donation' => round($totalAmount / $totalDonations, 2),
            'last_donation' => now()->subDays(rand(1, 30))->toISOString(),
            'impact_reached' => $this->estimateImpact($charityId, $totalAmount),
        ];
    }

    /**
     * Estimate impact based on amount donated to charity
     */
    private function estimateImpact(string $charityId, float $amount): array
    {
        $charity = $this->getCharityPartner($charityId);
        $impactStatement = $charity['impact_statement'] ?? '';
        
        $pattern = '/Every \$(\d+) (.+)/';
        if (preg_match($pattern, $impactStatement, $matches)) {
            $baseAmount = floatval($matches[1]);
            $impactAction = $matches[2];
            
            $units = $amount / $baseAmount;
            
            return [
                'units_affected' => floor($units),
                'impact_description' => "Your donations have helped approximately {$units} {$impactAction}",
                'measurable_impact' => $units,
            ];
        }
        
        return [
            'units_affected' => 0,
            'impact_description' => 'Impact not quantified',
            'measurable_impact' => 0,
        ];
    }

    /**
     * Validate donation eligibility
     */
    public function validateDonationEligibility(array $userData): array
    {
        $eligibility = [
            'eligible' => true,
            'reasons' => [],
            'suggestions' => [],
        ];

        // Check if user has verified account
        if (empty($userData['verified_at'])) {
            $eligibility['eligible'] = false;
            $eligibility['reasons'][] = 'Account must be verified';
            $eligibility['suggestions'][] = 'Please complete account verification to enable donations';
        }

        // Check if user has payment method
        if (empty($userData['payment_methods']) || count($userData['payment_methods']) === 0) {
            $eligibility['eligible'] = false;
            $eligibility['reasons'][] = 'Payment method required';
            $eligibility['suggestions'][] = 'Add a payment method to make donations';
        }

        return $eligibility;
    }

    /**
     * Get recommended charities for a user based on preferences
     */
    public function getRecommendedCharities(string $userId, array $preferences = []): array
    {
        // In a real implementation, this would look at user's donation history and preferences
        // For this implementation, we'll return a sample selection
        $categories = $preferences['preferred_categories'] ?? ['education', 'environment', 'health'];
        
        $recommended = [];
        foreach ($this->charityPartners as $id => $charity) {
            if (in_array($charity['category'], $categories)) {
                $recommended[] = $charity;
            }
        }
        
        // Limit to 6 recommendations
        $recommended = array_slice($recommended, 0, 6);
        
        return [
            'recommended_charities' => $recommended,
            'user_preferences' => $preferences,
            'categories_matched' => $categories,
        ];
    }

    /**
     * Get donation receipt
     */
    public function getDonationReceipt(string $donationId): ?array
    {
        $donation = \Cache::get("donation_{$donationId}");
        
        if (!$donation) {
            return null;
        }
        
        return [
            'donation_id' => $donation['id'],
            'user_id' => $donation['user_id'],
            'charity_name' => $donation['charity_name'],
            'amount' => $donation['amount'],
            'currency' => $donation['currency'],
            'date' => $donation['created_at'],
            'status' => $donation['status'],
            'impact_description' => $donation['impact_description'],
            'receipt_generated' => now()->toISOString(),
        ];
    }
}