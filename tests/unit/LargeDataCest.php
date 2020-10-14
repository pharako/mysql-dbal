<?php

use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Pharako\DBAL\Connection;

class LargeDataCest
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
     * @group insert
     * @group multiple
     * @group data
     */
    public function largeDataInsert(UnitTester $I)
    {
        $heroes = [];
        $n = 20000;
        for ($i = 0; $i < $n; $i++) {
            $heroes[] = [
                'name' => sq("Tupac Qatari $i - "),
                'an_integer' => rand(0, $n)
            ];
        }

        $this->dbal->insert('heroes', $heroes);

        foreach ($heroes as $hero) {
            $I->seeInDatabase('heroes', $hero);
        }
    }

    /**
     * All fields, except unique key (`name`), get updated
     * @group upsert
     * @group multiple
     * @group data
     */
    public function largeDataUpsert(UnitTester $I)
    {
        $heroes = [];
        $n = 20000;
        for ($i = 0; $i < $n; $i++) {
            $heroes[] = [
                'name' => sq("Tupac Qatari $i - "),
                'an_integer' => rand(0, $n)
            ];
        }

        $this->dbal->upsert('heroes', $heroes);

        foreach ($heroes as $hero) {
            $I->seeInDatabase('heroes', $hero);
        }
    }
}
