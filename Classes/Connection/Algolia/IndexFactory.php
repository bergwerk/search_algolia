<?php

namespace Mahu\SearchAlgolia\Connection\Algolia;

/*
 * Copyright (C) 2018 Martin Hummer
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use AlgoliaSearch\Index;
use Codappix\SearchCore\Configuration\ConfigurationContainerInterface;
use Codappix\SearchCore\Domain\Index\IndexerFactory;
use TYPO3\CMS\Core\SingletonInterface as Singleton;

/**
 * Factory to get indexes.
 *
 * The factory will take care of configuration and creation of index if necessary.
 */
class IndexFactory implements Singleton
{
    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    /**
     * @param ConfigurationContainerInterface $configuration
     */
    public function __construct(ConfigurationContainerInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * returns the Algolia Index based on the indexName which is specified in the typoscript configuration.
     * If no indexName is specified for the given documentType, then the documentType itself will be used instead.
     *
     * @param Connection $connection
     * @param string $documentType
     *
     * @return \AlgoliaSearch\Index
     */
    public function getIndex(Connection $connection, $documentType)
    {
        $indexName = $this->getIndexNameFromConfiguration($documentType);

        /** @var $index Index */

        if ($indexName) {
            $index = $connection->getClient()->initIndex($indexName);
        } else {
            $index = $connection->getClient()->initIndex($documentType);
        }

        $indexConfig = $this->getIndexConfiguration($documentType);

        if (isset($indexConfig['facetFields'])) {
            $index->setSettings([
                'attributesForFaceting' => explode(
                    ',',
                    $indexConfig['facetFields']
                )
            ]);
        }

        return $index;
    }

    protected function getIndexConfiguration($documentType)
    {
        foreach ($this->getLocalIndexList() as $indexType => $indexConfig) {
            if ($indexType == $documentType) {
                return $indexConfig;
            }
        }

        return [];
    }

    protected function getIndexNameFromConfiguration($documentType)
    {
        try {
            $indexConfig = $this->getIndexConfiguration($documentType);

            if (isset($indexConfig['indexName'])) {
                return $indexConfig['indexName'];
            }
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }

    /**
     * @return array
     */
    public function getLocalIndexList()
    {
        $list = $this->configuration->get('indexing');

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][IndexerFactory::class]['getLocalIndexList'])) {
            foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][IndexerFactory::class]['getLocalIndexList'] as $_funcRef) {
                \TYPO3\CMS\Core\Utility\GeneralUtility::callUserFunction($_funcRef, $list, $this);
            }
        }

        return $list;
    }
}
