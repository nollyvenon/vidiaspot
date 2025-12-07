<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentGatewaysController extends Controller
{
    /**
     * Get all payment gateway configurations.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $gateways = Setting::where('section', 'payment')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $gateways,
            'message' => 'Payment gateway configurations'
        ]);
    }

    /**
     * Update payment gateway configuration.
     */
    public function update(Request $request, string $gateway): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'config' => 'required|array',
            'config.*' => 'string',
        ]);

        $gatewaySettings = [
            'paystack' => [
                'secret_key' => ['label' => 'Paystack Secret Key', 'type' => 'password', 'required' => true],
                'public_key' => ['label' => 'Paystack Public Key', 'type' => 'text', 'required' => true],
                'enabled' => ['label' => 'Enable Paystack', 'type' => 'boolean', 'required' => false],
            ],
            'flutterwave' => [
                'secret_key' => ['label' => 'Flutterwave Secret Key', 'type' => 'password', 'required' => true],
                'public_key' => ['label' => 'Flutterwave Public Key', 'type' => 'text', 'required' => true],
                'encryption_key' => ['label' => 'Flutterwave Encryption Key', 'type' => 'text', 'required' => true],
                'enabled' => ['label' => 'Enable Flutterwave', 'type' => 'boolean', 'required' => false],
            ],
            'stripe' => [
                'secret_key' => ['label' => 'Stripe Secret Key', 'type' => 'password', 'required' => true],
                'publishable_key' => ['label' => 'Stripe Publishable Key', 'type' => 'text', 'required' => true],
                'enabled' => ['label' => 'Enable Stripe', 'type' => 'boolean', 'required' => false],
            ],
            'paypal' => [
                'client_id' => ['label' => 'PayPal Client ID', 'type' => 'text', 'required' => true],
                'client_secret' => ['label' => 'PayPal Client Secret', 'type' => 'password', 'required' => true],
                'mode' => ['label' => 'PayPal Mode', 'type' => 'select', 'options' => ['sandbox' => 'Sandbox', 'live' => 'Live'], 'required' => false],
                'enabled' => ['label' => 'Enable PayPal', 'type' => 'boolean', 'required' => false],
            ],
            'mpesa' => [
                'consumer_key' => ['label' => 'M-Pesa Consumer Key', 'type' => 'text', 'required' => true],
                'consumer_secret' => ['label' => 'M-Pesa Consumer Secret', 'type' => 'password', 'required' => true],
                'business_short_code' => ['label' => 'M-Pesa Business Short Code', 'type' => 'text', 'required' => true],
                'passkey' => ['label' => 'M-Pesa Passkey', 'type' => 'text', 'required' => true],
                'enabled' => ['label' => 'Enable M-Pesa', 'type' => 'boolean', 'required' => false],
            ],
            'sofort' => [
                'customer_id' => ['label' => 'Sofort Customer ID', 'type' => 'text', 'required' => true],
                'project_id' => ['label' => 'Sofort Project ID', 'type' => 'text', 'required' => true],
                'api_key' => ['label' => 'Sofort API Key', 'type' => 'password', 'required' => true],
                'enabled' => ['label' => 'Enable Sofort', 'type' => 'boolean', 'required' => false],
            ],
            'bank_transfer' => [
                'account_name' => ['label' => 'Account Name', 'type' => 'text', 'required' => true],
                'account_number' => ['label' => 'Account Number', 'type' => 'text', 'required' => true],
                'bank_name' => ['label' => 'Bank Name', 'type' => 'text', 'required' => true],
                'instructions' => ['label' => 'Payment Instructions', 'type' => 'textarea', 'required' => false],
                'enabled' => ['label' => 'Enable Bank Transfer', 'type' => 'boolean', 'required' => false],
            ],
        ];

        if (!isset($gatewaySettings[$gateway])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment gateway'
            ], 400);
        }

        // Update all settings for this gateway
        foreach ($gatewaySettings[$gateway] as $key => $config) {
            if (isset($request->config[$key])) {
                $setting = Setting::updateOrCreate(
                    [
                        'key' => $gateway . '_' . $key
                    ],
                    [
                        'value' => $request->config[$key],
                        'type' => $config['type'],
                        'section' => 'payment',
                        'name' => $config['label'],
                        'description' => 'Configuration for ' . $gateway . ' ' . $key,
                        'is_active' => true,
                        'updated_by' => $user->id,
                        'order' => 0,
                    ]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment gateway configuration updated successfully'
        ]);
    }

    /**
     * Enable/disable a payment gateway.
     */
    public function toggleStatus(Request $request, string $gateway): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $setting = Setting::firstOrCreate(
            [
                'key' => $gateway . '_enabled'
            ],
            [
                'value' => 'false',
                'type' => 'boolean',
                'section' => 'payment',
                'name' => Str::title($gateway) . ' Enabled',
                'description' => 'Enable/disable ' . $gateway . ' gateway',
                'is_active' => true,
                'updated_by' => $user->id,
            ]
        );

        $setting->update([
            'value' => $request->enabled ? 'true' : 'false',
            'updated_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $setting,
            'message' => 'Payment gateway status updated successfully'
        ]);
    }

    /**
     * Get supported payment gateways.
     */
    public function supported(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $supportedGateways = [
            'paystack' => 'Paystack',
            'flutterwave' => 'Flutterwave',
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'mpesa' => 'M-Pesa',
            'sofort' => 'Sofort',
            'bank_transfer' => 'Bank Transfer',
        ];

        return response()->json([
            'success' => true,
            'data' => $supportedGateways,
            'message' => 'Supported payment gateways'
        ]);
    }
}
