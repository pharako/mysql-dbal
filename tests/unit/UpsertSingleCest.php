<?php

use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Pharako\DBAL\Connection;

class UpsertSingleCest
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
     * @group single
     */
    public function insertSingle(UnitTester $I)
    {
        $hero = [
            'name' => 'Sepé',
            'a_string' => sq('A string_'),
            'an_integer' => rand(0, 100)
        ];

        $this->dbal->upsert('heroes', $hero);

        $I->seeInDatabase('heroes', $hero);
    }

    /**
     * @group upsert
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

        $this->dbal->upsert('heroes', $heroes);

        $I->seeInDatabase('heroes', $heroes[0]);
    }

    /**
     * Passing an array with Doctrine types guarantees parameter binding (note that 'an_integer' is being cast to a 
     * float but still correctly inserted as an integer)
     * @group upsert
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
        $hero['an_integer'] = floatval((string)$hero['an_integer'] . '.00The');

        $this->dbal->upsert('heroes', $hero, ['string', 'string', 'integer']);

        $I->seeInDatabase('heroes', $correctHero);
    }

    /**
     * Same as insertSingleWithTypes(), but with a dictionary of types instead of a flat array.
     * @group upsert
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
        $hero['an_integer'] = floatval((string)$hero['an_integer'] . '.00The');

        $this->dbal->upsert('heroes', $hero, ['name' => 'string', 'a_string' => 'string', 'an_integer' => 'integer']);

        $I->seeInDatabase('heroes', $correctHero);
    }

    /**
     * @group upsert
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

        $this->dbal->upsert('heroes', $heroes, ['string', 'string', 'integer']);

        $I->seeInDatabase('heroes', $correctHeroes[0]);
    }

    /**
     * Same as insertSingleWithTypesMultidimensional(), but with a dictionary of types instead of a flat array.
     * @group upsert
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

        $this->dbal->upsert(
            'heroes',
            $heroes,
            ['name' => 'string', 'an_string' => 'string', 'an_integer' => 'integer']
        );

        $I->seeInDatabase('heroes', $correctHeroes[0]);
    }

    /**
     * All fields, except unique key (`name`), get updated
     * @group upsert
     * @group single
     */
    public function updateSingle(UnitTester $I)
    {
        $hero = [
            'name' => 'Sepé',
            'a_string' => sq('A string_'),
            'an_integer' => rand(0, 100)
        ];

        $this->dbal->upsert('heroes', $hero);

        $I->seeInDatabase('heroes', $hero);
    }

    /**
     * @group upsert
     * @group single
     */
    public function updateSingleMultidimensional(UnitTester $I)
    {
        $heroes = [
            [
                'name' => 'Sepé',
                'a_string' => sq('A string_'),
                'an_integer' => rand(0, 100)
            ]
        ];

        $this->dbal->upsert('heroes', $heroes[0]);

        $I->seeInDatabase('heroes', $heroes[0]);
    }

    /**
     * This will only update the `a_string` field, no matter what other fields are passed (note that `an_integer` 
     * doesn't get updated)
     * @group upsert
     * @group single
     */
    public function updateSingleOnlySpecificColumns(UnitTester $I)
    {
        $correctHero = [
            'name' => 'Sepé',
            'a_string' => sq('A string_')
        ];

        $hero = $correctHero;
        $hero['an_integer'] = rand(0, 100);

        $this->dbal->upsert('heroes', $hero, [], ['a_string']);

        $I->seeInDatabase('heroes', $correctHero);
    }

    /**
     * @group upsert
     * @group single
     */
    public function updateSingleOnlySpecificColumnsMultidimensional(UnitTester $I)
    {
        $correctHeroes = [
            [
                'name' => 'Sepé',
                'a_string' => sq('A string_')
            ]
        ];

        $heroes = $correctHeroes;
        $heroes[0]['an_integer'] = rand(0, 100);

        $this->dbal->upsert('heroes', $heroes, [], ['a_string']);

        $I->seeInDatabase('heroes', $correctHeroes[0]);
    }

    /**
     * @group upsert
     * @group single
     */
    public function updateSingleWithTypes(UnitTester $I)
    {
        $correctHero = ['name' => 'Sepé', 'an_integer' => rand(0, 100)];

        $hero = $correctHero;
        $hero['an_integer'] = floatval((string)$hero['an_integer'] . '.00The');

        $this->dbal->upsert('heroes', $hero, ['string', 'integer']);

        $I->seeInDatabase('heroes', $correctHero);
    }

    /**
     * Same as updateSingleWithTypes(), but with a dictionary of types instead of a flat array.
     * @group upsert
     * @group single
     */
    public function updateSingleWithTypesDictionary(UnitTester $I)
    {
        $correctHero = ['name' => 'Sepé', 'an_integer' => rand(0, 100)];

        $hero = $correctHero;
        $hero['an_integer'] = floatval((string)$hero['an_integer'] . '.00The');

        $this->dbal->upsert('heroes', $hero, ['name' => 'string', 'an_integer' => 'integer']);

        $I->seeInDatabase('heroes', $correctHero);
    }

    /**
     * @group upsert
     * @group single
     */
    public function updateSingleWithTypesMultidimensional(UnitTester $I)
    {
        $correctHeroes = [
            [
                'name' => 'Sepé',
                'an_integer' => rand(0, 100)
            ]
        ];

        $heroes = $correctHeroes;
        $heroes[0]['an_integer'] = floatval((string)$correctHeroes[0]['an_integer'] . '.00The');

        $this->dbal->upsert('heroes', $heroes, ['string', 'integer']);

        $I->seeInDatabase('heroes', $correctHeroes[0]);
    }

    /**
     * Same as updateSingleWithTypesMultidimensional(), but with a dictionary of types instead of a flat array.
     * @group upsert
     * @group single
     */
    public function updateSingleWithTypesDictionaryMultidimensional(UnitTester $I)
    {
        $correctHeroes = [
            [
                'name' => 'Sepé',
                'an_integer' => rand(0, 100)
            ]
        ];

        $heroes = $correctHeroes;
        $heroes[0]['an_integer'] = floatval((string)$correctHeroes[0]['an_integer'] . '.00The');

        $this->dbal->upsert('heroes', $heroes, ['name' => 'string', 'an_integer' => 'integer']);

        $I->seeInDatabase('heroes', $correctHeroes[0]);
    }

    /**
     * @group upsert
     * @group single
     */
    public function updateSingleWithTypesMultidimensionalOnlySpecificColumns(UnitTester $I)
    {
        $correctHeroes = [
            [
                'name' => 'Sepé',
                'a_string' => sq('A string_')
            ]
        ];

        $heroes = $correctHeroes;
        $heroes[0]['an_integer'] = rand(0, 100);

        $this->dbal->upsert('heroes', $heroes, ['string', 'string', 'integer'], ['a_string']);

        $I->seeInDatabase('heroes', $correctHeroes[0]);
    }

    /**
     * Same as updateSingleWithTypesMultidimensionalOnlySpecificColumns(),
     * but with a dictionary of types instead of a flat array.
     * @group upsert
     * @group single
     */
    public function updateSingleWithTypesDictionaryMultidimensionalOnlySpecificColumns(UnitTester $I)
    {
        $correctHeroes = [
            [
                'name' => 'Sepé',
                'a_string' => sq('A string_')
            ]
        ];

        $heroes = $correctHeroes;
        $heroes[0]['an_integer'] = rand(0, 100);

        $this->dbal->upsert(
            'heroes',
            $heroes,
            ['name' => 'string', 'a_string' => 'string', 'an_integer' => 'integer'],
            ['a_string']
        );

        $I->seeInDatabase('heroes', $correctHeroes[0]);
    }
}
