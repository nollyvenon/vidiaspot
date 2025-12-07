<?php

namespace App\Http\Controllers;

use App\Services\ElasticsearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    protected ElasticsearchService $elasticsearch;

    public function __construct(ElasticsearchService $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    /**
     * Perform advanced search
     */
    public function search(Request $request): JsonResponse
    {
        $criteria = [
            'search' => $request->input('q'),
            'category' => $request->input('category'),
            'location' => $request->input('location'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'condition' => $request->input('condition'),
            'user_id' => $request->input('user_id'),
        ];

        $from = ($request->input('page', 1) - 1) * $request->input('per_page', 10);
        $size = $request->input('per_page', 10);

        $results = $this->elasticsearch->search($criteria, $from, $size);

        return response()->json([
            'data' => $results['hits']['hits'] ?? [],
            'total' => $results['hits']['total']['value'] ?? 0,
            'aggregations' => $results['aggregations'] ?? [],
        ]);
    }

    /**
     * Search with aggregations (faceted search)
     */
    public function searchWithFacets(Request $request): JsonResponse
    {
        $criteria = [
            'search' => $request->input('q'),
            'category' => $request->input('category'),
            'location' => $request->input('location'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'condition' => $request->input('condition'),
        ];

        $results = $this->elasticsearch->searchWithAggregations($criteria);

        return response()->json([
            'data' => $results['hits']['hits'] ?? [],
            'total' => $results['hits']['total']['value'] ?? 0,
            'aggregations' => $results['aggregations'] ?? [],
        ]);
    }

    /**
     * Simple search endpoint
     */
    public function simpleSearch(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        if (empty($query)) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $results = $this->elasticsearch->simpleSearch($query);

        return response()->json([
            'data' => $results['hits']['hits'] ?? [],
            'total' => $results['hits']['total']['value'] ?? 0,
        ]);
    }

    /**
     * Get related products
     */
    public function getRelatedProducts(Request $request, int $productId): JsonResponse
    {
        $size = $request->input('size', 5);
        $results = $this->elasticsearch->getRelatedProducts($productId, $size);

        return response()->json([
            'data' => $results['hits']['hits'] ?? [],
            'total' => count($results['hits']['hits'] ?? []),
        ]);
    }

    /**
     * Index a single ad document
     */
    public function indexAd(Request $request): JsonResponse
    {
        $ad = $request->all();
        $result = $this->elasticsearch->indexDocument($ad);

        return response()->json($result);
    }

    /**
     * Bulk index ads
     */
    public function bulkIndexAds(Request $request): JsonResponse
    {
        $ads = $request->input('ads', []);
        $result = $this->elasticsearch->bulkIndexDocuments($ads);

        return response()->json($result);
    }

    /**
     * Check if Elasticsearch is available
     */
    public function healthCheck(): JsonResponse
    {
        try {
            $stats = $this->elasticsearch->getIndexStats();
            return response()->json([
                'status' => 'healthy',
                'index_exists' => $this->elasticsearch->indexExists(),
                'document_count' => $stats['_all']['primaries']['docs']['count'] ?? 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}