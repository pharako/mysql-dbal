<?php

use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Pharako\DBAL\Connection;

class UpsertMultipleUniqueIndexesCest
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
     * @group multiple-unique-indexes
     */
    public function insertMultiple(UnitTester $I)
    {
        $heroes = [
            [
                'name' => 'Sepé',
                'unique_thing' => 'A thing',
                'an_integer' => 1
            ],
            [
                'name' => 'Pindobusu',
                'unique_thing' => 'Another thing',
                'an_integer' => 2
            ]
        ];

        $this->dbal->insert('heroes_multiple_unique_indexes', $heroes);

        foreach ($heroes as $hero) {
            $I->seeInDatabase('heroes_multiple_unique_indexes', $hero);
        }
    }

    /**
     * @group upsert
     * @group multiple-unique-indexes
     */
    public function updateMultiple(UnitTester $I)
    {
        $heroes = [
            [
                'name' => 'Komorim',
                'unique_thing' => 'A thing',
                'an_integer' => 11
            ],
            [
                'name' => 'Parabusu',
                'unique_thing' => 'Another thing',
                'an_integer' => 22
            ]
        ];

        $this->dbal->upsert('heroes_multiple_unique_indexes', $heroes);

        foreach ($heroes as $hero) {
            $I->seeInDatabase('heroes_multiple_unique_indexes', $hero);
        }
    }
}
