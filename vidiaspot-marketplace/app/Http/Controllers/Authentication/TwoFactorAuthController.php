<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Illuminate\Support\Str;

class TwoFactorAuthController extends Controller
{
    /**
     * Enable 2FA for the authenticated user.
     */
    public function enable2FA(Request $request)
    {
        $user = Auth::user();
        
        if ($user->google2fa_enabled) {
            return response()->json(['message' => '2FA is already enabled'], 400);
        }
        
        // Generate a new secret key
        $secret = $this->generateSecretKey();
        
        // Save the secret to the user
        $user->google2fa_secret = $secret;
        $user->save();
        
        // Get the QR code URL for the user
        $qrCodeUrl = $user->getGoogle2faQrCodeUrl();
        
        return response()->json([
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'message' => '2FA setup initiated. Scan the QR code with your authenticator app.'
        ]);
    }

    /**
     * Confirm 2FA setup by validating the current code.
     */
    public function confirm2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);
        
        $user = Auth::user();
        
        if (!$user->google2fa_secret) {
            return response()->json(['message' => '2FA not properly initialized'], 400);
        }
        
        // Validate the provided code
        $authenticator = new Authenticator($user);
        $isValid = $authenticator->verifyAndStore($request->code);
        
        if ($isValid) {
            // Enable 2FA for the user
            $user->google2fa_enabled = true;
            $user->save();
            
            return response()->json([
                'message' => '2FA successfully enabled',
                'enabled' => true
            ]);
        }
        
        return response()->json(['message' => 'Invalid 2FA code'], 400);
    }

    /**
     * Disable 2FA for the authenticated user.
     */
    public function disable2FA(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->google2fa_enabled) {
            return response()->json(['message' => '2FA is not enabled'], 400);
        }
        
        // Disable 2FA for the user
        $user->google2fa_enabled = false;
        $user->google2fa_secret = null;
        $user->save();
        
        return response()->json([
            'message' => '2FA successfully disabled',
            'enabled' => false
        ]);
    }

    /**
     * Verify 2FA during login.
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);
        
        $user = Auth::user();
        
        if (!$user || !$user->google2fa_enabled) {
            return response()->json(['message' => '2FA not enabled for this user'], 400);
        }
        
        $authenticator = new Authenticator($user);
        $isValid = $authenticator->verifyAndStore($request->code);
        
        if ($isValid) {
            return response()->json(['message' => '2FA verified', 'valid' => true]);
        }
        
        return response()->json(['message' => 'Invalid 2FA code'], 400);
    }

    /**
     * Request SMS 2FA code for the authenticated user.
     */
    public function requestSmsCode()
    {
        $user = Auth::user();
        $twoFactorService = new TwoFactorAuthService();

        // Generate and store a new code
        $code = $twoFactorService->generateCode();
        $twoFactorService->storeCode($user, $code);

        // Send the code via SMS
        $sent = $twoFactorService->sendSmsCode($user, $code);

        if ($sent) {
            return response()->json([
                'message' => '2FA code sent via SMS',
                'sent' => true
            ]);
        }

        return response()->json([
            'message' => 'Failed to send SMS code',
            'sent' => false
        ], 500);
    }

    /**
     * Request Email 2FA code for the authenticated user.
     */
    public function requestEmailCode()
    {
        $user = Auth::user();
        $twoFactorService = new TwoFactorAuthService();

        // Generate and store a new code
        $code = $twoFactorService->generateCode();
        $twoFactorService->storeCode($user, $code);

        // Send the code via email
        $sent = $twoFactorService->sendEmailCode($user, $code);

        if ($sent) {
            return response()->json([
                'message' => '2FA code sent via email',
                'sent' => true
            ]);
        }

        return response()->json([
            'message' => 'Failed to send email code',
            'sent' => false
        ], 500);
    }

    /**
     * Get current 2FA status for the authenticated user.
     */
    public function getStatus()
    {
        $user = Auth::user();

        return response()->json([
            'enabled' => $user->google2fa_enabled,
            'has_secret' => !empty($user->google2fa_secret),
            'backup_codes_available' => !empty($user->backup_codes)
        ]);
    }

    /**
     * Generate backup codes for the authenticated user.
     */
    public function generateBackupCodes()
    {
        $user = Auth::user();
        $twoFactorService = new TwoFactorAuthService();

        $backupCodes = $twoFactorService->generateBackupCodes($user);

        return response()->json([
            'message' => 'Backup codes generated successfully',
            'backup_codes' => $backupCodes
        ]);
    }

    /**
     * Verify backup code during login.
     */
    public function verifyBackupCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $user = Auth::user();
        $twoFactorService = new TwoFactorAuthService();

        $isValid = $twoFactorService->validateBackupCode($user, $request->code);

        if ($isValid) {
            return response()->json([
                'message' => 'Backup code verified successfully',
                'valid' => true
            ]);
        }

        return response()->json([
            'message' => 'Invalid or already used backup code',
            'valid' => false
        ], 400);
    }

    /**
     * Generate a secret key for 2FA.
     */
    private function generateSecretKey(): string
    {
        return Str::random(16);
    }
}