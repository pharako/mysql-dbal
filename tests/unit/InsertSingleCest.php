<?php

use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Pharako\DBAL\Connection;

class InsertSingleCest
{
    public function _before(UnitTester $I)
    {
        $params = [
            'dbname' => getenv('DB_DATABASE') ?: 'testdb',
            'host' => getenv('DB_HOST') ?: '127.0.0.1',
            'user' => getenv('DB_USERNAME') ?: 'root',
            'password' => getenv('DB_PASSWORD') ?: '',
            'driver' => getenv('DB_DRIVER') ?: 'pdo_mysql'
        ];

        $this->dbal = new Connection(
            $params,
            new Driver(),
            new Configuration(),
            new EventManager()
        );

        $this
            ->dbal
            ->getConfiguration()
            ->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
    }

    public function _after(UnitTester $I)
    {
    }

    /**
     * @group insert
     * @group single
     */
    public function insertSingleTest(UnitTester $I)
    {
        $hero = [
            'name' => 'Coxo',
            'pseudonym' => null,
            'date_of_birth' => '1800-04-04',
            'genociders_knocked_down' => 100
        ];

        $this->dbal->insert('heroes', $hero);

        $I->seeInDatabase('heroes', $hero);
    }

    /**
     * Passing an array with Doctrine types guarantees parameter binding
     * @group insert
     * @group single
     */
    public function insertSingleWitTypesTest(UnitTester $I)
    {
        $hero = ['name' => 'Pindobusu', 'genociders_knocked_down' => 100];

        $this->dbal->insert('heroes', $hero, ['string', 'integer']);

        $I->seeInDatabase('heroes', $hero);
    }

    /**
     * @group insert
     * @group single
     */
    public function insertSingleMultidimensionalTest(UnitTester $I)
    {
        $heroes = [
            [
                'name' => 'Taina',
                'pseudonym' => null,
                'date_of_birth' => '1700-04-04',
                'genociders_knocked_down' => 300
            ]
        ];

        $this->dbal->insert('heroes', $heroes[0]);

        $I->seeInDatabase('heroes', $heroes[0]);
    }
}
