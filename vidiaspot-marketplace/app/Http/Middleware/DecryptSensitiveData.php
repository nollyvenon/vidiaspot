<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class DecryptSensitiveData
{
    /**
     * List of sensitive fields that should be decrypted
     */
    private array $sensitiveFields = [
        'email', 'phone', 'address', 'payment_info', 'credit_card', 'ssn', 'id_number'
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // For API responses, decrypt sensitive fields if they were encrypted
        if ($request->is('api/*') && $response->headers->get('Content-Type') === 'application/json') {
            $content = $response->getContent();
            $data = json_decode($content, true);

            if ($data) {
                $this->decryptSensitiveFields($data);
                $response->setContent(json_encode($data));
            }
        }

        return $response;
    }

    /**
     * Recursively decrypt sensitive fields in the response data
     */
    private function decryptSensitiveFields(array &$data): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->decryptSensitiveFields($data[$key]);
            } elseif (is_string($value) && in_array($key, $this->sensitiveFields)) {
                // Attempt to decrypt the value
                try {
                    $decrypted = Crypt::decrypt($value);
                    $data[$key] = $decrypted;
                } catch (\Exception $e) {
                    // If decryption fails, leave the value as is
                    continue;
                }
            }
        }
    }
}