<?php

use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Pharako\DBAL\Connection;

class UpsertMultipleCest
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

        $this
            ->dbal
            ->getConfiguration()
            ->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
    }

    public function _after(UnitTester $I)
    {
    }

    /**
     * @group upsert
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

        $this->dbal->upsert('heroes', $heroes);

        $I->seeInDatabase('heroes', $heroes[0]);
        $I->seeInDatabase('heroes', $heroes[1]);
    }

    /**
     * All fields, except unique key (`name`), get updated
     * @group upsert
     * @group multiple
     */
    public function updateMultipleTest(UnitTester $I)
    {
        $heroes = [
            [
                'name' => 'Sepé',
                'pseudonym' => 'José Tiaraj...',
                'date_of_birth' => '1804-01-01',
                'genociders_knocked_down' => 180
            ],
            [
                'name' => 'Tupaq Amaru',
                'pseudonym' => 'Túpac Amar...',
                'date_of_birth' => '1700-01-01',
                'genociders_knocked_down' => 1100
            ],
            [
                'name' => 'Guaicaipuro',
                'pseudonym' => null,
                'date_of_birth' => '1900-01-01',
                'genociders_knocked_down' => 50
            ]
        ];

        $this->dbal->upsert('heroes', $heroes);

        $I->seeInDatabase('heroes', $heroes[0]);
        $I->seeInDatabase('heroes', $heroes[1]);
        $I->seeInDatabase('heroes', $heroes[2]);
    }

    /**
     * This will only update the `pseudonym` field, no matter what other fields are passed (note that
     * `genociders_knocked_down` doesn't get updated)
     * @group upsert
     * @group multiple
     */
    public function updateMultipleTestWithColumnsToUpdate(UnitTester $I)
    {
        $correctHeroes = [
            [
                'name' => 'Sepé',
                'pseudonym' => 'José Tiaraju',
                'genociders_knocked_down' => 180
            ],
            [
                'name' => 'Tupaq Amaru',
                'pseudonym' => 'Túpac Amaru',
                'genociders_knocked_down' => 1100
            ]
        ];

        $heroes = $correctHeroes;
        $heroes[0]['genociders_knocked_down'] = 0;
        $heroes[1]['genociders_knocked_down'] = 0;

        $this->dbal->upsert('heroes', $heroes, [], ['pseudonym']);

        $I->seeInDatabase('heroes', $correctHeroes[0]);
        $I->seeInDatabase('heroes', $correctHeroes[1]);
    }

    /**
     * Passing an array with Doctrine types guarantees parameter binding
     * @group upsert
     * @group multiple
     */
    public function updateMultipleTestWitTypes(UnitTester $I)
    {
        $heroes = [
            ['name' => 'Sepé', 'genociders_knocked_down' => 300],
            ['name' => 'Tupaq Amaru', 'genociders_knocked_down' => 300],
            ['name' => 'Guaicaipuro', 'genociders_knocked_down' => 300]
        ];

        $this->dbal->upsert('heroes', $heroes, ['string', 'integer']);

        $I->seeInDatabase('heroes', $heroes[0]);
        $I->seeInDatabase('heroes', $heroes[1]);
        $I->seeInDatabase('heroes', $heroes[2]);
    }
}
