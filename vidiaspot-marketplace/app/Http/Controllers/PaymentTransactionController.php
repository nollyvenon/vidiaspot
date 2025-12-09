<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentTransactionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $transactions = PaymentTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $transaction = PaymentTransaction::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_gateway' => 'required|string',
        ]);

        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'ad_id' => $request->ad_id,
            'transaction_id' => 'TXN_' . uniqid() . '_' . time(),
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'payment_gateway' => $request->payment_gateway,
            'currency' => 'NGN',
            'status' => 'pending',
            'payment_details' => $request->payment_details ?: [],
            'metadata' => [
                'source' => 'web',
                'user_ip' => $request->ip(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'data' => $transaction
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        $transaction = PaymentTransaction::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $transaction->update($request->only([
            'status',
            'provider_reference',
            'payment_details',
            'processed_at',
            'confirmed_at'
        ]));

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        
        $transaction = PaymentTransaction::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully'
        ]);
    }

    public function forAd($adId)
    {
        $user = Auth::user();
        
        $ad = Ad::findOrFail($adId);
        
        // Only ad owner can view transactions for their ad
        if ($ad->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view transactions for this ad'
            ], 403);
        }

        $transactions = PaymentTransaction::where('ad_id', $adId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function webhook(Request $request)
    {
        // This would handle payment gateway webhooks to update transaction status
        $payload = $request->all();
        
        // Verify the webhook came from a trusted source
        // This is a simplified verification - implement proper webhook verification
        
        $providerReference = $payload['provider_reference'] ?? null;
        
        if ($providerReference) {
            $transaction = PaymentTransaction::where('provider_reference', $providerReference)->first();
            
            if ($transaction) {
                $transaction->update([
                    'status' => $payload['status'] ?? 'pending',
                    'confirmed_at' => now(),
                    'payment_details' => array_merge($transaction->payment_details ?? [], $payload['payment_details'] ?? [])
                ]);
                
                return response()->json(['success' => true]);
            }
        }
        
        return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
    }
}