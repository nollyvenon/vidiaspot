<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class EncryptSensitiveData
{
    /**
     * List of sensitive fields that should be encrypted
     */
    private array $sensitiveFields = [
        'email', 'phone', 'address', 'payment_info', 'credit_card', 'ssn', 'id_number'
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For API requests, we might want to encrypt sensitive fields
        if ($request->is('api/*')) {
            // Check if this is a request containing sensitive data
            foreach ($this->sensitiveFields as $field) {
                if ($request->has($field)) {
                    // Encrypt sensitive data before processing
                    $request->merge([
                        $field => Crypt::encrypt($request->get($field))
                    ]);
                }
            }
        }

        return $next($request);
    }
}