<?php

namespace App\Services;

use App\Models\VendorStore;
use App\Models\CustomAdField;
use App\Models\InsurancePolicy;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdvancedPaymentService
{

    /**
     * Creates or gets a vendor store for the user
     */
    public function createVendorStore($userId, $storeData)
    {
        $user = User::findOrFail($userId);

        // Create a unique slug for the store
        $slug = Str::slug($storeData['store_name'] . '-' . $user->id);
        
        $store = VendorStore::create([
            'user_id' => $userId,
            'store_name' => $storeData['store_name'],
            'store_slug' => $slug,
            'description' => $storeData['description'] ?? '',
            'theme' => $storeData['theme'] ?? 'default',
            'theme_config' => $storeData['theme_config'] ?? [],
            'logo_url' => $storeData['logo_url'] ?? null,
            'banner_url' => $storeData['banner_url'] ?? null,
            'contact_email' => $storeData['contact_email'] ?? $user->email,
            'contact_phone' => $storeData['contact_phone'] ?? $user->phone,
            'business_hours' => $storeData['business_hours'] ?? [],
            'social_links' => $storeData['social_links'] ?? [],
            'settings' => $storeData['settings'] ?? [],
        ]);

        return $store;
    }

    /**
     * Updates a vendor store
     */
    public function updateVendorStore($storeId, $storeData)
    {
        $store = VendorStore::findOrFail($storeId);
        
        $store->update([
            'store_name' => $storeData['store_name'] ?? $store->store_name,
            'description' => $storeData['description'] ?? $store->description,
            'theme' => $storeData['theme'] ?? $store->theme,
            'theme_config' => $storeData['theme_config'] ?? $store->theme_config,
            'logo_url' => $storeData['logo_url'] ?? $store->logo_url,
            'banner_url' => $storeData['banner_url'] ?? $store->banner_url,
            'contact_email' => $storeData['contact_email'] ?? $store->contact_email,
            'contact_phone' => $storeData['contact_phone'] ?? $store->contact_phone,
            'business_hours' => $storeData['business_hours'] ?? $store->business_hours,
            'social_links' => $storeData['social_links'] ?? $store->social_links,
            'settings' => $storeData['settings'] ?? $store->settings,
        ]);

        return $store;
    }

    /**
     * Gets vendor store by ID or slug
     */
    public function getVendorStore($identifier, $bySlug = false)
    {
        if ($bySlug) {
            return VendorStore::where('store_slug', $identifier)->with('ads')->first();
        }
        return VendorStore::where('id', $identifier)->with('ads')->first();
    }

    /**
     * Gets all vendor store themes
     */
    public function getAvailableThemes()
    {
        $templates = \App\Models\StoreTemplate::where('is_active', true)
                                              ->orderBy('sort_order')
                                              ->orderBy('created_at')
                                              ->get();

        $themes = [];
        foreach ($templates as $template) {
            $themes[$template->key] = [
                'name' => $template->name,
                'description' => $template->description,
                'features' => $template->features ?? [],
            ];
        }

        return $themes;
    }

    /**
     * Adds custom ad fields for an ad
     */
    public function addCustomAdFields($adId, $fields)
    {
        $ad = Ad::findOrFail($adId);

        // Clear existing fields
        CustomAdField::where('ad_id', $adId)->delete();

        // Add new fields
        foreach ($fields as $field) {
            CustomAdField::create([
                'ad_id' => $adId,
                'field_key' => $field['key'],
                'field_label' => $field['label'],
                'field_type' => $field['type'],
                'field_options' => $field['options'] ?? null,
                'field_value' => $field['value'],
                'field_config' => $field['config'] ?? [],
                'sort_order' => $field['sort_order'] ?? 0,
                'is_active' => $field['is_active'] ?? true,
            ]);
        }

        return $ad->refresh()->customFields;
    }

    /**
     * Gets custom ad fields for an ad
     */
    public function getCustomAdFields($adId)
    {
        return CustomAdField::where('ad_id', $adId)->orderBy('sort_order')->get();
    }

    /**
     * Creates an insurance policy for an ad or general purchase
     */
    public function createInsurancePolicy($policyData, $country = null, $state = null, $city = null)
    {
        // Check if insurance policy feature is available
        $featureService = new \App\Services\FeatureAvailabilityService();
        if (!$featureService->isInsurancePolicyAvailable($country, $state, $city)) {
            throw new \Exception('Insurance policy feature is not available in your region');
        }

        $policyNumber = $this->generatePolicyNumber();

        // Find provider by name to get provider ID
        $provider = \App\Models\InsuranceProvider::where('name', $policyData['provider'])->first();
        $providerId = $provider ? $provider->id : null;

        $policy = InsurancePolicy::create([
            'user_id' => $policyData['user_id'],
            'ad_id' => $policyData['ad_id'] ?? null,
            'policy_number' => $policyNumber,
            'provider' => $policyData['provider'],
            'provider_id' => $providerId,
            'coverage_type' => $policyData['coverage_type'],
            'policy_title' => $policyData['policy_title'],
            'description' => $policyData['description'],
            'premium_amount' => $policyData['premium_amount'],
            'coverage_amount' => $policyData['coverage_amount'],
            'status' => 'active',
            'risk_level' => $policyData['risk_level'] ?? 'medium',
            'effective_from' => $policyData['effective_from'],
            'effective_until' => $policyData['effective_until'],
            'billing_cycle' => $policyData['billing_cycle'] ?? 'one_time',
            'coverage_details' => $policyData['coverage_details'] ?? [],
            'exclusions' => $policyData['exclusions'] ?? [],
            'claim_requirements' => $policyData['claim_requirements'] ?? [],
            'beneficiaries' => $policyData['beneficiaries'] ?? [],
            'documents' => $policyData['documents'] ?? [],
            'terms_and_conditions' => $policyData['terms_and_conditions'] ?? '',
            'custom_fields' => $policyData['custom_fields'] ?? [],
            'insurance_category' => $policyData['insurance_category'] ?? null,
            'insured_value' => $policyData['insured_value'] ?? null,
            'deductible_amount' => $policyData['deductible_amount'] ?? null,
            'payment_frequency' => $policyData['payment_frequency'] ?? null,
            'agent_id' => $policyData['agent_id'] ?? null,
            'commission_rate' => $policyData['commission_rate'] ?? null,
            'commission_amount' => $policyData['commission_amount'] ?? null,
            'renewal_reminder_sent' => false,
            'next_renewal_date' => $policyData['next_renewal_date'] ?? null,
            'policy_type' => $policyData['policy_type'] ?? null,
            'coverage_area' => $policyData['coverage_area'] ?? null,
            'network_hospitals' => $policyData['network_hospitals'] ?? null,
            'zero_depreciation' => $policyData['zero_depreciation'] ?? false,
            'ncb_protector' => $policyData['ncb_protector'] ?? false,
            'policy_documents' => $policyData['policy_documents'] ?? [],
            'claim_status' => null,
            'claim_amount' => null,
            'claim_date' => null,
        ]);

        return $policy;
    }

    /**
     * Updates an insurance policy
     */
    public function updateInsurancePolicy($policyId, $policyData)
    {
        $policy = InsurancePolicy::findOrFail($policyId);

        $policy->update([
            'policy_title' => $policyData['policy_title'] ?? $policy->policy_title,
            'description' => $policyData['description'] ?? $policy->description,
            'premium_amount' => $policyData['premium_amount'] ?? $policy->premium_amount,
            'coverage_amount' => $policyData['coverage_amount'] ?? $policy->coverage_amount,
            'status' => $policyData['status'] ?? $policy->status,
            'effective_until' => $policyData['effective_until'] ?? $policy->effective_until,
            'coverage_details' => $policyData['coverage_details'] ?? $policy->coverage_details,
            'exclusions' => $policyData['exclusions'] ?? $policy->exclusions,
            'custom_fields' => $policyData['custom_fields'] ?? $policy->custom_fields,
        ]);

        return $policy;
    }

    /**
     * Gets an insurance policy
     */
    public function getInsurancePolicy($policyId)
    {
        return InsurancePolicy::findOrFail($policyId);
    }

    /**
     * Gets all insurance policies for a user
     */
    public function getUserInsurancePolicies($userId)
    {
        return InsurancePolicy::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Generates a unique policy number
     */
    private function generatePolicyNumber()
    {
        return 'VIDI_INS_' . date('Y') . '_' . strtoupper(Str::random(8));
    }

    /**
     * Submits a claim for an insurance policy
     */
    public function submitInsuranceClaim($policyId, $claimData)
    {
        $policy = InsurancePolicy::findOrFail($policyId);

        if ($policy->status !== 'active') {
            throw new \Exception('Cannot submit claim for inactive policy');
        }

        // Update the policy status
        $policy->update([
            'status' => 'claimed',
            'claimed_at' => now(),
            'claim_status' => 'pending',
            'claim_amount' => $claimData['claim_amount'] ?? null,
            'claim_date' => now(),
        ]);

        // Add claim to policy details (in a real app, this would be a separate Claims model)
        $existingClaims = $policy->custom_fields['claims'] ?? [];
        $existingClaims[] = [
            'id' => 'claim_' . time() . '_' . rand(1000, 9999),
            'submitted_at' => now(),
            'details' => $claimData,
            'status' => 'pending',
        ];

        $policy->update([
            'custom_fields' => array_merge($policy->custom_fields ?? [], ['claims' => $existingClaims])
        ]);

        return $policy;
    }

    /**
     * Calculate insurance premium using the calculator service
     */
    public function calculateInsurancePremium($calculationData, $country = null, $state = null, $city = null)
    {
        // Check if insurance policy feature is available
        $featureService = new \App\Services\FeatureAvailabilityService();
        if (!$featureService->isInsurancePolicyAvailable($country, $state, $city)) {
            throw new \Exception('Insurance policy feature is not available in your region');
        }

        $calculator = new \App\Services\InsuranceCalculatorService();

        switch ($calculationData['type']) {
            case 'life':
                return $calculator->calculateLifeInsurancePremium(
                    $calculationData['age'],
                    $calculationData['sum_assured'],
                    $calculationData['term'],
                    $calculationData['smoking_status'] ?? 'no',
                    $calculationData['gender'] ?? 'male',
                    $calculationData['health_status'] ?? 'good'
                );
            case 'health':
                return $calculator->calculateHealthInsurancePremium(
                    $calculationData['age'],
                    $calculationData['sum_insured'],
                    $calculationData['member_count'] ?? 1,
                    $calculationData['room_category'] ?? 'general',
                    $calculationData['pre_existing_condition'] ?? false,
                    $calculationData['location'] ?? 'metro'
                );
            case 'motor':
                return $calculator->calculateMotorInsurancePremium(
                    $calculationData['vehicle_type'] ?? 'car',
                    $calculationData['vehicle_value'],
                    $calculationData['manufacture_year'],
                    $calculationData['idv_percentage'] ?? 95,
                    $calculationData['ncb'] ?? 0,
                    $calculationData['zero_depreciation'] ?? false,
                    $calculationData['engine_cc'] ?? 1200
                );
            default:
                throw new \Exception('Invalid insurance type for calculation');
        }
    }

    /**
     * Calculate EMI for insurance premium payments
     */
    public function calculateInsuranceEMI($principal, $interestRate, $tenure, $frequency = 'monthly')
    {
        $calculator = new \App\Services\InsuranceCalculatorService();
        return $calculator->calculateEMI($principal, $interestRate, $tenure, $frequency);
    }

    /**
     * Compare insurance policies from different providers
     */
    public function compareInsurancePolicies($requirements, $category, $country = null, $state = null, $city = null)
    {
        // Check if insurance aggregator feature is available
        $featureService = new \App\Services\FeatureAvailabilityService();
        if (!$featureService->isInsuranceAggregatorAvailable($country, $state, $city)) {
            throw new \Exception('Insurance comparison feature is not available in your region');
        }

        // Filter providers based on location and availability
        $availableProviders = $featureService->getAvailableInsuranceProviders($category, $country, $state, $city);

        if ($availableProviders->isEmpty()) {
            throw new \Exception('No insurance providers available in your region');
        }

        $calculator = new \App\Services\InsuranceCalculatorService();
        $providers = $calculator->compareInsurancePolicies($requirements, $category);

        // Filter providers based on available providers in the user's region
        $filteredProviders = [];
        foreach ($providers as $provider) {
            $providerName = $provider['name'];
            $matchingProvider = $availableProviders->first(function($p) use ($providerName) {
                return strpos(strtolower($p->name), strtolower($providerName)) !== false;
            });

            if ($matchingProvider) {
                $filteredProviders[] = $provider;
            }
        }

        return $filteredProviders;
    }

    /**
     * Get all insurance providers
     */
    public function getInsuranceProviders($category = null, $area = null, $country = null, $state = null, $city = null)
    {
        // Check if insurance policy feature is available
        $featureService = new \App\Services\FeatureAvailabilityService();
        if (!$featureService->isInsurancePolicyAvailable($country, $state, $city)) {
            return collect(); // Return empty collection if feature is not available
        }

        return $featureService->getAvailableInsuranceProviders($category, $country, $state, $city);
    }

    /**
     * Send renewal reminders to policyholders
     */
    public function sendRenewalReminders($daysBeforeRenewal = 15)
    {
        $renewalDate = now()->addDays($daysBeforeRenewal);
        $policies = InsurancePolicy::where('next_renewal_date', $renewalDate)
                                  ->where('renewal_reminder_sent', false)
                                  ->where('status', 'active')
                                  ->get();

        foreach ($policies as $policy) {
            // In a real app, this would send an email/SMS notification
            $policy->update(['renewal_reminder_sent' => true]);

            // Log reminder sent or queue notification
            // Here we could integrate with notification services
        }

        return $policies->count() . ' renewal reminders sent';
    }

    /**
     * Get policy documents for a user
     */
    public function getPolicyDocuments($userId)
    {
        return InsurancePolicy::where('user_id', $userId)
                             ->whereNotNull('policy_documents')
                             ->get(['policy_number', 'policy_title', 'policy_documents', 'created_at']);
    }

    /**
     * Upload policy document
     */
    public function uploadPolicyDocument($policyId, $documentPath, $documentType = 'policy')
    {
        $policy = InsurancePolicy::findOrFail($policyId);

        $documents = $policy->policy_documents ?? [];
        $documents[] = [
            'type' => $documentType,
            'path' => $documentPath,
            'uploaded_at' => now(),
            'status' => 'uploaded'
        ];

        $policy->update(['policy_documents' => $documents]);

        return $policy;
    }

    /**
     * Track commission for agents
     */
    public function trackAgentCommission($agentId, $policyId, $amount)
    {
        $policy = InsurancePolicy::findOrFail($policyId);

        $policy->update([
            'agent_id' => $agentId,
            'commission_amount' => $amount,
            'commission_rate' => ($amount / $policy->premium_amount) * 100
        ]);

        // Could also store in a separate commission tracking table
        return $policy;
    }

    /**
     * Get user's insurance dashboard data
     */
    public function getUserInsuranceDashboard($userId)
    {
        $activePolicies = InsurancePolicy::where('user_id', $userId)
                                        ->where('status', 'active')
                                        ->count();

        $expiringPolicies = InsurancePolicy::where('user_id', $userId)
                                           ->where('status', 'active')
                                           ->where('effective_until', '<=', now()->addDays(30))
                                           ->count();

        $totalCoverage = InsurancePolicy::where('user_id', $userId)
                                        ->sum('coverage_amount');

        $totalPremium = InsurancePolicy::where('user_id', $userId)
                                       ->sum('premium_amount');

        $totalClaims = InsurancePolicy::where('user_id', $userId)
                                      ->whereNotNull('claim_status')
                                      ->count();

        return [
            'total_policies' => $activePolicies,
            'expiring_policies' => $expiringPolicies,
            'total_coverage' => $totalCoverage,
            'total_premium_paid' => $totalPremium,
            'total_claims_made' => $totalClaims,
        ];
    }

    /**
     * Create term insurance policy with riders
     */
    public function createTermInsurancePolicy($policyData, $country = null, $state = null, $city = null)
    {
        $calculator = new \App\Services\InsuranceCalculatorService();
        $premiumCalc = $calculator->calculateTermInsuranceWithRiders(
            $policyData['age'] ?? 30,
            $policyData['sum_assured'] ?? 5000000,
            $policyData['term'] ?? 20,
            $policyData['riders'] ?? []
        );

        $policyData['premium_amount'] = $premiumCalc['total_premium'];
        $policyData['custom_fields'] = array_merge($policyData['custom_fields'] ?? [], [
            'riders' => $policyData['riders'] ?? [],
            'premium_breakdown' => $premiumCalc
        ]);

        return $this->createInsurancePolicy($policyData, $country, $state, $city);
    }

    /**
     * Process insurance for high-value ads
     */
    public function processAdInsurance($adId, $userId, $insuranceData, $country = null, $state = null, $city = null)
    {
        $ad = Ad::findOrFail($adId);

        $policyData = [
            'user_id' => $userId,
            'ad_id' => $adId,
            'provider' => $insuranceData['provider'],
            'coverage_type' => $insuranceData['coverage_type'],
            'policy_title' => $insuranceData['policy_title'] ?? 'Ad Insurance for ' . $ad->title,
            'description' => $insuranceData['description'],
            'premium_amount' => $insuranceData['premium_amount'],
            'coverage_amount' => $insuranceData['coverage_amount'],
            'risk_level' => $insuranceData['risk_level'],
            'effective_from' => now(),
            'effective_until' => $insuranceData['effective_until'],
            'billing_cycle' => 'one_time',
            'coverage_details' => $insuranceData['coverage_details'],
            'exclusions' => $insuranceData['exclusions'] ?? [],
            'claim_requirements' => $insuranceData['claim_requirements'] ?? [],
            'beneficiaries' => [$userId],
            'documents' => $insuranceData['documents'] ?? [],
            'terms_and_conditions' => $insuranceData['terms'] ?? '',
        ];

        return $this->createInsurancePolicy($policyData, $country, $state, $city);
    }

    /**
     * Get predefined custom field templates for different categories
     */
    public function getCustomFieldTemplates($category = null)
    {
        $templates = [
            'electronics' => [
                ['key' => 'brand', 'label' => 'Brand', 'type' => 'text'],
                ['key' => 'model', 'label' => 'Model', 'type' => 'text'],
                ['key' => 'condition', 'label' => 'Condition', 'type' => 'select', 'options' => ['New', 'Like New', 'Good', 'Fair', 'Poor']],
                ['key' => 'manufacture_year', 'label' => 'Manufacture Year', 'type' => 'number'],
                ['key' => 'screen_size', 'label' => 'Screen Size (inches)', 'type' => 'number'],
                ['key' => 'storage_capacity', 'label' => 'Storage Capacity (GB)', 'type' => 'number'],
                ['key' => 'color', 'label' => 'Color', 'type' => 'text'],
            ],
            'vehicles' => [
                ['key' => 'brand', 'label' => 'Brand', 'type' => 'text'],
                ['key' => 'model', 'label' => 'Model', 'type' => 'text'],
                ['key' => 'year', 'label' => 'Year', 'type' => 'number'],
                ['key' => 'mileage', 'label' => 'Mileage', 'type' => 'number'],
                ['key' => 'fuel_type', 'label' => 'Fuel Type', 'type' => 'select', 'options' => ['Petrol', 'Diesel', 'Electric', 'Hybrid']],
                ['key' => 'transmission', 'label' => 'Transmission', 'type' => 'select', 'options' => ['Manual', 'Automatic']],
                ['key' => 'engine_size', 'label' => 'Engine Size (cc)', 'type' => 'number'],
                ['key' => 'color', 'label' => 'Color', 'type' => 'text'],
                ['key' => 'condition', 'label' => 'Condition', 'type' => 'select', 'options' => ['New', 'Used']],
            ],
            'furniture' => [
                ['key' => 'brand', 'label' => 'Brand', 'type' => 'text'],
                ['key' => 'material', 'label' => 'Material', 'type' => 'text'],
                ['key' => 'dimensions', 'label' => 'Dimensions', 'type' => 'text'],
                ['key' => 'color', 'label' => 'Color', 'type' => 'text'],
                ['key' => 'condition', 'label' => 'Condition', 'type' => 'select', 'options' => ['New', 'Like New', 'Good', 'Fair', 'Poor']],
                ['key' => 'assembly_required', 'label' => 'Assembly Required', 'type' => 'checkbox'],
            ],
            'property' => [
                ['key' => 'property_type', 'label' => 'Property Type', 'type' => 'select', 'options' => ['Apartment', 'House', 'Land', 'Commercial', 'Office', 'Shop']],
                ['key' => 'bedrooms', 'label' => 'Bedrooms', 'type' => 'number'],
                ['key' => 'bathrooms', 'label' => 'Bathrooms', 'type' => 'number'],
                ['key' => 'square_feet', 'label' => 'Square Feet', 'type' => 'number'],
                ['key' => 'property_age', 'label' => 'Property Age (years)', 'type' => 'number'],
                ['key' => 'furnished', 'label' => 'Furnished', 'type' => 'select', 'options' => ['Furnished', 'Semi-Furnished', 'Unfurnished']],
                ['key' => 'parking_space', 'label' => 'Parking Space', 'type' => 'number'],
            ],
        ];

        if ($category) {
            return $templates[$category] ?? [];
        }

        return $templates;
    }

    /**
     * Process a QR code payment
     */
    public function processQrCodePayment($userId, $paymentData)
    {
        // For a real implementation, this would interface with a QR payment service
        // For now, we'll simulate the process
        
        return [
            'success' => true,
            'qr_code' => $this->generateQrCode($paymentData['amount'], $paymentData['currency'], $userId),
            'payment_url' => url("/payment/qr/{$paymentData['reference']}"),
            'expires_at' => now()->addMinutes(15), // QR code expires in 15 minutes
            'reference' => $paymentData['reference'],
            'amount' => $paymentData['amount'],
        ];
    }

    /**
     * Generate QR code data for payment
     */
    private function generateQrCode($amount, $currency, $userId)
    {
        // In a real implementation, we would generate an actual QR code
        // For this demo, returning a mock QR code data
        return "VIDIASPOT_PAY:" . $userId . ":" . $amount . ":" . $currency . ":" . time();
    }
}