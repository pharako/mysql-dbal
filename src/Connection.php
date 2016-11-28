<?php

namespace Pharako\DBAL;

use Doctrine\DBAL\Connection as DBALConnection;

class Connection
{
    protected $connection;

    public function __construct(DBALConnection $connection)
    {
        if ('mysql' != strtolower($connection->getDriver()->getDatabasePlatform()->getName())) {
            throw new \Doctrine\DBAL\DBALException('This is not a MySQL database.');
        }

        $this->connection = $connection;
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->connection, $method), $arguments);
    }

    /**
     * Inserts one or more table rows with specified data.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableExpression The expression of the table to insert data into, quoted or unquoted.
     * @param array  $data      An associative array (multidimensional, if insertion involves multiple records)
     *                          containing column-value pairs.
     * @param array  $types     Types of the inserted data.
     *
     * @return integer The number of affected rows.
     */
    public function insert($tableExpression, array $data, array $types = array())
    {
        if (empty($data)) {
            return $this->connection->executeUpdate('INSERT INTO ' . $tableExpression . ' ()' . ' VALUES ()');
        }

        return $this->isArrayMultidimensional($data) ?
               $this->insertMultiple($tableExpression, $data, $types) :
               $this->connection->insert($tableExpression, $data, $types);
    }

    /**
     * Executes a ON DUPLICATE KEY UPDATE statement on a MySQL table.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableExpression  The expression of the table to update quoted or unquoted.
     * @param array  $data       An associative array (multidimensional, if insertion/upate involves multiple records)
     *                           containing column-value pairs.
     * @param array  $types      Types of the merged $data and $identifier arrays in that order.
     * @param array  $columnsToUpdate   Columns to be updated in case of duplicates.
     *
     * @return integer The number of affected rows.
     */
    public function upsert($tableExpression, array $data, array $types = array(), array $columnsToUpdate = array())
    {
        if (!$this->isArrayMultidimensional($data)) {
            $data = array($data);
        }

        return $this->upsertMultiple($tableExpression, $data, $types, $columnsToUpdate);
    }

    /**
     * Inserts multiple table rows with specified data.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableExpression The expression of the table to insert data into, quoted or unquoted.
     * @param array  $data      A multidimensional array containing subarrays of column-value pairs.
     * @param array  $types     Types of the inserted data.
     *
     * @return integer The number of affected rows.
     */
    protected function insertMultiple($tableExpression, array $data, array $types = array())
    {
        $values = array();
        $params = array();

        foreach ($data as $index => $array) {
            $values[] = '(' . implode(', ', array_fill(0, count($array), '?')) . ')';
            $params = array_merge($params, array_values($array));
        }

        $first = reset($data);
        $sql = 'INSERT INTO ' . $tableExpression . ' (' . implode(', ', array_keys($first)) . ') VALUES '
               . implode(', ', $values);

        $types = is_string(key($types)) ? $this->extractTypeValues($first, $types) : $types;
        $finalTypes = $this->repeatTypeValues($data, $types);

        return $this->connection->executeUpdate($sql, $params, $finalTypes);
    }

    /**
     * Executes a ON DUPLICATE KEY UPDATE statement on a MySQL table.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableExpression  The expression of the table to update quoted or unquoted.
     * @param array  $data       A multidimensional array containing subarrays of column-value pairs.
     * @param array  $types      Types of the merged $data and $identifier arrays in that order.
     * @param array  $columnsToUpdate   Columns to be updated in case of duplicates.
     *
     * @return integer The number of affected rows.
     */
    protected function upsertMultiple($tableExpression, array $data, array $types = array(), array $columnsToUpdate = array())
    {
        $first = reset($data);
        $columnsToUpdate = !empty($columnsToUpdate) ? $columnsToUpdate : array_keys($first);
        $updates = array();

        foreach ($columnsToUpdate as $columnName) {
            $updates[] = $columnName . '=' . 'VALUES(' . $columnName . ')';
        }

        $values = array();
        $params = array();

        foreach ($data as $index => $array) {
            $values[] = '(' . implode(', ', array_fill(0, count($array), '?')) . ')';
            $params = array_merge($params, array_values($array));

        }

        $sql = 'INSERT INTO ' . $tableExpression . ' (' . implode(', ', array_keys($first)) . ') VALUES '
               . implode(', ', $values) . ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);

        $types = is_string(key($types)) ? $this->extractTypeValues($first, $types) : $types;
        $finalTypes = $this->repeatTypeValues($data, $types);

        return $this->connection->executeUpdate($sql, $params, $finalTypes);
    }

    /**
     * Checks if an array is multidimensional (contains multiple entries).
     *
     * @param array  $data       An array (associative or not).
     *
     * @return boolean True if the array is multidimensional, false otherwise.
     */
    private function isArrayMultidimensional(array $array) {
        return count($array) != count($array, COUNT_RECURSIVE);
    }

    /**
     * Gets repeated type list from two associate key lists of data and types.
     *
     * @param array $data
     * @param array $types
     *
     * @return array
     */
    private function repeatTypeValues(array $data, array $types = array())
    {
        if (empty($types)) {
            return array();
        }

        $count = count(array_keys($data));
        $repeated = array_fill(0, $count, $types);
        return call_user_func_array('array_merge', $repeated);
    }

    /**
     * Extracts ordered type list from two associate key lists of data and types.
     *
     * @param array $data
     * @param array $types
     *
     * @return array
     */
    private function extractTypeValues(array $data, array $types)
    {
        $typeValues = array();

        foreach ($data as $k => $_) {
            $typeValues[] = isset($types[$k])
                ? $types[$k]
                : \PDO::PARAM_STR;
        }

        return $typeValues;
    }
}
