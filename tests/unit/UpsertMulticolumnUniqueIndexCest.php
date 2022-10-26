<?php

use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\PDO\MySql\Driver;
use Pharako\DBAL\Connection;

class UpsertMulticolumnUniqueIndexCest
{
    public function _before(UnitTester $I)
    {
        $params = $I->getDbConfig();

        $this->dbal = new Connection(
            $params,
            new Driver(),
            new Configuration(),
            new EventManager()
        );
    }

    public function _after(UnitTester $I)
    {
    }

    /**
     * @group upsert
     * @group multicolumn-unique-index
     */
    public function insertMultiple(UnitTester $I)
    {
        $heroes = [
            [
                'name1' => 'Tupac',
                'name2' => 'Amaru',
                'a_string' => 'A string',
                'an_integer' => 1
            ],
            [
                'name1' => 'Tupac',
                'name2' => 'Qatari',
                'a_string' => 'Another string',
                'an_integer' => 2
            ]
        ];

        $this->dbal->insert('heroes_multi_column_index', $heroes);

        foreach ($heroes as $hero) {
            $I->seeInDatabase('heroes_multi_column_index', $hero);
        }
    }

    /**
     * @group upsert
     * @group multicolumn-unique-index
     */
    public function updateMultiple(UnitTester $I)
    {
        $heroes = [
            [
                'name1' => 'Tupac',
                'name2' => 'Amaru',
                'a_string' => 'An updated string',
                'an_integer' => 11
            ],
            [
                'name1' => 'Tupac',
                'name2' => 'Qatari',
                'a_string' => 'Another updated string',
                'an_integer' => 22
            ]
        ];

        $this->dbal->upsert('heroes_multi_column_index', $heroes);

        foreach ($heroes as $hero) {
            $I->seeInDatabase('heroes_multi_column_index', $hero);
        }
    }
}
