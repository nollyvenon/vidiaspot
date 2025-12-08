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
    // Predefined themes for vendor stores
    private $themes = [
        'default' => [
            'name' => 'Default Theme',
            'description' => 'Clean and simple theme',
            'features' => ['responsive', 'fast', 'minimal'],
        ],
        'modern' => [
            'name' => 'Modern Theme',
            'description' => 'Contemporary and sleek design',
            'features' => ['gallery', 'animations', 'modern'],
        ],
        'classic' => [
            'name' => 'Classic Theme',
            'description' => 'Traditional and reliable look',
            'features' => ['trusted', 'professional', 'classic'],
        ],
        'premium' => [
            'name' => 'Premium Theme',
            'description' => 'Elegant and feature-rich',
            'features' => ['exclusive', 'enhanced', 'premium'],
        ],
        'storefront' => [
            'name' => 'Storefront Theme',
            'description' => 'Designed for shopping experience',
            'features' => ['cart', 'checkout', 'shopping'],
        ],
        'portfolio' => [
            'name' => 'Portfolio Theme',
            'description' => 'Showcase your products beautifully',
            'features' => ['gallery', 'presentation', 'showcase'],
        ],
    ];

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
        return $this->themes;
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
    public function createInsurancePolicy($policyData)
    {
        $policyNumber = $this->generatePolicyNumber();

        $policy = InsurancePolicy::create([
            'user_id' => $policyData['user_id'],
            'ad_id' => $policyData['ad_id'] ?? null,
            'policy_number' => $policyNumber,
            'provider' => $policyData['provider'],
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
        ]);

        // Add claim to policy details (in a real app, this would be a separate Claims model)
        $existingClaims = $policy->custom_fields['claims'] ?? [];
        $existingClaims[] = [
            'id' => 'claim_' . time() . '_' . rand(1000, 9999),
            'submitted_at' => now(),
            'details' => $claimData,
            'status' => 'reviewing',
        ];

        $policy->update([
            'custom_fields' => array_merge($policy->custom_fields ?? [], ['claims' => $existingClaims])
        ]);

        return $policy;
    }

    /**
     * Process insurance for high-value ads
     */
    public function processAdInsurance($adId, $userId, $insuranceData)
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

        return $this->createInsurancePolicy($policyData);
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