<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    /**
     * Display countries management page.
     */
    public function countries(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Country::query();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('code', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('active')) {
            $isActive = $request->active === 'yes';
            $query->where('is_active', $isActive);
        }

        $countries = $query->orderBy('name')->paginate(25);

        return $this->adminView('admin.locations.countries', [
            'countries' => $countries,
        ]);
    }

    /**
     * Store a new country.
     */
    public function storeCountry(Request $request): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:3|unique:countries,code',
            'phone_code' => 'nullable|string|max:10',
            'currency_code' => 'required|string|size:3',
            'is_active' => 'boolean',
            'flag_icon' => 'nullable|string|max:50',
        ]);

        $country = Country::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'phone_code' => $request->phone_code,
            'currency_code' => strtoupper($request->currency_code),
            'is_active' => $request->is_active ?? true,
            'flag_icon' => $request->flag_icon,
        ]);

        return response()->json([
            'message' => 'Country created successfully',
            'country' => $country,
        ], 201);
    }

    /**
     * Update a country.
     */
    public function updateCountry(Request $request, Country $country): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:3|unique:countries,code,' . $country->id,
            'phone_code' => 'nullable|string|max:10',
            'currency_code' => 'required|string|size:3',
            'is_active' => 'boolean',
            'flag_icon' => 'nullable|string|max:50',
        ]);

        $country->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'phone_code' => $request->phone_code,
            'currency_code' => strtoupper($request->currency_code),
            'is_active' => $request->is_active ?? true,
            'flag_icon' => $request->flag_icon,
        ]);

        return response()->json([
            'message' => 'Country updated successfully',
            'country' => $country->refresh(),
        ]);
    }

    /**
     * Delete a country.
     */
    public function deleteCountry(Country $country): JsonResponse
    {
        $this->checkAdminAccess();

        // Check if country has associated states or vendors
        if ($country->states()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete country with associated states',
            ], 400);
        }

        $country->delete();

        return response()->json([
            'message' => 'Country deleted successfully',
        ]);
    }

    /**
     * Display states for a specific country.
     */
    public function states(Request $request, Country $country): View
    {
        $this->checkAdminAccess();

        $query = State::where('country_id', $country->id);

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $states = $query->orderBy('name')->paginate(25);

        return $this->adminView('admin.locations.states', [
            'states' => $states,
            'country' => $country,
        ]);
    }

    /**
     * Store a new state.
     */
    public function storeState(Request $request, Country $country): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $state = State::create([
            'name' => $request->name,
            'country_id' => $country->id,
        ]);

        return response()->json([
            'message' => 'State created successfully',
            'state' => $state,
        ], 201);
    }

    /**
     * Update a state.
     */
    public function updateState(Request $request, State $state): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $state->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'State updated successfully',
            'state' => $state->refresh(),
        ]);
    }

    /**
     * Delete a state.
     */
    public function deleteState(State $state): JsonResponse
    {
        $this->checkAdminAccess();

        // Check if state has associated cities or vendors
        if ($state->cities()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete state with associated cities',
            ], 400);
        }

        $state->delete();

        return response()->json([
            'message' => 'State deleted successfully',
        ]);
    }

    /**
     * Display cities for a specific state.
     */
    public function cities(Request $request, State $state): View
    {
        $this->checkAdminAccess();

        $query = City::where('state_id', $state->id);

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $cities = $query->orderBy('name')->paginate(25);

        return $this->adminView('admin.locations.cities', [
            'cities' => $cities,
            'state' => $state,
            'country' => $state->country,
        ]);
    }

    /**
     * Store a new city.
     */
    public function storeCity(Request $request, State $state): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        $city = City::create([
            'name' => $request->name,
            'state_id' => $state->id,
        ]);

        return response()->json([
            'message' => 'City created successfully',
            'city' => $city,
        ], 201);
    }

    /**
     * Update a city.
     */
    public function updateCity(Request $request, City $city): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $city->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'City updated successfully',
            'city' => $city->refresh(),
        ]);
    }

    /**
     * Delete a city.
     */
    public function deleteCity(City $city): JsonResponse
    {
        $this->checkAdminAccess();

        // Check if city has associated vendors
        if ($city->vendors()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete city with associated vendors',
            ], 400);
        }

        $city->delete();

        return response()->json([
            'message' => 'City deleted successfully',
        ]);
    }
}