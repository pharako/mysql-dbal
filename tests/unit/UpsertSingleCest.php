<?php

use Doctrine\DBAL\DriverManager;
use Pharako\DBAL\Connection;

class UpsertSingleCest
{
    public function _before(UnitTester $I)
    {
        $this->dbal = new Connection(DriverManager::getConnection([
            'dbname' => 'testdb',
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'driver' => 'pdo_mysql'
        ]));

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

        $this->dbal->upsert('heroes', $hero);

        $I->seeInDatabase('heroes', $hero);
    }

    /**
     * All fields, except unique key (`name`), get updated
     * @group upsert
     * @group single
     */
    public function updateSingleTest(UnitTester $I)
    {
        $hero = [
            'name' => 'Coxo',
            'genociders_knocked_down' => 999
        ];

        $this->dbal->upsert('heroes', $hero);

        $I->seeInDatabase('heroes', $hero);
    }

    /**
     * This will only update the `pseudonym` field, no matter what other fields are passed (note that
     * `genociders_knocked_down` doesn't get updated)
     * @group upsert
     * @group single
     */
    public function updateSingleTestWithColumnsToUpdate(UnitTester $I)
    {
        $correctHero = [
            'name' => 'Coxo',
            'pseudonym' => null,
            'date_of_birth' => '1800-04-04',
            'genociders_knocked_down' => 999
        ];

        $hero = $correctHero;
        $heroes['genociders_knocked_down'] = 0;

        $this->dbal->upsert('heroes', $hero, [], ['pseudonym']);

        $I->seeInDatabase('heroes', $correctHero);
    }

    /**
     * Passing an array with Doctrine types guarantees parameter binding
     * @group upsert
     * @group single
     */
    public function updateSingleTestWitTypes(UnitTester $I)
    {
        $hero = ['name' => 'Coxo', 'genociders_knocked_down' => 999];

        $this->dbal->upsert('heroes', $hero, ['string', 'integer']);

        $I->seeInDatabase('heroes', $hero);
    }
}
