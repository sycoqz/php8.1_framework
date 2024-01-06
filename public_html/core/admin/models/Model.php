<?php

namespace core\admin\models;

use core\base\controllers\Singleton;
use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\models\BaseModel;
use core\base\settings\Settings;

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

    /**
     * @throws DbException
     */
    public function search($data, $currentTable = false, $qty = false)
    {

        $dbTables = $this->showTables();

        $data = addslashes($data);

        $arr = preg_split('/(,|\.)?\s+/', $data, 0, PREG_SPLIT_NO_EMPTY);

        $searchArr = [];

        $order = [];

        for (;;) {

            if (!$arr) break;

            $searchArr[] = implode(' ', $arr);

            unset($arr[count($arr) - 1]);

        }

        // Приоритет объектов
        $correctCurrentTable = false;

        $projectTables = Settings::get('projectTables');

        if (!$projectTables) throw new RouteException('Ошибка поиска. Нет раздела админ панели.');

        foreach ($projectTables as $table => $item) {

            if (!in_array($table, $dbTables)) continue;

            $searchRows = [];

            $orderRows = ['name'];

            $fields = [];

            $columns = $this->showColumns($table);

            $fields[] = $columns['id_row'] . ' as id';

            $fieldName = isset($columns['name']) ? "CASE WHEN name <> '' THEN name " : '';

            foreach ($columns as $col => $value) {

                if ($col !== 'name' && stripos($col, 'name') !== false) {

                    if (!$fieldName) $fieldName = 'CASE ';

                    $fieldName .= "WHEN $col <> '' THEN $col ";

                }

                // Признак формирования
                if (isset($value['Type']) && (stripos($value['Type'], 'char') !== false ||
                        stripos($value['Type'], 'text') !== false)) {

                        $searchRows[] = $col;

                }

            }

            if ($fieldName) $fields[] = $fieldName . 'END as name';
            else $fields[] = $columns['id_row'] . ' as name';

            $fields[] = "('$table') AS table_name";

            $result = $this->createWhereOrder($searchRows, $searchArr, $orderRows, $table);

            $where = $result['where'];

            !$order && $order = $result['order'];

            if ($table === $currentTable) {

                $correctCurrentTable = true;

                $fields[] = "('current_table') AS current_table";

            }

            if (isset($where)) {

                $this->buildUnion($table, [
                    'fields' => $fields,
                    'where' => $where,
                    'no_concat' => true
                ]);

            }

        }

        $orderDirection = null;

        if (isset($order)) {

            $order = ($correctCurrentTable ? 'current_table DESC, ' : '') . '(' . implode('+', $order) . ')';

            $orderDirection = 'DESC';

        }

        $result = $this->getUnion([
            //'type' => 'all',
            //'pagination' => [],
            //'limit' => 3,
            'order' => $order,
            'order_direction' => $orderDirection
        ]);

        $a = 1;

    }

    /**
     * @throws DbException
     */
    protected function createWhereOrder(array $searchRows, array $searchArr, array $orderRows, string $table): array
    {

        $where = '';

        $order = [];

        if ($searchRows && $searchArr) {

            $columns = $this->showColumns($table);

            if ($columns) {

                $where = '(';

                foreach ($searchRows as $row) {

                    $where .= '(';

                    foreach ($searchArr as $item) {

                        if (in_array($row, $orderRows)) {

                            $str = "($row LIKE '%$item%')";

                            if (!in_array($str, $order)) {

                                $order[] = $str;

                            }

                        }

                        if (isset($columns[$row])) {

                            $where .= "$row LIKE '%$item%' OR ";

                        }

                    }

                    $where = preg_replace('/\)?\s*or\s*\(?$/i', '', $where) . ') OR ';

                }

                $where && $where = preg_replace('/\s*or\s*$/i', '', $where) . ')';

            }

        }

        return compact('where', 'order');

    }

}