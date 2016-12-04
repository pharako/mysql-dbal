# MySQL DBAL

MySQL extensions for [Doctrine DBAL](https://github.com/doctrine/dbal).

`Pharako\DBAL\Connection` is an extension of `Doctrine\DBAL\Connection`, which means all functionality you get from the latter is also present in the former, with a few add-ons specific to MySQL:

* multiple inserts
* single and multiple upserts (update records if they exist, insert them otherwise)

## Installation

Install via Composer:

```SHELL
$ composer require "pharako/mysql-dbal"
```

## Usage

### Instantiation and configuration

Most web frameworks will have some sort of *service injection* functionality to help you with configuration, but nothing stops you from doing it by hand.

#### Manually

```PHP
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Pharako\DBAL\Connection;

$params = [
    'dbname' => 'my_db',
    'host' => 'localhost',
    'user' => 'username',
    'password' => '***',
    'driver' => 'pdo_mysql'
];

$dbal = new Connection(
    $params,
    new Driver(),
    new Configuration(),
    new EventManager()
);
```

#### Symfony 2 and above

Just specify the DBAL connection class under `wrapper_class` in `config.yml`. All the other configurations should remain the same:

```YAML
doctrine:
    dbal:
        dbname: %database_name%
        host: %database_host%
        port: %database_port%
        user: %database_user%
        password: %database_password%
        driver: pdo_mysql
        wrapper_class: 'Pharako\DBAL\Connection'
```

You can read [Doctrine DBAL Configuration](http://symfony.com/doc/current/reference/configuration/doctrine.html#doctrine-dbal-configuration) for more information on `wrapper_class` and other options.

## Extra functionality

### Multiple inserts

You can insert multiple records with one call - this will hit the database only once:

```PHP
$data = [
    [
        'name' => 'Foo',
        'family_name' => 'Bar'
    ],
    [
        'name' => 'Fuzz',
        'family_name' => 'Bazz'
    ]
];

$dbal->insert('my_table', $data);
```

### Single or multiple upserts (update if present, insert if new)

Assuming the `name` field is a unique key in the table structure, the first two records will have their `family_name` fields updated to `Rab` and `Zabb`, respectivelly, and the last one will be inserted:

```PHP
$data = [
    [
        'name' => 'Foo',
        'family_name' => 'Rab'
    ],
    [
        'name' => 'Fuzz',
        'family_name' => 'Zabb'
    ],
    [
        'name' => 'New',
        'family_name' => 'Foo'
    ]
];

$dbal->upsert('my_table', $data);
```

Again, this will hit the database only once.

