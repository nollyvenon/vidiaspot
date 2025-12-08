<?php

namespace App\Services;

use App\Models\SearchLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrendingSearchService
{
    public function getTrendingSearches($limit = 10, $days = 7)
    {
        return SearchLog::select('query', DB::raw('COUNT(*) as count'))
            ->where('searched_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('query')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTrendingSearchesByCategory($category, $limit = 5, $days = 7)
    {
        // This would contain category-specific trending searches
        // For now we'll return general trending searches related to the category
        return SearchLog::select('query', DB::raw('COUNT(*) as count'))
            ->where('searched_at', '>=', Carbon::now()->subDays($days))
            ->where('query', 'LIKE', '%' . $category . '%')
            ->groupBy('query')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function addSearchLog($query, $location = null, $userId = null)
    {
        return SearchLog::create([
            'query' => $query,
            'location' => $location,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'searched_at' => now(),
        ]);
    }
}