<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TwoFactorAuthService
{
    /**
     * Send 2FA code via SMS
     */
    public function sendSmsCode(User $user, string $code): bool
    {
        // This is a placeholder implementation - in a real application, 
        // you would integrate with an SMS provider like Twilio
        $phoneNumber = $user->phone;
        
        if (empty($phoneNumber)) {
            return false;
        }
        
        // Log the code for testing purposes (in production, send via SMS API)
        \Log::info("2FA SMS code for user {$user->id}: {$code}");
        
        // Example using a hypothetical SMS service
        // return $this->sendSmsViaProvider($phoneNumber, "Your 2FA code is: {$code}");
        
        return true;
    }

    /**
     * Send 2FA code via Email
     */
    public function sendEmailCode(User $user, string $code): bool
    {
        // In a real application, you would send an email with the code
        \Log::info("2FA Email code for user {$user->id}: {$code}");
        
        // Example email sending implementation:
        // Mail::raw("Your 2FA code is: {$code}", function ($message) use ($user) {
        //     $message->to($user->email)->subject('Your 2FA Code');
        // });
        
        return true;
    }

    /**
     * Generate a random 6-digit code
     */
    public function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Store the 2FA code temporarily (in cache or database)
     */
    public function storeCode(User $user, string $code, int $expiresInMinutes = 5): void
    {
        // Store in cache with expiration
        \Cache::put("2fa_code_{$user->id}", $code, now()->addMinutes($expiresInMinutes));
    }

    /**
     * Validate the 2FA code for a user
     */
    public function validateCode(User $user, string $code): bool
    {
        $storedCode = \Cache::get("2fa_code_{$user->id}");
        
        if ($storedCode && hash_equals($storedCode, $code)) {
            // Delete the code after successful validation
            \Cache::forget("2fa_code_{$user->id}");
            return true;
        }
        
        return false;
    }

    /**
     * Send backup codes to user
     */
    public function generateBackupCodes(User $user, int $count = 10): array
    {
        $backupCodes = [];

        for ($i = 0; $i < $count; $i++) {
            $backupCodes[] = Str::random(16); // Generate random backup codes
        }

        // Store backup codes for the user
        $user->backup_codes = $backupCodes;
        $user->save();

        return $backupCodes;
    }

    /**
     * Validate a backup code for a user
     */
    public function validateBackupCode(User $user, string $code): bool
    {
        $backupCodes = $user->backup_codes;

        if (!$backupCodes) {
            return false;
        }

        $codeIndex = array_search($code, $backupCodes);

        if ($codeIndex !== false) {
            // Remove the used backup code
            unset($backupCodes[$codeIndex]);
            $user->backup_codes = array_values($backupCodes);
            $user->save();

            return true;
        }

        return false;
    }
}