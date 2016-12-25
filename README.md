[![Build Status](https://travis-ci.org/pharako/mysql-dbal.svg?branch=master)](https://travis-ci.org/pharako/mysql-dbal) [![Latest Stable Version](https://poser.pugx.org/pharako/mysql-dbal/v/stable)](https://packagist.org/packages/pharako/mysql-dbal) [![Total Downloads](https://poser.pugx.org/pharako/mysql-dbal/downloads)](https://packagist.org/packages/pharako/mysql-dbal) [![Latest Unstable Version](https://poser.pugx.org/pharako/mysql-dbal/v/unstable)](https://packagist.org/packages/pharako/mysql-dbal) [![License](https://poser.pugx.org/pharako/mysql-dbal/license)](https://packagist.org/packages/pharako/mysql-dbal)

MySQL DBAL
==========

MySQL extensions for [Doctrine DBAL](https://github.com/doctrine/dbal).

`Pharako\DBAL\Connection` is an extension of `Doctrine\DBAL\Connection`, which means all functionality you get from the latter is also present in the former, with a few add-ons specific to MySQL:

* multiple inserts
* single and multiple upserts (update records if they exist, insert them otherwise)

# Requirements

PHP 5.6 or above.

# Installation

Install via Composer:

```SHELL
$ composer require pharako/mysql-dbal
```

# Usage

## Instantiation and configuration

Most web frameworks will have some sort of *service injection* functionality to help you with configuration, but nothing stops you from doing it by hand.

### Manually

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

### Symfony 2 and above

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

# Extra functionality

## Multiple inserts

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

## Single and multiple upserts (update if present, insert if new)

Before using this functionality, make sure you read [*Caution with those upserts*](#caution-with-those-upserts) below.

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

# Caution with those upserts

Upserts in MySQL are more involved than simple inserts and updates. Because they rely on the `ON DUPLICATE KEY UPDATE` clause, it is advised that upserts only be used on tables containing one unique key at most (naturally, if a table has not a single unique key defined, the upsert will work as a regular insert). Or, as the [official documentation](https://dev.mysql.com/doc/refman/5.7/en/insert-on-duplicate.html) puts it: *In general, you should try to avoid using an `ON DUPLICATE KEY UPDATE` clause on tables with multiple unique indexes* and *"[...] an `INSERT ... ON DUPLICATE KEY UPDATE` statement against a table having more than one unique or primary key is also marked as unsafe"*.

Despite that, if you are feeling adventurous, you can definitely run upserts on tables containing multiple indexes so long as you are careful with the column(s) being updated when a duplicate is found - if one such column is itself a unique key, you are guaranteed to get inconsistencies in your final data. So keep in mind that the results from an upsert in such circumstances may be surprisingly different than you would expect.

By and large, upserts will function as expected when using a single index, no matter how complex that index is - for example, a table with a single multiple-column unique index should work just fine.

That being said, if you want to play it safe, avoid using upserts on tables with multiple indexes and, when the upsert involves a single multiple-column unique index, try to tighten your tests to make sure you get the expected results when the upsert inserts your records as well as when it (potentially) updates them.

Also, be aware of some limitations when using upserts on a master/slave installation (see the full story [here](http://bugs.mysql.com/bug.php?id=58637)).

