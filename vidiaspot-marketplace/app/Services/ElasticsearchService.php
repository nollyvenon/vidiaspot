<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ElasticsearchService
{
    protected string $host;
    protected string $index;
    protected array $mapping;

    public function __construct()
    {
        $this->host = env('ELASTICSEARCH_HOST', 'http://localhost:9200');
        $this->index = env('ELASTICSEARCH_INDEX', 'vidiaspot_marketplace');
        $this->mapping = [
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                        'fields' => [
                            'keyword' => ['type' => 'keyword']
                        ]
                    ],
                    'description' => [
                        'type' => 'text',
                        'analyzer' => 'standard'
                    ],
                    'category' => [
                        'type' => 'keyword',
                        'fields' => [
                            'text' => ['type' => 'text']
                        ]
                    ],
                    'price' => ['type' => 'float'],
                    'location' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                        'fields' => [
                            'keyword' => ['type' => 'keyword']
                        ]
                    ],
                    'condition' => ['type' => 'keyword'],
                    'user_id' => ['type' => 'integer'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date'],
                    'tags' => ['type' => 'keyword'],
                    'status' => ['type' => 'keyword'],
                    'featured' => ['type' => 'boolean'],
                ]
            ]
        ];
    }

    /**
     * Create the Elasticsearch index with mapping
     *
     * @return array
     */
    public function createIndex(): array
    {
        $response = Http::put("{$this->host}/{$this->index}", [
            'Content-Type' => 'application/json',
        ], $this->mapping);

        return $response->json();
    }

    /**
     * Index a document (ad) in Elasticsearch
     *
     * @param array $document
     * @return array
     */
    public function indexDocument(array $document): array
    {
        $id = $document['id'];
        $response = Http::put("{$this->host}/{$this->index}/_doc/{$id}", [
            'Content-Type' => 'application/json',
        ], $document);

        return $response->json();
    }

    /**
     * Bulk index multiple documents
     *
     * @param array $documents
     * @return array
     */
    public function bulkIndexDocuments(array $documents): array
    {
        $bulkBody = '';
        foreach ($documents as $doc) {
            $docId = $doc['id'];
            $bulkBody .= json_encode(["index" => ["_index" => $this->index, "_id" => $docId]]) . "\n";
            $bulkBody .= json_encode($doc) . "\n";
        }

        $response = Http::post("{$this->host}/{$this->index}/_bulk", [
            'Content-Type' => 'application/x-ndjson',
        ], $bulkBody);

        return $response->json();
    }

    /**
     * Update a document in Elasticsearch
     *
     * @param int $id
     * @param array $document
     * @return array
     */
    public function updateDocument(int $id, array $document): array
    {
        $response = Http::post("{$this->host}/{$this->index}/_update/{$id}", [
            'Content-Type' => 'application/json',
        ], ['doc' => $document]);

        return $response->json();
    }

    /**
     * Delete a document from Elasticsearch
     *
     * @param int $id
     * @return array
     */
    public function deleteDocument(int $id): array
    {
        $response = Http::delete("{$this->host}/{$this->index}/_doc/{$id}");

        return $response->json();
    }

    /**
     * Search documents with various criteria
     *
     * @param array $criteria
     * @param int $from
     * @param int $size
     * @return array
     */
    public function search(array $criteria = [], int $from = 0, int $size = 10): array
    {
        $query = [
            'query' => [
                'bool' => [
                    'should' => [],
                    'must' => [],
                    'filter' => [],
                ],
            ],
            'from' => $from,
            'size' => $size,
            'sort' => [
                ['created_at' => ['order' => 'desc']]
            ]
        ];

        // Add text search
        if (isset($criteria['search'])) {
            $query['query']['bool']['should'][] = [
                'multi_match' => [
                    'query' => $criteria['search'],
                    'fields' => ['title^3', 'description^2', 'location', 'tags'],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO'
                ]
            ];
        }

        // Add filters
        if (isset($criteria['category'])) {
            $query['query']['bool']['must'][] = [
                'term' => ['category.keyword' => $criteria['category']]
            ];
        }

        if (isset($criteria['location'])) {
            $query['query']['bool']['must'][] = [
                'match_phrase_prefix' => ['location' => $criteria['location']]
            ];
        }

        if (isset($criteria['min_price'])) {
            $query['query']['bool']['must'][] = [
                'range' => ['price' => ['gte' => $criteria['min_price']]]
            ];
        }

        if (isset($criteria['max_price'])) {
            $query['query']['bool']['must'][] = [
                'range' => ['price' => ['lte' => $criteria['max_price']]]
            ];
        }

        if (isset($criteria['condition'])) {
            $query['query']['bool']['must'][] = [
                'term' => ['condition' => $criteria['condition']]
            ];
        }

        if (isset($criteria['user_id'])) {
            $query['query']['bool']['must'][] = [
                'term' => ['user_id' => $criteria['user_id']]
            ];
        }

        // Add status filter
        $query['query']['bool']['must'][] = [
            'term' => ['status' => 'active']
        ];

        // Execute search
        $response = Http::post("{$this->host}/{$this->index}/_search", [
            'Content-Type' => 'application/json',
        ], $query);

        return $response->json();
    }

    /**
     * Simple search with wildcard
     *
     * @param string $query
     * @return array
     */
    public function simpleSearch(string $query): array
    {
        $searchQuery = [
            'query' => [
                'simple_query_string' => [
                    'query' => $query,
                    'fields' => ['title^3', 'description^2', 'location', 'tags'],
                    'default_operator' => 'and'
                ]
            ]
        ];

        $response = Http::post("{$this->host}/{$this->index}/_search", [
            'Content-Type' => 'application/json',
        ], $searchQuery);

        return $response->json();
    }

    /**
     * Search with aggregations (facets)
     *
     * @param array $criteria
     * @return array
     */
    public function searchWithAggregations(array $criteria = []): array
    {
        $response = $this->search($criteria);

        // Add aggregations to the search request if we need them separately
        $aggQuery = [
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['status' => 'active']]
                    ]
                ]
            ],
            'aggs' => [
                'categories' => [
                    'terms' => [
                        'field' => 'category.keyword',
                        'size' => 20
                    ]
                ],
                'locations' => [
                    'terms' => [
                        'field' => 'location.keyword',
                        'size' => 20
                    ]
                ],
                'conditions' => [
                    'terms' => [
                        'field' => 'condition',
                        'size' => 10
                    ]
                ],
                'price_range' => [
                    'range' => [
                        'field' => 'price',
                        'ranges' => [
                            ['to' => 10000],
                            ['from' => 10000, 'to' => 50000],
                            ['from' => 50000, 'to' => 100000],
                            ['from' => 100000, 'to' => 500000],
                            ['from' => 500000]
                        ]
                    ]
                ]
            ]
        ];

        // Add additional filters
        if (isset($criteria['search'])) {
            $aggQuery['query']['bool']['must'][] = [
                'multi_match' => [
                    'query' => $criteria['search'],
                    'fields' => ['title^3', 'description^2', 'location', 'tags'],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO'
                ]
            ];
        }

        $aggResponse = Http::post("{$this->host}/{$this->index}/_search", [
            'Content-Type' => 'application/json',
        ], $aggQuery);

        $result = $response;
        $result['aggregations'] = $aggResponse->json()['aggregations'] ?? [];

        return $result;
    }

    /**
     * Get related products based on current product
     *
     * @param int $productId
     * @param int $size
     * @return array
     */
    public function getRelatedProducts(int $productId, int $size = 5): array
    {
        // First, get the current product to extract its properties
        $productResponse = Http::get("{$this->host}/{$this->index}/_doc/{$productId}");
        $product = $productResponse->json();

        if (!isset($product['_source'])) {
            return ['hits' => ['hits' => []]];
        }

        $source = $product['_source'];

        // Build query to find similar products
        $query = [
            'query' => [
                'bool' => [
                    'should' => [
                        [
                            'term' => ['category.keyword' => $source['category']]
                        ],
                        [
                            'more_like_this' => [
                                'fields' => ['title', 'description'],
                                'like' => [
                                    [
                                        '_index' => $this->index,
                                        '_id' => $productId
                                    ]
                                ],
                                'min_term_freq' => 1,
                                'max_query_terms' => 12
                            ]
                        ]
                    ],
                    'must_not' => [
                        ['term' => ['id' => $productId]]
                    ],
                    'filter' => [
                        ['term' => ['status' => 'active']]
                    ]
                ]
            ],
            'size' => $size
        ];

        $response = Http::post("{$this->host}/{$this->index}/_search", [
            'Content-Type' => 'application/json',
        ], $query);

        return $response->json();
    }

    /**
     * Check if index exists
     *
     * @return bool
     */
    public function indexExists(): bool
    {
        $response = Http::get("{$this->host}/_cat/indices/{$this->index}?h=index");

        return $response->successful() && !empty($response->body()) && trim($response->body()) === $this->index;
    }

    /**
     * Delete the index
     *
     * @return array
     */
    public function deleteIndex(): array
    {
        $response = Http::delete("{$this->host}/{$this->index}");

        return $response->json();
    }

    /**
     * Get index statistics
     *
     * @return array
     */
    public function getIndexStats(): array
    {
        $response = Http::get("{$this->host}/{$this->index}/_stats");

        return $response->json();
    }
}