<?php

namespace App\Services\Search;

use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;
use App\Models\Ad;

/**
 * Service for advanced search functionality using Elasticsearch
 */
class ElasticsearchService
{
    protected $client;
    
    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([config('elasticsearch.host', env('ELASTICSEARCH_HOST', 'http://localhost:9200'))])
            ->build();
    }
    
    /**
     * Index an ad document in Elasticsearch
     */
    public function indexAd(Ad $ad): bool
    {
        try {
            $params = [
                'index' => config('elasticsearch.index', env('ELASTICSEARCH_INDEX', 'vidiaspot_marketplace')),
                'id' => $ad->id,
                'body' => [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'price' => $ad->price,
                    'location' => $ad->location,
                    'category_id' => $ad->category_id,
                    'category_name' => $ad->category->name ?? null,
                    'user_id' => $ad->user_id,
                    'status' => $ad->status,
                    'created_at' => $ad->created_at->toISOString(),
                    'updated_at' => $ad->updated_at->toISOString(),
                    'tags' => $ad->tags ?? [],
                ]
            ];
            
            $response = $this->client->index($params);
            return isset($response['result']) && in_array($response['result'], ['created', 'updated']);
        } catch (\Exception $e) {
            Log::error('Elasticsearch index error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search ads in Elasticsearch
     */
    public function searchAds(array $queryData): array
    {
        $index = config('elasticsearch.index', env('ELASTICSEARCH_INDEX', 'vidiaspot_marketplace'));
        
        $searchParams = [
            'index' => $index,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [],
                        'filter' => [],
                    ]
                ],
                'sort' => [
                    'created_at' => ['order' => 'desc']
                ],
                'from' => $queryData['from'] ?? 0,
                'size' => $queryData['size'] ?? 20,
            ]
        ];
        
        // Add search term if provided
        if (!empty($queryData['q'])) {
            $searchParams['body']['query']['bool']['must'][] = [
                'multi_match' => [
                    'query' => $queryData['q'],
                    'fields' => ['title^3', 'description^2', 'location', 'category_name']
                ]
            ];
        }
        
        // Add category filter
        if (!empty($queryData['category_id'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'term' => [
                    'category_id' => $queryData['category_id']
                ]
            ];
        }
        
        // Add location filter
        if (!empty($queryData['location'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'match_phrase' => [
                    'location' => $queryData['location']
                ]
            ];
        }
        
        // Add price range filter
        if (!empty($queryData['min_price']) || !empty($queryData['max_price'])) {
            $rangeQuery = ['range' => ['price' => []]];
            
            if (!empty($queryData['min_price'])) {
                $rangeQuery['range']['price']['gte'] = $queryData['min_price'];
            }
            
            if (!empty($queryData['max_price'])) {
                $rangeQuery['range']['price']['lte'] = $queryData['max_price'];
            }
            
            $searchParams['body']['query']['bool']['filter'][] = $rangeQuery;
        }
        
        try {
            $results = $this->client->search($searchParams);
            
            return [
                'hits' => $results['hits']['hits'] ?? [],
                'total' => $results['hits']['total']['value'] ?? 0,
                'took' => $results['took'] ?? 0
            ];
        } catch (\Exception $e) {
            Log::error('Elasticsearch search error: ' . $e->getMessage());
            return ['hits' => [], 'total' => 0, 'took' => 0];
        }
    }
    
    /**
     * Delete an ad from Elasticsearch index
     */
    public function deleteAd(int $adId): bool
    {
        try {
            $params = [
                'index' => config('elasticsearch.index', env('ELASTICSEARCH_INDEX', 'vidiaspot_marketplace')),
                'id' => $adId
            ];
            
            $response = $this->client->delete($params);
            return isset($response['result']) && $response['result'] === 'deleted';
        } catch (\Exception $e) {
            Log::error('Elasticsearch delete error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an ad in Elasticsearch index
     */
    public function updateAd(Ad $ad): bool
    {
        // For update, we can simply re-index it
        return $this->indexAd($ad);
    }
    
    /**
     * Perform an autocomplete search
     */
    public function autocompleteSearch(string $query, int $size = 10): array
    {
        $index = config('elasticsearch.index', env('ELASTICSEARCH_INDEX', 'vidiaspot_marketplace'));
        
        $searchParams = [
            'index' => $index,
            'body' => [
                'suggest' => [
                    'ad-suggest' => [
                        'prefix' => $query,
                        'completion' => [
                            'field' => 'title.suggest',
                            'size' => $size,
                            'skip_duplicates' => true
                        ]
                    ]
                ]
            ]
        ];
        
        try {
            $results = $this->client->search($searchParams);
            $suggestions = [];
            
            if (isset($results['suggest']['ad-suggest'][0]['options'])) {
                foreach ($results['suggest']['ad-suggest'][0]['options'] as $option) {
                    $suggestions[] = [
                        'text' => $option['text'],
                        'score' => $option['score'] ?? 0
                    ];
                }
            }
            
            return $suggestions;
        } catch (\Exception $e) {
            Log::error('Elasticsearch autocomplete error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get search suggestions based on popular searches
     */
    public function getPopularSearches(int $limit = 10): array
    {
        // This would typically use Elasticsearch aggregations to find popular searches
        // For now, returning a placeholder implementation
        return [];
    }
}