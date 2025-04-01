<?php

use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Pharako\DBAL\Connection;

class InsertMultipleCest
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
     * @group insert
     * @group multiple
     */
    public function insertMultiple(UnitTester $I)
    {
        $heroes = [
            ['name' => sq('Tupac Qatari_'), 'an_integer' => rand(0, 100)],
            ['name' => sq('Moctezuma_'), 'an_integer' => rand(0, 100)],
            ['name' => sq('Guaicaipuro_'), 'an_integer' => rand(0, 100)]
        ];

        $this->dbal->insert('heroes', $heroes);

        foreach ($heroes as $hero) {
            $I->seeInDatabase('heroes', $hero);
        }
    }

    /**
     * Passing an array with Doctrine types guarantees parameter binding (note that 'an_integer' is being cast to a 
     * float but still correctly inserted as an integer)
     * @group insert
     * @group multiple
     */
    public function insertMultipleWithTypes(UnitTester $I)
    {
        $correctHeroes = [
            ['name' => sq('Tupac Qatari_'), 'an_integer' => rand(0, 100)],
            ['name' => sq('Moctezuma_'), 'an_integer' => rand(0, 100)],
            ['name' => sq('Guaicaipuro_'), 'an_integer' => rand(0, 100)]
        ];

        $heroes = $correctHeroes;
        foreach ($heroes as &$hero) {
            $hero['an_integer'] = floatval((string)$hero['an_integer'] . '.00The');
        }

        $this->dbal->insert('heroes', $heroes, ['string', 'integer']);

        foreach ($correctHeroes as $correctHero) {
            $I->seeInDatabase('heroes', $correctHero);
        }
    }

    /**
     * Same as insertMultipleWithTypes(), but with a dictionary of types instead of a flat array.
     * @group insert
     * @group multiple
     */
    public function insertMultipleWithTypesDictionary(UnitTester $I)
    {
        $correctHeroes = [
            ['name' => sq('Tupac Qatari_'), 'an_integer' => rand(0, 100)],
            ['name' => sq('Moctezuma_'), 'an_integer' => rand(0, 100)],
            ['name' => sq('Guaicaipuro_'), 'an_integer' => rand(0, 100)]
        ];

        $heroes = $correctHeroes;
        foreach ($heroes as &$hero) {
            $hero['an_integer'] = floatval((string)$hero['an_integer'] . '.00The');
        }

        $this->dbal->insert('heroes', $heroes, ['name' => 'string', 'an_integer' => 'integer']);

        foreach ($correctHeroes as $correctHero) {
            $I->seeInDatabase('heroes', $correctHero);
        }
    }
}
