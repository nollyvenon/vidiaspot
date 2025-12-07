<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    /**
     * Display currencies management page.
     */
    public function index(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Currency::query();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('code', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('active')) {
            $isActive = $request->active === 'yes';
            $query->where('is_active', $isActive);
        }

        $currencies = $query->orderBy('name')->paginate(25);

        return $this->adminView('admin.currencies.index', [
            'currencies' => $currencies,
        ]);
    }

    /**
     * Store a new currency.
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:3|unique:currencies,code',
            'symbol' => 'required|string|max:10',
            'is_active' => 'boolean',
            'format' => 'nullable|string|max:50',
            'thousand_separator' => 'nullable|string|max:5',
            'decimal_separator' => 'nullable|string|max:5',
            'decimal_places' => 'nullable|integer|min:0|max:6',
        ]);

        $currency = Currency::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'symbol' => $request->symbol,
            'is_active' => $request->is_active ?? true,
            'format' => $request->format ?? '{{symbol}}{{amount}}',
            'thousand_separator' => $request->thousand_separator ?? ',',
            'decimal_separator' => $request->decimal_separator ?? '.',
            'decimal_places' => $request->decimal_places ?? 2,
        ]);

        return response()->json([
            'message' => 'Currency created successfully',
            'currency' => $currency,
        ], 201);
    }

    /**
     * Update a currency.
     */
    public function update(Request $request, Currency $currency): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:3|unique:currencies,code,' . $currency->id,
            'symbol' => 'required|string|max:10',
            'is_active' => 'boolean',
            'format' => 'nullable|string|max:50',
            'thousand_separator' => 'nullable|string|max:5',
            'decimal_separator' => 'nullable|string|max:5',
            'decimal_places' => 'nullable|integer|min:0|max:6',
        ]);

        $currency->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'symbol' => $request->symbol,
            'is_active' => $request->is_active ?? true,
            'format' => $request->format ?? '{{symbol}}{{amount}}',
            'thousand_separator' => $request->thousand_separator ?? ',',
            'decimal_separator' => $request->decimal_separator ?? '.',
            'decimal_places' => $request->decimal_places ?? 2,
        ]);

        return response()->json([
            'message' => 'Currency updated successfully',
            'currency' => $currency->refresh(),
        ]);
    }

    /**
     * Delete a currency.
     */
    public function destroy(Currency $currency): JsonResponse
    {
        $this->checkAdminAccess();

        // Don't allow deletion of currencies that are in use
        if ($currency->code === 'NGN') { // Default currency
            return response()->json([
                'error' => 'Cannot delete default currency',
            ], 400);
        }

        // Check if currency is used in any transactions or records
        // This is a simplified check - in a real app, you'd check for references in payments, products, etc.
        if ($currency->exchangeRatesFrom()->count() > 0 || $currency->exchangeRatesTo()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete currency with exchange rates',
            ], 400);
        }

        $currency->delete();

        return response()->json([
            'message' => 'Currency deleted successfully',
        ]);
    }

    /**
     * Display exchange rates management page.
     */
    public function exchangeRates(Request $request): View
    {
        $this->checkAdminAccess();

        $query = ExchangeRate::with(['fromCurrency', 'toCurrency']);

        $exchangeRates = $query->orderBy('from_currency_code')->orderBy('to_currency_code')->paginate(25);

        return $this->adminView('admin.currencies.exchange-rates', [
            'exchangeRates' => $exchangeRates,
        ]);
    }

    /**
     * Store a new exchange rate.
     */
    public function storeExchangeRate(Request $request): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'from_currency_code' => 'required|exists:currencies,code',
            'to_currency_code' => 'required|exists:currencies,code',
            'rate' => 'required|numeric|min:0',
        ]);

        // Prevent creating exchange rate for same currency
        if ($request->from_currency_code === $request->to_currency_code) {
            return response()->json([
                'error' => 'Cannot create exchange rate for the same currency',
            ], 400);
        }

        $exchangeRate = ExchangeRate::updateOrCreate(
            [
                'from_currency_code' => $request->from_currency_code,
                'to_currency_code' => $request->to_currency_code,
            ],
            [
                'rate' => $request->rate,
                'updated_by' => auth()->id(),
            ]
        );

        return response()->json([
            'message' => 'Exchange rate created/updated successfully',
            'exchange_rate' => $exchangeRate,
        ], 201);
    }

    /**
     * Update an exchange rate.
     */
    public function updateExchangeRate(Request $request, ExchangeRate $exchangeRate): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'rate' => 'required|numeric|min:0',
        ]);

        $exchangeRate->update([
            'rate' => $request->rate,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Exchange rate updated successfully',
            'exchange_rate' => $exchangeRate->refresh(),
        ]);
    }

    /**
     * Delete an exchange rate.
     */
    public function destroyExchangeRate(ExchangeRate $exchangeRate): JsonResponse
    {
        $this->checkAdminAccess();

        $exchangeRate->delete();

        return response()->json([
            'message' => 'Exchange rate deleted successfully',
        ]);
    }
}