<?php

use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\PDO\MySql\Driver;
use Pharako\DBAL\Connection;

class InsertSingleCest
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
     * @group single
     */
    public function insertSingle(UnitTester $I)
    {
        $hero = [
            'name' => sq('Sepé_'),
            'a_string' => sq('A string_'),
            'an_integer' => rand(0, 100)
        ];

        $this->dbal->insert('heroes', $hero);

        $I->seeInDatabase('heroes', $hero);
    }

    /**
     * @group insert
     * @group single
     */
    public function insertSingleMultidimensional(UnitTester $I)
    {
        $heroes = [
            [
                'name' => sq('Sepé_'),
                'a_string' => sq('A string_'),
                'an_integer' => rand(0, 100)
            ]
        ];

        $this->dbal->insert('heroes', $heroes);

        $I->seeInDatabase('heroes', $heroes[0]);
    }

    /**
     * Passing an array with Doctrine types guarantees parameter binding (note that 'an_integer' is being cast to a 
     * float but still correctly inserted as an integer)
     * @group insert
     * @group single
     */
    public function insertSingleWithTypes(UnitTester $I)
    {
        $correctHero = [
            'name' => sq('Sepé_'),
            'a_string' => sq('A string_'),
            'an_integer' => rand(0, 100)
        ];

        $hero = $correctHero;
        $hero['an_integer'] = floatval((string)$correctHero['an_integer'] . '.00The');

        $this->dbal->insert('heroes', $hero, ['string', 'string', 'integer']);

        $I->seeInDatabase('heroes', $correctHero);
    }

    /**
     * Same as insertSingleWithTypes(), but with a dictionary of types instead of a flat array.
     * @group insert
     * @group single
     */
    public function insertSingleWithTypesDictionary(UnitTester $I)
    {
        $correctHero = [
            'name' => sq('Sepé_'),
            'a_string' => sq('A string_'),
            'an_integer' => rand(0, 100)
        ];

        $hero = $correctHero;
        $hero['an_integer'] = floatval((string)$correctHero['an_integer'] . '.00The');

        $this->dbal->insert('heroes', $hero, ['name' => 'string', 'a_string' => 'string', 'an_integer' => 'integer']);

        $I->seeInDatabase('heroes', $correctHero);
    }

    /**
     * @group insert
     * @group single
     */
    public function insertSingleWithTypesMultidimensional(UnitTester $I)
    {
        $correctHeroes = [
            [
                'name' => sq('Sepé_'),
                'a_string' => sq('A string_'),
                'an_integer' => rand(0, 100)
            ]
        ];

        $heroes = $correctHeroes;
        $heroes[0]['an_integer'] = floatval((string)$correctHeroes[0]['an_integer'] . '.00The');

        $this->dbal->insert('heroes', $heroes, ['string', 'string', 'integer']);

        $I->seeInDatabase('heroes', $correctHeroes[0]);
    }

    /**
     * Same as insertSingleWithTypesMultidimensional(), but with a dictionary of types instead of a flat array.
     * @group insert
     * @group single
     */
    public function insertSingleWithTypesDictionaryMultidimensional(UnitTester $I)
    {
        $correctHeroes = [
            [
                'name' => sq('Sepé_'),
                'a_string' => sq('A string_'),
                'an_integer' => rand(0, 100)
            ]
        ];

        $heroes = $correctHeroes;
        $heroes[0]['an_integer'] = floatval((string)$correctHeroes[0]['an_integer'] . '.00The');

        $this->dbal->insert('heroes', $heroes, ['name' => 'string', 'a_string' => 'string', 'an_integer' => 'integer']);

        $I->seeInDatabase('heroes', $correctHeroes[0]);
    }
}
