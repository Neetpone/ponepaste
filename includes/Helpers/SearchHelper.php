<?php

namespace PonePaste\Helpers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Exception;
use PonePaste\Models\Paste;
use PonePaste\Search\SearchParser;
use stdClass;

class SearchHelper {
    private Client $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    public function indexPaste(Paste $paste) {
        $paste->loadMissing('user'); // We need the user to index the paste
        $paste->loadMissing('tags');

        $this->client->index([
            'index' => 'pastes',
            'id' => $paste->id,
            'body' => [
                'title' => $paste->title,
                'author' => $paste->user->username,
                'content' =>  openssl_decrypt($paste->content, PP_ENCRYPTION_ALGO, PP_ENCRYPTION_KEY),
                'tags' => $paste->tags->map(function($tag) { return $tag->name; })->toArray(),
                'created_at' => $paste->created_at
            ]
        ]);
    }

    public function dropPasteIndex() {
        $this->client->indices()->delete([
            'index' => 'pastes'
        ]);
    }

    public function createPasteIndex() {
        $this->client->indices()->create([
            'index' => 'pastes',
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1
                ],
                'mappings' => [
                    'dynamic' => false,
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'snowball'
                        ],
                        'author' => [
                            'type' => 'text',
                            'analyzer' => 'snowball'
                        ],
                        'content' => [
                            'type' => 'text',
                            'analyzer' => 'snowball'
                        ],
                        'tags' => [
                            'type' => 'text',
                            'analyzer' => 'keyword'
                        ],
                        'created_at' => [
                            'type' => 'date',
                            'format' => 'yyyy-MM-dd HH:mm:ss'
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function search(array $query) : \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise {
//        return $this->client->search([
//            'index' => 'pastes',
//            'body' => [
//                'query' => [
//                    'multi_match' => [
//                        'query' => $query,
//                        'fields' => ['title', 'author', 'content']
//                    ]
//                ]
//            ]
//        ]);
        $searchBody = array_merge($query, [
            'highlight' => [
                'fields' => [
                    'content' => [
                        'pre_tags' => ['^highlight^'],
                        'post_tags' => ['^highlight^']
                    ],
                    'title' => [
                        'pre_tags' => ['^highlight^'],
                        'post_tags' => ['^highlight^']
                    ],
                    'author' => [
                        'pre_tags' => ['^highlight^'],
                        'post_tags' => ['^highlight^']
                    ]
                ]
            ]
        ]);

        return $this->client->search([
            'index' => 'pastes',
            'body' => $searchBody,
        ]);
    }

    private function defaultSort(): array {
        return [['created_at' => 'desc']];
    }

    private function defaultQuery(): array {
        return [
            'match_all' => new stdClass(),
        ];
    }

    public function fancySearch(array $options = []): \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise {
        $queries = $options['queries'] ?? [];
        $filters = $options['filters'] ?? [];
        $sorts = $options['sorts'] ?? [];

        // Handle query parsing if provided
        if (isset($options['query'])) {
            $searchParser = new SearchParser($options['query'], "tags", [
                'allowed_fields' => [
                    'full_text' => ['content'],
                    'literal' => ['author']
                ]
            ]);
            
            if ($searchParser->requiresQuery()) {
                $queries[] = $searchParser->parsed();
            } else {
                $filters[] = $searchParser->parsed();
            }
        }

        // Use default sort if none provided
        if (empty($sorts)) {
            $sorts = $this->defaultSort();
        }

        // Use default query if none provided
        if (empty($queries)) {
            $queries[] = $this->defaultQuery();
        }

        // Build the search body
        $searchBody = [
            'query' => [
                'bool' => [
                    'must' => $queries,
                    'filter' => $filters
                ]
            ],
            'sort' => $sorts,
            '_source' => false,
            'track_total_hits' => true
        ];

        // Handle pagination
        $size = (int)($options['size'] ?? $options['per_page'] ?? 25);

        if (isset($options['from'])) {
            $searchBody['size'] = $size;
            $searchBody['from'] = (int)$options['from'];
        } elseif (isset($options['page'])) {
            $searchBody['size'] = $size;
            $searchBody['from'] = ((int)$options['page'] - 1) * $size;
        } else {
            $searchBody['size'] = $size;
            $searchBody['from'] = 0;
        }

        // echo '<pre>';
        // echo json_encode($searchBody, JSON_PRETTY_PRINT);
        // echo '</pre>';

        return $this->search($searchBody);
    }

    public static function toRecords(\Elastic\Elasticsearch\Response\Elasticsearch|array $results) : \Illuminate\Database\Eloquent\Collection {
        $ids = array_column($results['hits']['hits'], '_id');
        return Paste::with('user')->whereIn('id', $ids)->get();
    }

    // Create the client lazily since most pages don't need it
    public static function instance() : SearchHelper {
        static $helper = null;

        if ($helper === null) {
            $helper = new SearchHelper(
                ClientBuilder::create()
                    ->setHosts([PP_ELASTICSEARCH_URL])
                    //->setBasicAuthentication(PP_ELASTICSEARCH_USER, PP_ELASTICSEARCH_PASS)
                    ->build()
            );
        }

        return $helper;
    }
}