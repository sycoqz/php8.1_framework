<?php

namespace core\admin\models;

use core\base\controllers\Singleton;
use core\base\exceptions\DbException;
use core\base\models\BaseModel;

class Model extends BaseModel
{

    use Singleton;

    /**
     * @throws DbException
     */
    public function showForeignKeys(string $table, bool|string $key = false): bool|array|int|string|null
    {

        $db = DB_NAME;
        $where = '';

        if ($key) $where = "AND COLUMN_NAME = '$key' LIMIT 1";

        $query = "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE
                      WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$table' AND
                        CONSTRAINT_NAME <> 'PRIMARY' AND REFERENCED_TABLE_NAME IS NOT NULL $where";

        return $this->query($query);

    }

    /**
     * @throws DbException
     */
    public function updateMenuPosition(string $table, string $row, array|bool $where, string $end_pos, array $update_rows = [])
    {

        if ($update_rows && isset($update_rows['where'])) {

            $update_rows['operand'] = $update_rows['operand'] ?? ['='];

            if ($where) {

                $old_data = $this->read($table, [
                    'fields' => [$update_rows['where'], $row],
                    'where' => $where
                ])[0];

                $start_pos = $old_data[$row];

                // Получение кол-ва элементов в таблице, у которых был изменён родитель
                if ($old_data[$update_rows['where']] !== $_POST[$update_rows['where']]) {

                    $pos = $this->read($table, [
                        'fields' => ['COUNT(*) as count'],
                        'where' => [$update_rows['where'] => $old_data[$update_rows['where']]],
                        'no_concat' => true
                    ])[0]['count'];

                    if ($start_pos != $pos) {

                        $update_where = $this->createWhere([
                            'where' => [$update_rows['where'] => $old_data[$update_rows['where']]],
                            'operand' => $update_rows['operand']
                        ]);

                        $query = "UPDATE $table SET $row = $row - 1 $update_where AND $row <= $pos AND $row > $start_pos";

                        $this->query($query, 'u');

                    }

                    $start_pos = $this->read($table, [
                        'fields' => ['COUNT(*) as count'],
                        'where' => [$update_rows['where'] => $_POST[$update_rows['where']]],
                        'no_concat' => true
                    ])[0]['count'] + 1;

                }

            } else {

                $start_pos = $this->read($table, [
                        'fields' => ['COUNT(*) as count'],
                        'where' => [$update_rows['where'] => $_POST[$update_rows['where']]],
                        'no_concat' => true
                    ])[0]['count'] + 1;

            }

            if (array_key_exists($update_rows['where'], $_POST)) $where_equal = $_POST[$update_rows['where']];
            elseif (isset($old_data[$update_rows['where']])) $where_equal = $old_data[$update_rows['where']];
            else $where_equal = NULL;

            $db_where = $this->createWhere([
                'where' => [$update_rows['where'] => $where_equal],
                'operand' => $update_rows['operand']
            ]);

        } else {

            if ($where) {

                $start_pos = $this->read($table, [
                    'fields' => [$row],
                    'where' => $where
                ])[0][$row];

            } else {

                $start_pos = $this->read($table, [
                    'fields' => ['COUNT(*) as count'],
                    'no_concat' => true
                ])[0]['count'] + 1;

            }

        }

        $db_where = isset($db_where) ? $db_where . ' AND' : 'WHERE';

        if ($start_pos < $end_pos)
            $query = "UPDATE $table SET $row = $row - 1 $db_where $row <= $end_pos AND $row > $start_pos";
        elseif ($start_pos > $end_pos)
            $query = "UPDATE $table SET $row = $row + 1 $db_where $row >= $end_pos AND $row < $start_pos";
        else return;

        return $this->query($query, 'u');

    }

}