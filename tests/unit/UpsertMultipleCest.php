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
    public function insertMultiple(UnitTester $I)
    {
        $heroes = [
            ['name' => sq('Tupac Qatari_'), 'an_integer' => rand(0, 100)],
            ['name' => sq('Moctezuma_'), 'an_integer' => rand(0, 100)],
            ['name' => sq('Guaicaipuro_'), 'an_integer' => rand(0, 100)]
        ];

        $this->dbal->upsert('heroes', $heroes);

        foreach ($heroes as $hero) {
            $I->seeInDatabase('heroes', $hero);
        }
    }

   /**
     * Passing an array with Doctrine types guarantees parameter binding (note that 'an_integer' is being cast to a 
     * float but still correctly inserted as an integer)
     * @group upsert
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

        $this->dbal->upsert('heroes', $heroes, ['string', 'integer']);

        foreach ($correctHeroes as $correctHero) {
            $I->seeInDatabase('heroes', $correctHero);
        }
    }

    /**
     * Same as insertMultipleWithTypes(), but with a dictionary of types instead of a flat array.
     * @group upsert
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

        $this->dbal->upsert('heroes', $heroes, ['name' => 'string', 'an_integer' => 'integer']);

        foreach ($correctHeroes as $correctHero) {
            $I->seeInDatabase('heroes', $correctHero);
        }
    }

    /**
     * All fields, except unique key (`name`), get updated
     * @group upsert
     * @group multiple
     */
    public function updateMultiple(UnitTester $I)
    {
        $heroes = [
            ['name' => 'Tupac Qatari', 'a_string' => sq('A string_'), 'an_integer' => rand(0, 100)],
            ['name' => 'Moctezuma', 'a_string' => sq('A string_'), 'an_integer' => rand(0, 100)],
            ['name' => 'Guaicaipuro', 'a_string' => sq('A string_'), 'an_integer' => rand(0, 100)]
        ];

        $this->dbal->upsert('heroes', $heroes);

        foreach ($heroes as $hero) {
            $I->seeInDatabase('heroes', $hero);
        }
    }

    /**
     * @group upsert
     * @group multiple
     */
    public function updateMultipleWithTypes(UnitTester $I)
    {
        $correctHeroes = [
            ['name' => 'Tupac Qatari', 'an_integer' => rand(0, 100)],
            ['name' => 'Moctezuma', 'an_integer' => rand(0, 100)],
            ['name' => 'Guaicaipuro', 'an_integer' => rand(0, 100)]
        ];

        $heroes = $correctHeroes;
        foreach ($heroes as &$hero) {
            $hero['an_integer'] = floatval((string)$hero['an_integer'] . '.00The');
        }

        $this->dbal->upsert('heroes', $heroes, ['string', 'integer']);

        foreach ($correctHeroes as $correctHero) {
            $I->seeInDatabase('heroes', $correctHero);
        }
    }

    /**
     * Same as updateMultipleWithTypes(), but with a dictionary of types instead of a flat array.
     * @group upsert
     * @group multiple
     */
    public function updateMultipleWithTypesDictionary(UnitTester $I)
    {
        $correctHeroes = [
            ['name' => 'Tupac Qatari', 'an_integer' => rand(0, 100)],
            ['name' => 'Moctezuma', 'an_integer' => rand(0, 100)],
            ['name' => 'Guaicaipuro', 'an_integer' => rand(0, 100)]
        ];

        $heroes = $correctHeroes;
        foreach ($heroes as &$hero) {
            $hero['an_integer'] = floatval((string)$hero['an_integer'] . '.00The');
        }

        $this->dbal->upsert('heroes', $heroes, ['name' => 'string', 'an_integer' => 'integer']);

        foreach ($correctHeroes as $correctHero) {
            $I->seeInDatabase('heroes', $correctHero);
        }
    }

    /**
     * This will only update the `a_string` field, no matter what other fields are passed (note that `an_integer` 
     * doesn't get updated)
     * @group upsert
     * @group multiple
     */
    public function updateMultipleOnlySpecificColumns(UnitTester $I)
    {
        $correctHeroes = [
            ['name' => 'Tupac Qatari', 'a_string' => sq('A string_')],
            ['name' => 'Moctezuma', 'a_string' => sq('A string_')],
            ['name' => 'Guaicaipuro', 'a_string' => sq('A string_')]
        ];

        $heroes = $correctHeroes;
        foreach ($heroes as &$hero) {
            $hero['an_integer'] = rand(0, 100);
        }

        $this->dbal->upsert('heroes', $heroes, [], ['a_string']);

        foreach ($correctHeroes as $correctHero) {
            $I->seeInDatabase('heroes', $correctHero);
        }
    }

    /**
     * @group upsert
     * @group multiple
     */
    public function updateMultipleOnlySpecificColumnsWithTypes(UnitTester $I)
    {
        $correctHeroes = [
            ['name' => 'Tupac Qatari', 'a_string' => sq('A string_')],
            ['name' => 'Moctezuma', 'a_string' => sq('A string_')],
            ['name' => 'Guaicaipuro', 'a_string' => sq('A string_')]
        ];

        $heroes = $correctHeroes;
        foreach ($heroes as &$hero) {
            $hero['an_integer'] = rand(0, 100);
        }

        $this->dbal->upsert('heroes', $heroes, ['string', 'string', 'integer'], ['a_string']);

        foreach ($correctHeroes as $correctHero) {
            $I->seeInDatabase('heroes', $correctHero);
        }
    }

    /**
     * Same as updateMultipleOnlySpecificColumnsWithTypes(), but with a dictionary of types instead of a flat array.
     * @group upsert
     * @group multiple
     */
    public function updateMultipleOnlySpecificColumnsWithTypesDictionary(UnitTester $I)
    {
        $correctHeroes = [
            ['name' => 'Tupac Qatari', 'a_string' => sq('A string_')],
            ['name' => 'Moctezuma', 'a_string' => sq('A string_')],
            ['name' => 'Guaicaipuro', 'a_string' => sq('A string_')]
        ];

        $heroes = $correctHeroes;
        foreach ($heroes as &$hero) {
            $hero['an_integer'] = rand(0, 100);
        }

        $this->dbal->upsert(
            'heroes',
            $heroes,
            ['name' => 'string', 'a_string' => 'string', 'an_integer' => 'integer'],
            ['a_string']
        );

        foreach ($correctHeroes as $correctHero) {
            $I->seeInDatabase('heroes', $correctHero);
        }
    }
}
