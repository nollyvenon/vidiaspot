<?php

namespace App\Services;

use App\Models\InsurancePolicy;
use App\Models\InsuranceProvider;

class InsuranceCalculatorService
{
    /**
     * Calculate premium for life insurance
     */
    public function calculateLifeInsurancePremium($age, $sumAssured, $term, $smokingStatus, $gender, $healthStatus = 'good')
    {
        // Base rate calculation
        $baseRate = 0.01; // 1% base rate
        
        // Apply age factor (older = higher risk)
        $ageFactor = $age < 30 ? 1.0 : ($age < 40 ? 1.2 : ($age < 50 ? 1.5 : 2.0));
        
        // Apply smoking factor
        $smokingFactor = $smokingStatus === 'yes' ? 2.0 : 1.0;
        
        // Apply gender factor
        $genderFactor = $gender === 'female' ? 0.9 : 1.0;
        
        // Apply health factor
        $healthFactor = $healthStatus === 'excellent' ? 0.8 : ($healthStatus === 'good' ? 1.0 : 1.3);
        
        // Calculate premium
        $premium = $sumAssured * $baseRate * $ageFactor * $smokingFactor * $genderFactor * $healthFactor;
        
        // Apply term discount for longer terms
        $termDiscount = $term >= 20 ? 0.85 : ($term >= 10 ? 0.9 : 1.0);
        
        $finalPremium = $premium * $termDiscount;
        
        return [
            'annual_premium' => round($finalPremium, 2),
            'monthly_premium' => round($finalPremium / 12, 2),
            'quarterly_premium' => round($finalPremium / 4, 2),
            'details' => [
                'age_factor' => $ageFactor,
                'smoking_factor' => $smokingFactor,
                'gender_factor' => $genderFactor,
                'health_factor' => $healthFactor,
                'term_discount' => $termDiscount,
            ]
        ];
    }

    /**
     * Calculate premium for health insurance
     */
    public function calculateHealthInsurancePremium($age, $sumInsured, $memberCount = 1, $roomCategory = 'general', $preExistingCondition = false, $location = 'metro')
    {
        // Base rate
        $baseRate = $location === 'metro' ? 0.025 : 0.02; // Higher in metro cities
        
        // Adjust for age bracket
        $ageFactor = $age < 25 ? 1.0 : ($age < 35 ? 1.2 : ($age < 45 ? 1.5 : ($age < 55 ? 2.0 : 3.0)));
        
        // Adjust for member count (family discount)
        $memberFactor = $memberCount === 1 ? 1.0 : ($memberCount === 2 ? 1.8 : ($memberCount === 3 ? 2.5 : 3.0));
        
        // Adjust for room category
        $roomFactor = $roomCategory === 'icu' ? 1.3 : ($roomCategory === 'semi-private' ? 1.2 : 1.0);
        
        // Adjust for pre-existing conditions
        $conditionFactor = $preExistingCondition ? 1.5 : 1.0;
        
        // Calculate premium
        $premium = $sumInsured * $baseRate * $ageFactor * $memberFactor * $roomFactor * $conditionFactor;
        
        return [
            'annual_premium' => round($premium, 2),
            'monthly_premium' => round($premium / 12, 2),
            'details' => [
                'age_factor' => $ageFactor,
                'member_factor' => $memberFactor,
                'room_factor' => $roomFactor,
                'condition_factor' => $conditionFactor,
                'location_factor' => $location === 'metro' ? 1.25 : 1.0,
            ]
        ];
    }

