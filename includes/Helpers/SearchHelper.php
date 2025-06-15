<?php

namespace PonePaste\Helpers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Exception;
use PonePaste\Models\Paste;

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
            ]
        ]);
    }

    public function search(string $query) : \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise {
        return $this->client->search([
            'index' => 'pastes',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['title', 'author', 'content']
                    ]
                ]
            ]
        ]);
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