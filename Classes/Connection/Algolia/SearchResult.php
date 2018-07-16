<?php

namespace Mahu\SearchAlgolia\Connection\Algolia;

use Codappix\SearchCore\Connection\ResultItemInterface;
use Codappix\SearchCore\Connection\SearchRequestInterface;
use Codappix\SearchCore\Connection\SearchResultInterface;
use Codappix\SearchCore\Domain\Model\QueryResultInterfaceStub;
use Codappix\SearchCore\Domain\Model\ResultItem;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class SearchResult implements SearchResultInterface
{
    use QueryResultInterfaceStub;

    /**
     * @var SearchRequestInterface
     */
    private $searchRequest;

    /**
     * @var mixed
     */
    private $resultsAlgolia;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var ResultItemInterface[]
     */
    private $results;

    /**
     * SearchResult constructor.
     * @param SearchRequestInterface $searchRequest
     * @param mixed $results
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SearchRequestInterface $searchRequest,
        $results,
        ObjectManager $objectManager
    )
    {
        $this->searchRequest = $searchRequest;
        $this->resultsAlgolia = $results;
        $this->objectManager = $objectManager;
    }

    /**
     * @return array<ResultItemInterface>
     */
    public function getResults(): array
    {
        if (is_null($this->results)) {
            $this->results = [];
            
            foreach ($this->resultsAlgolia as $indexName => $indexResults) {
                foreach ($indexResults['hits'] as $hit) {
                    $result = [
                        'id' => $hit['uid'],
                        'type' => $indexName,
                        'hit' => [
                            '_source' => $hit
                        ]
                    ];

                    $this->results[] = new ResultItem($result, $indexName);
                }
            }
        }

        return $this->results;
    }

    /**
     * Return all facets, if any.
     *
     * @return array<FacetInterface>
     */
    public function getFacets(): array
    {
        // TODO: Implement getFacets() method.
    }

    /**
     * Returns the number of results in current result
     */
    public function getCurrentCount(): int
    {
        return count($this->getResults());
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->getResults());
    }

    public function current()
    {
        return $this->getResults()[$this->position];
    }

    public function next()
    {
        ++$this->position;

        return $this->current();
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->getResults()[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function getQuery()
    {
        return $this->searchRequest;
    }
}
