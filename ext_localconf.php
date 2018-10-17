<?php

call_user_func(
    function ($extensionKey) {
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\Container\Container')
            ->registerImplementation(
                'Codappix\SearchCore\Connection\ConnectionInterface',
                'Mahu\SearchAlgolia\Connection\Algolia'
            );

        $settings = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('search_algolia');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('plugin {
            tx_searchalgolia {
                settings {
                    connections {
                        algolia {
                            applicationID = ' . $settings['appId'] .'
                            apiKey = ' . $settings['adminApiKey'] .'
                        }
                    }
                }
            }
        }'
        );
    },
    $_EXTKEY
);

/**
 * Frontend Plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Mahu.search_algolia',
    'Algolia',
    [
        'AlgoliaFrontend' => 'search'
    ],
    []
);
