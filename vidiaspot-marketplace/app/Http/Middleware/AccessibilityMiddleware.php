<?php

namespace App\Http\Middleware;

use App\Services\AccessibilityService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class AccessibilityMiddleware
{
    protected AccessibilityService $accessibilityService;

    public function __construct(AccessibilityService $accessibilityService)
    {
        $this->accessibilityService = $accessibilityService;
    }

    /**
     * Handle an incoming request for accessibility enhancements.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process HTML responses
        if ($response->headers->get('Content-Type') && 
            strpos($response->headers->get('Content-Type'), 'text/html') !== false) {
            
            $content = $response->getContent();
            
            // Add skip link to the beginning of the body
            $skipLink = $this->accessibilityService->generateSkipLink('#main-content', 'Skip to main content');
            $content = preg_replace('/<body[^>]*>/', '$0' . $skipLink, $content);
            
            // Add proper landmark roles to main sections (if not already present)
            $content = $this->addLandmarkRoles($content);
            
            // Update the response content
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * Add ARIA landmark roles to main sections
     */
    private function addLandmarkRoles(string $content): string
    {
        // Add role="banner" to header if not present
        $content = preg_replace('/<header(?![^>]*role=)/', '<header role="banner"', $content);
        
        // Add role="navigation" to navigation if not present
        $content = preg_replace('/<nav(?![^>]*role=)/', '<nav role="navigation"', $content);
        
        // Add role="main" to main content area if not present
        $content = preg_replace('/<main(?![^>]*role=)/', '<main role="main"', $content);
        
        // Add role="contentinfo" to footer if not present
        $content = preg_replace('/<footer(?![^>]*role=)/', '<footer role="contentinfo"', $content);
        
        // Add role="search" to search forms if not present
        $content = preg_replace('/<form[^>]*search[^>]*(?![^>]*role=)/i', function($matches) {
            return str_replace('<form', '<form role="search"', $matches[0]);
        }, $content);
        
        return $content;
    }
}