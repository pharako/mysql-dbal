<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends \Codeception\Module
{
    public function getDbConfig()
    {
        $dbConfig = $this->getModule('Db')->_getConfig();

        $dsn = explode(';', $dbConfig['dsn']);
        $host = substr($dsn[0], strpos($dsn[0], '=') + 1);
        $dbName = substr($dsn[1], strpos($dsn[1], '=') + 1);

        return [
            'dbname' => $dbName,
            'host' => $host,
            'user' => $dbConfig['user'],
            'password' => $dbConfig['password'],
            'driver' => 'pdo_mysql'
        ];
    }
}