    /**
     * Calculate premium for motor insurance (car)
     */
    public function calculateMotorInsurancePremium($vehicleType, $vehicleValue, $manufactureYear, $idvPercentage = 95, $ncb = 0, $zeroDepreciation = false, $engineCC = 1200)
    {
        // Base rate based on vehicle type
        $baseRate = $vehicleType === 'car' ? 0.03 : ($vehicleType === 'bike' ? 0.02 : 0.035);
        
        // IDV (Insured Declared Value) calculation
        $idv = $vehicleValue * ($idvPercentage / 100);
        
        // Age depreciation factor
        $yearsOld = date('Y') - $manufactureYear;
        $depreciationFactor = max(0.1, 1 - ($yearsOld * 0.05)); // 5% depreciation per year
        
        // Engine factor for cars
        $engineFactor = $engineCC <= 1000 ? 1.0 : ($engineCC <= 1500 ? 1.2 : ($engineCC <= 2000 ? 1.4 : 1.8));
        
        // NCB (No Claim Bonus) discount
        $ncbDiscount = $ncb > 0 ? (1 - ($ncb / 100)) : 1.0;
        
        // Zero depreciation add-on
        $zeroDepAddOn = $zeroDepreciation ? 1.5 : 1.0;
        
        // Calculate base premium
        $basePremium = $idv * $baseRate * $depreciationFactor * $engineFactor * $zeroDepAddOn;
        
        // Apply NCB discount
        $finalPremium = $basePremium * $ncbDiscount;
        
        return [
            'annual_premium' => round($finalPremium, 2),
            'idv' => round($idv, 2),
            'ncb_discount' => round($basePremium * (1 - $ncbDiscount), 2),
            'zero_depreciation_cost' => $zeroDepreciation ? round($basePremium * 0.5, 2) : 0,
            'details' => [
                'idv' => round($idv, 2),
                'depreciation_factor' => $depreciationFactor,
                'engine_factor' => $engineFactor,
                'ncb_discount' => $ncbDiscount,
                'zero_depreciation_applied' => $zeroDepreciation,
            ]
        ];
    }

    /**
     * Calculate EMI for insurance premium payments
     */
    public function calculateEMI($principal, $interestRate, $tenure, $frequency = 'monthly')
    {
        // Convert frequency to applicable interest rate and periods
        $monthlyRate = $interestRate / 1200; // Monthly interest rate (annual rate / 12 / 100)
        
        switch ($frequency) {
            case 'monthly':
                $n = $tenure; // Number of months
                break;
            case 'quarterly':
                $n = $tenure * 4; // Number of quarters
                $monthlyRate = $interestRate / 400; // Quarterly rate
                break;
            case 'half-yearly':
                $n = $tenure * 2; // Number of half-years
                $monthlyRate = $interestRate / 200; // Half-yearly rate
                break;
            default:
                $n = $tenure;
        }
        
        // EMI calculation formula
        if ($monthlyRate > 0) {
            $emi = $principal * $monthlyRate * pow(1 + $monthlyRate, $n) / (pow(1 + $monthlyRate, $n) - 1);
        } else {
            $emi = $principal / $n; // Simple division if no interest
        }
        
        return [
            'emi_amount' => round($emi, 2),
            'total_amount' => round($emi * $n, 2),
            'total_interest' => round(($emi * $n) - $principal, 2),
            'frequency' => $frequency,
            'tenure_periods' => $n,
            'interest_rate' => $interestRate
        ];
    }

    /**
     * Calculate term insurance with various riders
     */
    public function calculateTermInsuranceWithRiders($age, $sumAssured, $term, $riders = [])
    {
        $basePremium = $this->calculateLifeInsurancePremium($age, $sumAssured, $term, 'no', 'male')['annual_premium'];
        
        $riderCost = 0;
        $riderDetails = [];
        
        // Accidental Death Benefit Rider
        if (isset($riders['accidental_death_benefit']) && $riders['accidental_death_benefit']) {
            $riderCost += $sumAssured * 0.001; // 0.1% of sum assured
            $riderDetails['accidental_death_benefit'] = 'Included: +' . ($sumAssured * 0.001);
        }
        
        // Critical Illness Rider
        if (isset($riders['critical_illness']) && $riders['critical_illness']) {
            $riderCost += $sumAssured * 0.002; // 0.2% of sum assured
            $riderDetails['critical_illness'] = 'Included: +' . ($sumAssured * 0.002);
        }
        
        // Waiver of Premium Rider
        if (isset($riders['waiver_of_premium']) && $riders['waiver_of_premium']) {
            $riderCost += $basePremium * 0.05; // 5% of base premium
            $riderDetails['waiver_of_premium'] = 'Included: +' . ($basePremium * 0.05);
        }
        
        $totalPremium = $basePremium + $riderCost;
        
        return [
            'base_premium' => round($basePremium, 2),
            'rider_cost' => round($riderCost, 2),
            'total_premium' => round($totalPremium, 2),
            'monthly_premium' => round($totalPremium / 12, 2),
            'riders' => $riderDetails
        ];
    }

