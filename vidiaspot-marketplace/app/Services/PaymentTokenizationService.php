<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class PaymentTokenizationService
{
    /**
     * Tokenize sensitive payment data
     */
    public function tokenizePaymentData(array $paymentData): string
    {
        // Only tokenize sensitive fields
        $sensitiveFields = ['card_number', 'cvv', 'expiry_date', 'cardholder_name', 'account_number'];
        
        $filteredData = [];
        foreach ($sensitiveFields as $field) {
            if (isset($paymentData[$field])) {
                $filteredData[$field] = $paymentData[$field];
            }
        }
        
        // Encrypt the sensitive data
        $encryptedData = Crypt::encrypt(json_encode($filteredData));
        
        // Create a unique token
        $token = 'ptk_' . Str::random(32) . '_' . time();
        
        // Store the encrypted data using the token as key
        // In a real application, you'd store this in a secure database
        \Cache::put("payment_token_{$token}", $encryptedData, now()->addDays(365)); // Store for one year
        
        return $token;
    }

    /**
     * Detokenize (decrypt) payment data using the token
     */
    public function detokenizePaymentData(string $token): ?array
    {
        // Check if the token exists in cache
        $encryptedData = \Cache::get("payment_token_{$token}");
        
        if (!$encryptedData) {
            Log::warning('Invalid or expired payment token attempted', ['token' => $token]);
            return null;
        }
        
        try {
            // Decrypt the data
            $decryptedData = Crypt::decrypt($encryptedData);
            return json_decode($decryptedData, true);
        } catch (\Exception $e) {
            Log::error('Error decrypting payment data', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            
            // Remove the invalid token
            \Cache::forget("payment_token_{$token}");
            return null;
        }
    }

    /**
     * Delete a payment token (for security reasons)
     */
    public function deleteToken(string $token): bool
    {
        $key = "payment_token_{$token}";
        return \Cache::forget($key);
    }

    /**
     * Create a payment token that expires after a single use
     */
    public function tokenizeForSingleUse(array $paymentData): string
    {
        // Tokenize the data
        $sensitiveFields = ['card_number', 'cvv', 'expiry_date', 'cardholder_name', 'account_number'];
        
        $filteredData = [];
        foreach ($sensitiveFields as $field) {
            if (isset($paymentData[$field])) {
                $filteredData[$field] = $paymentData[$field];
            }
        }
        
        $encryptedData = Crypt::encrypt(json_encode($filteredData));
        $token = 'ptk_single_' . Str::random(32) . '_' . time();
        
        // Store with short expiration for single use
        \Cache::put("payment_token_{$token}", $encryptedData, now()->addMinutes(10)); // 10 minutes for single use
        
        return $token;
    }

    /**
     * Detokenize and mark as used (for single-use tokens)
     */
    public function detokenizeSingleUse(string $token): ?array
    {
        $result = $this->detokenizePaymentData($token);
        
        if ($result) {
            // Delete the token after single use
            $this->deleteToken($token);
        }
        
        return $result;
    }

    /**
     * Validate payment token format
     */
    public function isValidToken(string $token): bool
    {
        // Check if token matches expected format
        return preg_match('/^ptk_[a-zA-Z0-9_]+$/', $token) === 1;
    }

    /**
     * Tokenize card data specifically
     */
    public function tokenizeCardData(array $cardData): array
    {
        $tokenizedCard = [];
        
        // Only tokenize the card number, leaving other fields as needed
        if (isset($cardData['number'])) {
            $tokenizedCard['token'] = $this->tokenizePaymentData([
                'card_number' => $cardData['number'],
                'cvv' => $cardData['cvv'] ?? null,
                'expiry_date' => $cardData['expiry'] ?? $cardData['exp_month'] . '/' . $cardData['exp_year'] ?? null,
                'cardholder_name' => $cardData['name'] ?? null,
            ]);
            
            // Return a masked version of the card number for display
            $tokenizedCard['display_number'] = $this->maskCardNumber($cardData['number']);
        }
        
        return $tokenizedCard;
    }

    /**
     * Mask a card number for display
     */
    private function maskCardNumber(string $cardNumber): string
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber); // Remove non-numeric characters
        
        if (strlen($cardNumber) < 8) {
            return str_repeat('*', strlen($cardNumber));
        }
        
        $first = substr($cardNumber, 0, 6);
        $last = substr($cardNumber, -4);
        $middle = str_repeat('*', strlen($cardNumber) - 10);
        
        return $first . $middle . $last;
    }
}