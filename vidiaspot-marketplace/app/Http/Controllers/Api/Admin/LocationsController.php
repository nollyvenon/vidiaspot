<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LocationsController extends Controller
{
    /**
     * Get all countries for admin management.
     */
    public function getCountries(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Country::query();

        // Filter by active status
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $countries = $query->orderBy('name')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $countries,
            'message' => 'Countries list for admin management'
        ]);
    }

    /**
     * Store a new country.
     */
    public function storeCountry(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
            'code' => 'required|string|size:2,3|unique:countries,code',
            'phone_code' => 'nullable|string|max:10',
            'currency_code' => 'required|string|size:3|exists:currencies,code',
            'is_active' => 'boolean',
            'flag_icon' => 'nullable|string|max:50',
        ]);

        $country = Country::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $country,
            'message' => 'Country created successfully'
        ], 201);
    }

    /**
     * Update a country.
     */
    public function updateCountry(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:countries,name,' . $id,
            'code' => 'sometimes|string|size:2,3|unique:countries,code,' . $id,
            'phone_code' => 'nullable|string|max:10',
            'currency_code' => 'sometimes|string|size:3|exists:currencies,code',
            'is_active' => 'boolean',
            'flag_icon' => 'nullable|string|max:50',
        ]);

        $country = Country::findOrFail($id);
        $country->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $country->refresh(),
            'message' => 'Country updated successfully'
        ]);
    }

    /**
     * Toggle country active status.
     */
    public function toggleCountryStatus(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $country = Country::findOrFail($id);
        $country->update(['is_active' => !$country->is_active]);

        return response()->json([
            'success' => true,
            'data' => $country->refresh(),
            'message' => 'Country status updated successfully'
        ]);
    }

    /**
     * Get all states for a country.
     */
    public function getStates(Request $request, string $countryId): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = State::with('country');

        // Filter by country
        $query->where('country_id', $countryId);

        // Filter by active status
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $states = $query->orderBy('name')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $states,
            'message' => 'States list for admin management'
        ]);
    }

    /**
     * Store a new state.
     */
    public function storeState(Request $request, string $countryId): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'country_id' => 'required|integer|exists:countries,id',
            'is_active' => 'boolean',
        ]);

        $state = State::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $state,
            'message' => 'State created successfully'
        ], 201);
    }

    /**
     * Update a state.
     */
    public function updateState(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'nullable|string|max:10',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'is_active' => 'boolean',
        ]);

        $state = State::findOrFail($id);
        $state->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $state->refresh(),
            'message' => 'State updated successfully'
        ]);
    }

    /**
     * Toggle state active status.
     */
    public function toggleStateStatus(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $state = State::findOrFail($id);
        $state->update(['is_active' => !$state->is_active]);

        return response()->json([
            'success' => true,
            'data' => $state->refresh(),
            'message' => 'State status updated successfully'
        ]);
    }

    /**
     * Get all cities for a state.
     */
    public function getCities(Request $request, string $stateId): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = City::with(['state', 'state.country']);

        // Filter by state
        $query->where('state_id', $stateId);

        // Filter by active status
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $cities = $query->orderBy('name')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $cities,
            'message' => 'Cities list for admin management'
        ]);
    }

    /**
     * Store a new city.
     */
    public function storeCity(Request $request, string $stateId): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|integer|exists:states,id',
            'is_active' => 'boolean',
        ]);

        $city = City::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $city,
            'message' => 'City created successfully'
        ], 201);
    }

    /**
     * Update a city.
     */
    public function updateCity(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'state_id' => 'sometimes|integer|exists:states,id',
            'is_active' => 'boolean',
        ]);

        $city = City::findOrFail($id);
        $city->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $city->refresh(),
            'message' => 'City updated successfully'
        ]);
    }

    /**
     * Toggle city active status.
     */
    public function toggleCityStatus(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $city = City::findOrFail($id);
        $city->update(['is_active' => !$city->is_active]);

        return response()->json([
            'success' => true,
            'data' => $city->refresh(),
            'message' => 'City status updated successfully'
        ]);
    }

    /**
     * Get location statistics.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_countries' => Country::count(),
            'total_states' => State::count(),
            'total_cities' => City::count(),
            'active_countries' => Country::where('is_active', true)->count(),
            'active_states' => State::where('is_active', true)->count(),
            'active_cities' => City::where('is_active', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Location statistics'
        ]);
    }
}
