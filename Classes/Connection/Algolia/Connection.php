<?php

namespace Mahu\SearchAlgolia\Connection\Algolia;

/*
 * Copyright (C) 2016  Daniel Siepmann <coding@daniel-siepmann.de>
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

use Codappix\SearchCore\Configuration\ConfigurationContainerInterface;
use TYPO3\CMS\Core\SingletonInterface as Singleton;

/**
 * The current connection to algolia.
 *
 * Wrapper for Algolia\Client.
 *
 * TODO: Catch inner exception and throw general ones (at least in Connection-Namespace (not elastic specific))
 */
class Connection implements Singleton
{
    /**
     * @var \AlgoliaSearch\Client
     */
    protected $algoliaClient;

    /**
     * @var ConfigurationContainerInterface
     */
    protected $configuration;

    /**
     * @param ConfigurationContainerInterface $configuration
     * @param \AlgoliaSearch\Client $algoliaClient
     */
    public function __construct(
        ConfigurationContainerInterface $configuration,
        \AlgoliaSearch\Client $algoliaClient = null
    ) {
        $this->configuration = $configuration;

        $this->algoliaClient = $algoliaClient;
        if ($this->algoliaClient === null) {
            $this->algoliaClient = new \AlgoliaSearch\Client(
                $configuration->get('connections.algolia.applicationID'),
                $configuration->get('connections.algolia.apiKey')
            );
        }
    }

    /**
     * Get the concrete client for internal usage!
     *
     * @return \AlgoliaSearch\Client
     */
    public function getClient()
    {
        return $this->algoliaClient;
    }
}
