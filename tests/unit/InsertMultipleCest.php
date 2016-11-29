<?php

use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Pharako\DBAL\Connection;

class InsertMultipleCest
{
    public function _before(UnitTester $I)
    {
        $params = [
            'dbname' => 'testdb',
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'driver' => 'pdo_mysql'
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
     * @group multiple
     */
    public function insertMultipleTest(UnitTester $I)
    {
        $heroes = [
            [
                'name' => 'Sepé',
                'pseudonym' => null,
                'date_of_birth' => '1800-04-04',
                'genociders_knocked_down' => 100
            ],
            [
                'name' => 'Tupaq Amaru',
                'pseudonym' => 'Túpac Amaru',
                'date_of_birth' => '1700-04-04',
                'genociders_knocked_down' => 1000
            ]
        ];

        $this->dbal->insert('heroes', $heroes);

        $I->seeInDatabase('heroes', $heroes[0]);
        $I->seeInDatabase('heroes', $heroes[1]);
    }

    /**
     * Passing an array with Doctrine types guarantees parameter binding (note that 'genociders_knocked_down' is being
     * cast to a float but still correctly inserted as an integer)
     * @group insert
     * @group multiple
     */
    public function insertMultipleWitTypesTest(UnitTester $I)
    {
        $correctHeroes = [
            ['name' => 'Tupac Qatari', 'genociders_knocked_down' => 300],
            ['name' => 'Moctezuma', 'genociders_knocked_down' => 400],
            ['name' => 'Guaicaipuro', 'genociders_knocked_down' => 999]
        ];

        $heroes = $correctHeroes;
        foreach ($heroes as &$hero) {
            $hero['genociders_knocked_down'] = floatval((string)$hero['genociders_knocked_down'] . '.00The');
        }

        $this->dbal->upsert('heroes', $heroes, ['string', 'integer']);

        $I->seeInDatabase('heroes', $correctHeroes[0]);
        $I->seeInDatabase('heroes', $correctHeroes[1]);
        $I->seeInDatabase('heroes', $correctHeroes[2]);
    }
}
