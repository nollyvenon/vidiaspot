<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\TrendingSearchService;
use Symfony\Component\HttpFoundation\Response;

class LogSearches
{
    protected $trendingSearchService;

    public function __construct(TrendingSearchService $trendingSearchService)
    {
        $this->trendingSearchService = $trendingSearchService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log search queries for trending searches
        if ($request->is('search') && $request->has('q')) {
            $query = $request->get('q');
            $location = $request->get('location');
            $userId = auth()->check() ? auth()->id() : null;

            $this->trendingSearchService->addSearchLog($query, $location, $userId);
        }

        return $response;
    }
}
