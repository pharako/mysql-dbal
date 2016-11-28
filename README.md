# MySQL DBAL

MySQL extensions for [Doctrine DBAL](https://github.com/doctrine/dbal).

`Pharako\DBAL\Connection` is just a wrapper for `Doctrine\DBAL\Connection`, which means all functionality you get from the latter is also present in the former, with a few add-ons (see below).

## Installation

Install via Composer:

```SHELL
$ composer require "pharako/mysql-dbal"
```

## Configuration

You can use the standard Doctrine initialization procedures to get an instance of the DBAL connection.

You start by defining the database connection parameters:

```PHP
use Pharako\DBAL\Connection;

$connectionParams = [
    'dbname' => 'mydb',
    'user' => 'user',
    'password' => 'secret',
    'host' => 'localhost',
    'driver' => 'pdo_mysql'
];
```

Then you get a Doctrine DBAL connection:

```PHP
$doctrineDBAL = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);
```

And wrap it with a DBAL connection:

```PHP
$dbal = new Connection($doctrineDBAL);
```

Or simply:

```PHP
$connectionParams = [
    'dbname' => 'mydb',
    'user' => 'user',
    'password' => 'secret',
    'host' => 'localhost',
    'driver' => 'pdo_mysql'
];

$dbal = new Connection(\Doctrine\DBAL\DriverManager::getConnection($connectionParams));
```

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

### Single or multiple upserts (update if present or insert if new)

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

