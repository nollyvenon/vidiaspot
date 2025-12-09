<?php

namespace App\Http\Controllers;

use App\Models\VendorStore;
use App\Models\CustomAdField;
use App\Models\InsurancePolicy;
use App\Models\Ad;
use App\Services\AdvancedPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorStoreController extends Controller
{
    protected $advancedPaymentService;

    public function __construct(AdvancedPaymentService $advancedPaymentService)
    {
        $this->advancedPaymentService = $advancedPaymentService;
        $this->trustSafetyService = new \App\Services\TrustSafetyService();
    }

    /**
     * Display the vendor store setup form
     */
    public function create()
    {
        $themes = $this->advancedPaymentService->getAvailableThemes();
        return view('vendor.store.setup', compact('themes'));
    }

    /**
     * Create or update vendor store
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'store_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'theme' => 'required|in:' . implode(',', array_keys($this->advancedPaymentService->getAvailableThemes())),
            'logo' => 'nullable|image|max:2048', // Max 2MB
            'banner' => 'nullable|image|max:5120', // Max 5MB
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'business_hours' => 'nullable|array',
            'social_links' => 'nullable|array',
        ]);

        // Handle file uploads if present
        $logoUrl = null;
        $bannerUrl = null;
        
        if ($request->hasFile('logo')) {
            $logoUrl = $request->file('logo')->store('vendor-logos', 'public');
        }
        
        if ($request->hasFile('banner')) {
            $bannerUrl = $request->file('banner')->store('vendor-banners', 'public');
        }

        $storeData = [
            'store_name' => $request->store_name,
            'description' => $request->description,
            'theme' => $request->theme,
            'theme_config' => $request->theme_config ?? [],
            'logo_url' => $logoUrl,
            'banner_url' => $bannerUrl,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'business_hours' => $request->business_hours ?? [],
            'social_links' => $request->social_links ?? [],
            'settings' => $request->settings ?? [],
        ];

        $store = $this->advancedPaymentService->createVendorStore($user->id, $storeData);

        return response()->json([
            'success' => true,
            'message' => 'Store created successfully',
            'store' => $store,
        ]);
    }

    /**
     * Display vendor store
     */
    public function show($slug)
    {
        $store = $this->advancedPaymentService->getVendorStore($slug, true);
        
        if (!$store) {
            abort(404, 'Store not found');
        }

        $ads = $store->ads()->with(['user', 'category', 'images'])->paginate(12);

        return view('vendor.store.show', compact('store', 'ads'));
    }

    /**
     * Edit vendor store
     */
    public function edit()
    {
        $user = Auth::user();
        $store = $user->vendorStore;

        if (!$store) {
            return redirect()->route('vendor.store.create')->with('error', 'Store not found. Please create one first.');
        }

        $themes = $this->advancedPaymentService->getAvailableThemes();
        return view('vendor.store.edit', compact('store', 'themes'));
    }

    /**
     * Update vendor store
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        // Verify that the store belongs to the user
        $store = VendorStore::findOrFail($id);
        if ($store->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'store_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'theme' => 'required|in:' . implode(',', array_keys($this->advancedPaymentService->getAvailableThemes())),
            'logo' => 'nullable|image|max:2048', // Max 2MB
            'banner' => 'nullable|image|max:5120', // Max 5MB
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'business_hours' => 'nullable|array',
            'social_links' => 'nullable|array',
        ]);

        // Handle file uploads if present
        $storeData = [
            'store_name' => $request->store_name,
            'description' => $request->description,
            'theme' => $request->theme,
            'theme_config' => $request->theme_config ?? [],
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'business_hours' => $request->business_hours ?? [],
            'social_links' => $request->social_links ?? [],
            'settings' => $request->settings ?? [],
        ];

        if ($request->hasFile('logo')) {
            $storeData['logo_url'] = $request->file('logo')->store('vendor-logos', 'public');
        }
        
        if ($request->hasFile('banner')) {
            $storeData['banner_url'] = $request->file('banner')->store('vendor-banners', 'public');
        }

        $store = $this->advancedPaymentService->updateVendorStore($id, $storeData);

        return response()->json([
            'success' => true,
            'message' => 'Store updated successfully',
            'store' => $store,
        ]);
    }

    /**
     * Get custom ad field templates
     */
    public function getCustomFieldTemplates(Request $request)
    {
        $category = $request->get('category');
        $templates = $this->advancedPaymentService->getCustomFieldTemplates($category);

        return response()->json([
            'success' => true,
            'templates' => $templates,
        ]);
    }

    /**
     * Add custom ad fields
     */
    public function addCustomAdFields(Request $request, $adId)
    {
        $user = Auth::user();
        
        // Verify that the ad belongs to the user
        $ad = Ad::findOrFail($adId);
        if ($ad->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'fields' => 'required|array',
            'fields.*.key' => 'required|string',
            'fields.*.label' => 'required|string',
            'fields.*.type' => 'required|in:text,number,select,multiselect,checkbox,date',
            'fields.*.value' => 'required|string',
            'fields.*.sort_order' => 'nullable|integer',
        ]);

        $fields = $this->advancedPaymentService->addCustomAdFields($adId, $request->fields);

        return response()->json([
            'success' => true,
            'message' => 'Custom fields added successfully',
            'fields' => $fields,
        ]);
    }

    /**
     * Get custom ad fields for an ad
     */
    public function getCustomAdFields($adId)
    {
        $user = Auth::user();
        
        // Verify that the ad belongs to the user
        $ad = Ad::findOrFail($adId);
        if ($ad->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $fields = $this->advancedPaymentService->getCustomAdFields($adId);

        return response()->json([
            'success' => true,
            'fields' => $fields,
        ]);
    }

    /**
     * Create an insurance policy
     */
    public function createInsurancePolicy(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'provider' => 'required|string|max:255',
            'coverage_type' => 'required|in:device_protection,product_insurance,delivery_insurance,accident,extended_warranty',
            'policy_title' => 'required|string|max:255',
            'description' => 'required|string',
            'premium_amount' => 'required|numeric|min:0',
            'coverage_amount' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_until' => 'required|date|after:effective_from',
            'risk_level' => 'required|in:low,medium,high',
            'coverage_details' => 'nullable|array',
            'exclusions' => 'nullable|array',
            'claim_requirements' => 'nullable|array',
        ]);

        // Check if ad belongs to user (for high-value items) or if they can purchase insurance for any item
        $ad = Ad::findOrFail($request->ad_id);
        
        $policyData = $request->only([
            'ad_id',
            'provider',
            'coverage_type',
            'policy_title',
            'description',
            'premium_amount',
            'coverage_amount',
            'effective_from',
            'effective_until',
            'risk_level',
            'billing_cycle',
            'coverage_details',
            'exclusions',
            'claim_requirements',
            'beneficiaries',
            'documents',
            'terms_and_conditions',
            'custom_fields',
        ]);

        $policyData['user_id'] = $user->id;

        $policy = $this->advancedPaymentService->createInsurancePolicy($policyData);

        return response()->json([
            'success' => true,
            'message' => 'Insurance policy created successfully',
            'policy' => $policy,
        ]);
    }

    /**
     * Get user's insurance policies
     */
    public function getUserInsurancePolicies()
    {
        $user = Auth::user();
        $policies = $this->advancedPaymentService->getUserInsurancePolicies($user->id);

        return response()->json([
            'success' => true,
            'policies' => $policies,
        ]);
    }

    /**
     * Process insurance for a specific ad
     */
    public function processAdInsurance(Request $request, $adId)
    {
        $user = Auth::user();
        
        $request->validate([
            'provider' => 'required|string|max:255',
            'coverage_type' => 'required|in:device_protection,product_insurance,delivery_insurance,accident,extended_warranty',
            'policy_title' => 'required|string|max:255',
            'description' => 'required|string',
            'premium_amount' => 'required|numeric|min:0',
            'coverage_amount' => 'required|numeric|min:0',
            'effective_until' => 'required|date|after:today',
            'risk_level' => 'required|in:low,medium,high',
            'coverage_details' => 'nullable|array',
            'exclusions' => 'nullable|array',
            'claim_requirements' => 'nullable|array',
        ]);

        // Verify that the ad exists
        $ad = Ad::findOrFail($adId);

        $insuranceData = $request->only([
            'provider',
            'coverage_type',
            'policy_title',
            'description',
            'premium_amount',
            'coverage_amount',
            'effective_until',
            'risk_level',
            'coverage_details',
            'exclusions',
            'claim_requirements',
            'beneficiaries',
            'documents',
            'terms',
        ]);

        $policy = $this->advancedPaymentService->processAdInsurance($adId, $user->id, $insuranceData);

        return response()->json([
            'success' => true,
            'message' => 'Ad insurance processed successfully',
            'policy' => $policy,
        ]);
    }

    /**
     * Submit an insurance claim
     */
    public function submitInsuranceClaim(Request $request, $policyId)
    {
        $user = Auth::user();

        $policy = InsurancePolicy::findOrFail($policyId);

        if ($policy->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'claim_description' => 'required|string',
            'damage_photos' => 'nullable|array',
            'damage_photos.*' => 'image|max:5120', // Max 5MB per photo
            'circumstances' => 'required|string',
            'any_witnesses' => 'nullable|string',
            'claim_amount' => 'required|numeric|min:0',
        ]);

        $claimData = $request->only([
            'claim_description',
            'circumstances',
            'any_witnesses',
            'claim_amount',
        ]);

        if ($request->hasFile('damage_photos')) {
            $photos = [];
            foreach ($request->file('damage_photos') as $photo) {
                $photos[] = $photo->store('insurance-claims', 'public');
            }
            $claimData['damage_photos'] = $photos;
        }

        $policy = $this->advancedPaymentService->submitInsuranceClaim($policyId, $claimData);

        return response()->json([
            'success' => true,
            'message' => 'Claim submitted successfully',
            'policy' => $policy,
        ]);
    }

    /**
     * Calculate insurance premium
     */
    public function calculateInsurancePremium(Request $request)
    {
        $request->validate([
            'type' => 'required|in:life,health,motor,travel,home',
            'age' => 'required_if:type,life,health,motor|integer|min:18|max:70',
            'sum_assured' => 'required_if:type,life|numeric|min:100000',
            'sum_insured' => 'required_if:type,health|numeric|min:10000',
            'vehicle_value' => 'required_if:type,motor|numeric|min:50000',
            'manufacture_year' => 'required_if:type,motor|integer|min:1990|max:' . date('Y'),
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        $calculationData = $request->all();
        $result = $this->advancedPaymentService->calculateInsurancePremium(
            $calculationData,
            $request->country,
            $request->state,
            $request->city
        );

        return response()->json([
            'success' => true,
            'calculation' => $result,
        ]);
    }

    /**
     * Calculate EMI for insurance payments
     */
    public function calculateInsuranceEMI(Request $request)
    {
        $request->validate([
            'principal' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'tenure' => 'required|integer|min:1',
            'frequency' => 'in:monthly,quarterly,half-yearly'
        ]);

        $result = $this->advancedPaymentService->calculateInsuranceEMI(
            $request->principal,
            $request->interest_rate,
            $request->tenure,
            $request->frequency
        );

        return response()->json([
            'success' => true,
            'emi' => $result,
        ]);
    }

    /**
     * Compare insurance policies
     */
    public function compareInsurancePolicies(Request $request)
    {
        $request->validate([
            'category' => 'required|in:life,health,motor,travel,home',
            'requirements' => 'required|array',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        $requirements = $request->requirements;
        $category = $request->category;

        $providers = $this->advancedPaymentService->compareInsurancePolicies(
            $requirements,
            $category,
            $request->country,
            $request->state,
            $request->city
        );

        return response()->json([
            'success' => true,
            'providers' => $providers,
        ]);
    }

    /**
     * Get insurance providers
     */
    public function getInsuranceProviders(Request $request)
    {
        $category = $request->get('category');
        $area = $request->get('area');
        $country = $request->get('country');
        $state = $request->get('state');
        $city = $request->get('city');

        $providers = $this->advancedPaymentService->getInsuranceProviders($category, $area, $country, $state, $city);

        return response()->json([
            'success' => true,
            'providers' => $providers,
        ]);
    }

    /**
     * Get user's insurance dashboard
     */
    public function getUserInsuranceDashboard()
    {
        $user = Auth::user();
        $dashboard = $this->advancedPaymentService->getUserInsuranceDashboard($user->id);

        return response()->json([
            'success' => true,
            'dashboard' => $dashboard,
        ]);
    }

    /**
     * Get policy documents for user
     */
    public function getPolicyDocuments()
    {
        $user = Auth::user();
        $documents = $this->advancedPaymentService->getPolicyDocuments($user->id);

        return response()->json([
            'success' => true,
            'documents' => $documents,
        ]);
    }

    /**
     * Create term insurance policy with riders
     */
    public function createTermInsurancePolicy(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'age' => 'required|integer|min:18|max:65',
            'sum_assured' => 'required|numeric|min:100000',
            'term' => 'required|integer|min:5|max:40',
            'riders' => 'array',
            'riders.accidental_death_benefit' => 'boolean',
            'riders.critical_illness' => 'boolean',
            'riders.waiver_of_premium' => 'boolean',
            // Add other standard policy validations
            'provider' => 'required|string|max:255',
            'coverage_type' => 'required|in:life,health,motor,travel,home,term',
            'policy_title' => 'required|string|max:255',
            'description' => 'required|string',
            'premium_amount' => 'required|numeric|min:0',
            'coverage_amount' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_until' => 'required|date|after:effective_from',
            'risk_level' => 'required|in:low,medium,high',
        ]);

        $policyData = $request->only([
            'age',
            'sum_assured',
            'term',
            'riders',
            'provider',
            'coverage_type',
            'policy_title',
            'description',
            'effective_from',
            'effective_until',
            'risk_level',
            'coverage_details',
            'exclusions',
            'claim_requirements',
            'beneficiaries',
            'documents',
            'terms_and_conditions',
            'custom_fields',
        ]);

        $policyData['user_id'] = $user->id;

        $policy = $this->advancedPaymentService->createTermInsurancePolicy($policyData);

        return response()->json([
            'success' => true,
            'message' => 'Term insurance policy created successfully',
            'policy' => $policy,
        ]);
    }
}