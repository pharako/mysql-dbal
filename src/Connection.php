<?php

namespace Pharako\DBAL;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\ParameterType;

class Connection extends DBALConnection implements DriverConnection
{
    /**
     * Inserts one or more table rows with specified data.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableExpression   The expression of the table to insert data into, quoted or unquoted.
     * @param array  $data              An associative array (multidimensional, if insertion involves multiple records)
     * containing column-value pairs.
     * @param array  $types             Types of the inserted data.
     *
     * @return int The number of affected rows.
     */
    public function insert($tableExpression, array $data, array $types = [])
    {
        if (empty($data)) {
            return $this->executeUpdate('INSERT INTO ' . $tableExpression . ' ()' . ' VALUES ()');
        }

        return $this->isArrayMultidimensional($data) ?
               $this->insertMultiple($tableExpression, $data, $types) :
               parent::insert($tableExpression, $data, $types);
    }

    /**
     * Executes a ON DUPLICATE KEY UPDATE statement on a MySQL table.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableExpression   The expression of the table to update quoted or unquoted.
     * @param array  $data              An associative array (multidimensional, if insertion/update involves
     * multiple records) containing column-value pairs.
     * @param array  $types             Types of the merged $data and $identifier arrays in that order.
     * @param array  $columnsToUpdate   Columns to be updated in case of duplicates.
     *
     * @return int The number of affected rows.
     */
    public function upsert($tableExpression, array $data, array $types = [], array $columnsToUpdate = [])
    {
        if (!$this->isArrayMultidimensional($data)) {
            $data = [$data];
        }

        return $this->upsertMultiple($tableExpression, $data, $types, $columnsToUpdate);
    }

    /**
     * Inserts multiple table rows with specified data.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableExpression   The expression of the table to insert data into, quoted or unquoted.
     * @param array  $data              A multidimensional array containing subarrays of column-value pairs.
     * @param array  $types             Types of the inserted data.
     *
     * @return int The number of affected rows.
     */
    protected function insertMultiple($tableExpression, array $data, array $types = [])
    {
        $values = [];
        $params = [];

        foreach ($data as $index => $array) {
            $values[] = '(' . implode(', ', array_fill(0, count($array), '?')) . ')';
            array_push($params, ...array_values($array));
        }

        $first = reset($data);
        $sql = 'INSERT INTO ' . $tableExpression . ' (' . implode(', ', array_keys($first)) . ') VALUES '
               . implode(', ', $values);

        $types = is_string(key($types)) ? $this->extractTypeValues($first, $types) : $types;
        $finalTypes = $this->repeatTypeValues($data, $types);

        return $this->executeUpdate($sql, $params, $finalTypes);
    }

    /**
     * Executes a ON DUPLICATE KEY UPDATE statement on a MySQL table.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableExpression   The expression of the table to update quoted or unquoted.
     * @param array  $data              A multidimensional array containing subarrays of column-value pairs.
     * @param array  $types             Types of the merged $data and $identifier arrays in that order.
     * @param array  $columnsToUpdate   Columns to be updated in case of duplicates.
     *
     * @return int The number of affected rows.
     */
    protected function upsertMultiple($tableExpression, array $data, array $types = [], array $columnsToUpdate = [])
    {
        $first = reset($data);
        $columnsToUpdate = !empty($columnsToUpdate) ? $columnsToUpdate : array_keys($first);
        $updates = [];

        foreach ($columnsToUpdate as $columnName) {
            $updates[] = $columnName . '=' . 'VALUES(' . $columnName . ')';
        }

        $values = [];
        $params = [];

        foreach ($data as $index => $array) {
            $values[] = '(' . implode(', ', array_fill(0, count($array), '?')) . ')';
            array_push($params, ...array_values($array));
        }

        $sql = 'INSERT INTO ' . $tableExpression . ' (' . implode(', ', array_keys($first)) . ') VALUES '
               . implode(', ', $values) . ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);

        $types = is_string(key($types)) ? $this->extractTypeValues($first, $types) : $types;
        $finalTypes = $this->repeatTypeValues($data, $types);

        return $this->executeUpdate($sql, $params, $finalTypes);
    }

    /**
     * Extract ordered type list from an ordered column list and type map.
     *
     * @param array<int, string>     $columnList
     * @param array<int, int|string> $types      The query parameter types.
     *
     * @return array<int, int>|array<int, string>
     */
    private function extractTypeValues(array $columnList, array $types)
    {
        $typeValues = [];

        foreach ($columnList as $columnName) {
            $typeValues[] = $types[$columnName] ?? ParameterType::STRING;
        }

        return $typeValues;
    }

    /**
     * Checks if an array is multidimensional (contains multiple entries).
     *
     * @param array  $data       An array (associative or not).
     *
     * @return bool True if the array is multidimensional, false otherwise.
     */
    private function isArrayMultidimensional(array $array)
    {
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
    private function repeatTypeValues(array $data, array $types = [])
    {
        if (empty($types)) {
            return [];
        }

        $count = count(array_keys($data));
        $repeated = array_fill(0, $count, $types);

        return array_merge(...$repeated);
    }
}
