<?php

namespace PonePaste\Helpers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Exception;
use PonePaste\Models\Paste;
use PonePaste\Search\SearchParser;
use stdClass;

/**
 * SearchHelper is the main entry point for the search functionality.
 */
class SearchHelper {
    private Client $client;

    /**
     * SearchHelper constructor.
     * @param Client $client The Elasticsearch client.
     */
    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Drop the paste index and all data in it completely.
     */
    public function dropPasteIndex() {
        $this->client->indices()->delete([
            'index' => 'pastes'
        ]);
    }

    /**
     * Create the paste index with the appropriate field types and settings.
     */
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
                    'properties' => Paste::$ELASTICSEARCH_MAPPINGS
                ]
            ]
        ]);
    }

    /**
     * Index a paste into ElasticSearch.    
     * 
     * @param Paste $paste The paste to index.
     */
    public function indexPaste(Paste $paste) {
        $paste->index($this->client);
    }

    /**
     * Execute a raw search query against the paste index.
     * Also highlights the results for some reason.
     * 
     * @param array $query The query to search for.
     * @return \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise The search results.
     */
    public function search(array $query) : \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise {
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

    /**
     * fancySearch is a wrapper around the search method that allows for more complex queries.
     * 
     * @param array $options The options for the search.    
     * @param callable|null $filter_callback A callback function that can be used to add filters to the query.
     * @return \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise The search results.
     */
    public function fancySearch(array $options = [], callable|null $filter_callback = null): \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise {
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

        if ($filter_callback !== null) {
            $filter_callback($filters);
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

        return $this->search($searchBody);
    }

    /**
     * The default sort for the search: created_at descending.
     * 
     * @return array The default sort.
     */
    private function defaultSort(): array {
        return [['created_at' => 'desc']];
    }

    /**
     * The default query for the search: match_all.
     * 
     * @return array The default query.
     */
    private function defaultQuery(): array {
        return [
            'match_all' => new stdClass(),
        ];
    }

    /**
     * Convert the search results to a collection of pastes.
     * 
     * @param \Elastic\Elasticsearch\Response\Elasticsearch|array $results The search results.
     * @return \Illuminate\Database\Eloquent\Collection The collection of pastes.
     */
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