    /**
     * Compare insurance policies from different providers
     */
    public function compareInsurancePolicies($requirements, $category)
    {
        // In a real implementation, this would connect to various insurance provider APIs
        // For now, we'll simulate comparison data
        
        $providers = [
            [
                'name' => 'Max Life Insurance',
                'policy_name' => 'Smart Secure Plus',
                'coverage' => $requirements['sum_assured'] ?? 5000000,
                'premium' => $requirements['age'] < 30 ? 12000 : ($requirements['age'] < 40 ? 18000 : 25000),
                'features' => ['24/7 Support', 'Digital Claims', 'Family Discount'],
                'rating' => 4.5,
                'claim_settlement_ratio' => '96.5%',
                'link' => '/insurance/providers/max-life'
            ],
            [
                'name' => 'ICICI Prudential',
                'policy_name' => 'iProtect Smart',
                'coverage' => $requirements['sum_assured'] ?? 5000000,
                'premium' => $requirements['age'] < 30 ? 11500 : ($requirements['age'] < 40 ? 17500 : 24000),
                'features' => ['Flexibility', 'Riders Options', 'Online Management'],
                'rating' => 4.3,
                'claim_settlement_ratio' => '95.2%',
                'link' => '/insurance/providers/icici-prudential'
            ],
            [
                'name' => 'HDFC Life',
                'policy_name' => 'Click 2 Protect 3D',
                'coverage' => $requirements['sum_assured'] ?? 5000000,
                'premium' => $requirements['age'] < 30 ? 12500 : ($requirements['age'] < 40 ? 19000 : 26000),
                'features' => ['Quick Process', 'High Coverage', 'Easy Claims'],
                'rating' => 4.4,
                'claim_settlement_ratio' => '97.1%',
                'link' => '/insurance/providers/hdfc-life'
            ]
        ];

        // Filter providers based on category
        if ($category === 'health') {
            $providers = [
                [
                    'name' => 'Star Health',
                    'policy_name' => 'Family Health Optima',
                    'coverage' => $requirements['sum_insured'] ?? 500000,
                    'premium' => $requirements['age'] < 30 ? 8000 : ($requirements['age'] < 40 ? 12000 : 18000),
                    'features' => ['Network Hospitals', 'Cashless', 'Pre & Post Hospitalization'],
                    'rating' => 4.2,
                    'claim_settlement_ratio' => '92.3%',
                    'link' => '/insurance/providers/star-health'
                ],
                [
                    'name' => 'Apollo Munich',
                    'policy_name' => 'Easy Health',
                    'coverage' => $requirements['sum_insured'] ?? 500000,
                    'premium' => $requirements['age'] < 30 ? 7500 : ($requirements['age'] < 40 ? 11000 : 16000),
                    'features' => ['Day Care', 'Ailment Covers', 'Restoration'],
                    'rating' => 4.1,
                    'claim_settlement_ratio' => '91.8%',
                    'link' => '/insurance/providers/apollo-munich'
                ]
            ];
        } else if ($category === 'motor') {
            $providers = [
                [
                    'name' => 'Bajaj Allianz',
                    'policy_name' => 'Car Insurance',
                    'coverage' => $requirements['vehicle_value'] ?? 800000,
                    'premium' => $requirements['vehicle_value'] ? ($requirements['vehicle_value'] * 0.035) : 15000,
                    'features' => ['Zero Depreciation', '24/7 Support', 'Quick Claims'],
                    'rating' => 4.3,
                    'claim_settlement_ratio' => '93.5%',
                    'link' => '/insurance/providers/bajaj-allianz'
                ],
                [
                    'name' => 'ICICI Lombard',
                    'policy_name' => 'Car Insurance',
                    'coverage' => $requirements['vehicle_value'] ?? 800000,
                    'premium' => $requirements['vehicle_value'] ? ($requirements['vehicle_value'] * 0.032) : 14000,
                    'features' => ['Digital Claims', 'Emergency Support', 'Add-on Covers'],
                    'rating' => 4.4,
                    'claim_settlement_ratio' => '94.1%',
                    'link' => '/insurance/providers/icici-lombard'
                ]
            ];
        }

        return $providers;
    }
}