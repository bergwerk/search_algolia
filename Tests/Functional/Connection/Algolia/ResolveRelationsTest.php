<?php

namespace Mahu\SearchAlgolia\Tests\Functional\Connection\Algolia;

/*
 * Copyright (C) 2018  Martin Hummer <ma.hummer@gmail.com>
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

use TYPO3\CMS\Core\DataHandling\DataHandler as Typo3DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ResolveRelationsTest extends AbstractFunctionalTestCase
{
    protected function getDataSets()
    {
        return array_merge(
            parent::getDataSets(),
            ['Tests/Functional/Fixtures/Indexing/ResolveRelations.xml']
        );
    }

    /**
    * @group test
    * @test
    */
    public function dataHandlerRelationResolving()
    {
        $this->initIndex('tt_content');

        $tce = GeneralUtility::makeInstance(Typo3DataHandler::class);
        $tce->stripslashes_values = 0;
        $commandMap = [];
        $dataMap = [
            'tt_content' => [
              'NEW58d5079c822627844' => [
                'pid' => 1,
                'header' => 'New New New',
                'bodytext' => 'New New New',
                'CType' => 'text',
                'categories' => 1
              ],
            ],
            'sys_category_record_mm' => [
                'NEW58d506f3cd0c41.59344142' => [
                    'uid_local' => 1,
                  'uid_foreign' => 'NEW58d5079c822627844',
                  'tablenames' => 'tt_content',
                  'fieldname' => 'categories',
                  'sorting' => 1,
                  'sorting_foreign' => 1
                ],
            ],
          ];
        $tce->start($dataMap, []);
        $tce->process_datamap();

        $response = $this->index->search('*');

        $this->assertSame($response['nbHits'], 1, 'Not exactly 1 document was indexed.');

        $this->assertArraySubset(
                    [0 => [
                        'header' => 'New New New',
                        'categories' => ['Category 1']
                        ]
                    ],
                    $response['hits'],
                    false,
                    'tx_mdms_domain_model_collection_item Record was not indexed with Relations.'
                );
    }
}
