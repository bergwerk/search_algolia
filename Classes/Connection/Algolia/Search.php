<?php

namespace Mahu\SearchAlgolia\Connection\Algolia;

use AlgoliaSearch\Client;
use Codappix\SearchCore\Connection\SearchRequestInterface;

class Search
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string[]
     */
    private $indexes;

    /**
     * @var SearchRequestInterface
     */
    private $searchRequest;

    /**
     * Search constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $indexName
     * @return self
     */
    public function addIndex($indexName)
    {
        $this->indexes[] = $indexName;

        return $this;
    }

    /**
     * @throws \AlgoliaSearch\AlgoliaException
     * @return self
     */
    public function applyAllIndexes()
    {
        foreach ($this->client->listIndexes()['items'] as $index)
        {
            $this->addIndex($index['name']);
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function appliedIndexes()
    {
        return $this->indexes;
    }

    /**
     * @param SearchRequestInterface $searchRequest
     *
     * @return self
     */
    public function setSearchRequest(SearchRequestInterface $searchRequest)
    {
        $this->searchRequest = $searchRequest;

        return $this;
    }

    /**
     * @return array
     *
     * @throws \AlgoliaSearch\AlgoliaException
     */
    public function search()
    {
        $results = [];

        foreach ($this->indexes as $indexName)
        {
            $index = $this->client->initIndex($indexName);
            $results[$indexName] = $index->search($this->searchRequest->getSearchTerm());
        }

        return $results;
    }
}
