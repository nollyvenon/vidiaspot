<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BlockchainVerification;
use App\Services\BlockchainIdentityVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockchainVerificationController extends Controller
{
    private BlockchainIdentityVerificationService $verificationService;

    public function __construct()
    {
        $this->verificationService = new BlockchainIdentityVerificationService();
    }

    /**
     * Initiate blockchain identity verification for the authenticated user.
     */
    public function initiateVerification(Request $request)
    {
        $user = Auth::user();
        
        // Validate request data
        $request->validate([
            'document_type' => 'required|in:passport,driver_license,national_id',
            'document_number' => 'required|string|max:255',
            'document_image' => 'nullable|image|max:10240', // Max 10MB
        ]);
        
        // Process document image if provided
        $documentPath = null;
        if ($request->hasFile('document_image')) {
            $documentPath = $request->file('document_image')->store('verification-documents', 'public');
        }
        
        // Initiate verification
        $result = $this->verificationService->initiateIdentityVerification($user);
        
        // Store verification record in database
        $verification = BlockchainVerification::create([
            'user_id' => $user->id,
            'verification_request_id' => $result['verification_id'],
            'transaction_hash' => $result['transaction_hash'],
            'status' => 'pending',
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'identity_data' => [
                'email' => $user->email,
                'name' => $user->name,
                'phone' => $user->phone,
                'document_path' => $documentPath,
            ],
            'blockchain_network' => 'ethereum', // Default for now
        ]);
        
        return response()->json([
            'message' => 'Identity verification initiated successfully',
            'verification_id' => $result['verification_id'],
            'transaction_hash' => $result['transaction_hash'],
            'status' => $verification->status,
            'verification' => $verification
        ]);
    }

    /**
     * Get the verification status for the authenticated user.
     */
    public function getStatus()
    {
        $user = Auth::user();
        
        $verification = BlockchainVerification::where('user_id', $user->id)
            ->latest()
            ->first();
        
        if (!$verification) {
            return response()->json([
                'status' => 'not_found',
                'verified' => false,
                'message' => 'No verification record found'
            ]);
        }
        
        // Update status by checking blockchain
        $blockchainStatus = $this->verificationService->verifyIdentityOnBlockchain($user);
        
        return response()->json([
            'status' => $verification->status,
            'verified' => $verification->isValid(),
            'verification' => $verification,
            'blockchain_check' => $blockchainStatus
        ]);
    }

    /**
     * Verify a specific transaction hash.
     */
    public function verifyTransaction(Request $request)
    {
        $request->validate([
            'transaction_hash' => 'required|string',
        ]);
        
        $result = $this->verificationService->verifyTransaction($request->transaction_hash);
        
        return response()->json($result);
    }

    /**
     * Get all verifications for the authenticated user.
     */
    public function getUserVerifications()
    {
        $user = Auth::user();
        
        $verifications = BlockchainVerification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'verifications' => $verifications
        ]);
    }

    /**
     * Upload additional verification documents.
     */
    public function uploadDocuments(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'document_type' => 'required|in:selfie,utility_bill,address_proof',
            'document_file' => 'required|file|max:10240', // Max 10MB
        ]);
        
        $documentPath = $request->file('document_file')->store('verification-documents', 'public');
        
        // Here you would associate the additional document with the user's verification record
        // This is a simplified implementation
        
        return response()->json([
            'message' => 'Document uploaded successfully',
            'document_path' => $documentPath
        ]);
    }
}