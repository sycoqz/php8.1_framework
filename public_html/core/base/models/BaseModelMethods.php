<?php

namespace core\base\models;

use core\base\exceptions\DbException;

abstract class BaseModelMethods
{

    protected int $postNumber;
    protected int $linksNumber;
    protected int $numberPages = 0;
    protected int $page;
    protected int $totalCount;

    protected array $sqlFunc = ['NOW()', 'RAND()'];

    protected array $tableRows = [];

    protected array $union = [];

    /**
     * @throws DbException
     */
    protected function createFields(array $set, string|bool $table = false, bool $join = false): string
    {

        // Проверка для ключей связи при их ненадобности (если передан null)
        if (array_key_exists('fields', $set) && $set['fields'] === null) return '';

        $concat_table = '';

        $alias_table = $table;

        if (!isset($set['no_concat'])) {

            $arr = $this->createTableAlias($table);

            $concat_table = $arr['alias'] . '.';

            $alias_table = $arr['alias'];

        }

        $fields = '';

        $join_structure = false;

        if ((isset($set['join_structure']) || $join) && $table) {

            $join_structure = true;

             $this->showColumns($table);

             if (isset($this->tableRows[$table]['multi_id_row'])) $set['fields'] = [];

        }

        if (empty($set['fields']) || !is_array($set['fields'])) {

            if (!$join) {

                $fields = $concat_table . '*,';

            } else {

                foreach ($this->tableRows[$alias_table] as $key => $item) {

                    if ($key !== 'id_row' && $key !== 'multi_id_row') {

                        $fields .= $concat_table . $key . ' as TABLE' . $alias_table . 'TABLE_' . $key . ',';

                    }
                    
                }
                
            }

        } else {

            $id_field = false;

            foreach ($set['fields'] as $field) {

                if ($join_structure && !$id_field && $this->tableRows[$alias_table] === $field) {

                    $id_field = true;

                }

                if ($field || $field === null) {

                    if ($field === null) {

                        $fields .= "NULL,";

                        continue;

                    }

                    if ($join && $join_structure) {

                        if (preg_match('/^(.+)?\s+as\s+(.+)/i', $field, $matches)) {

                            $fields .= $concat_table . $matches[1] . ' as TABLE' . $alias_table . 'TABLE_' . $matches[2] . ',';

                        } else {

                            $fields .= $concat_table . $field . ' as TABLE' . $alias_table . 'TABLE_' . $field . ',';

                        }

                    } else {

                        $fields .= (!preg_match('/([^()]*\))|(case\s+.+?\s+end)/i', $field) ? $concat_table : '') . $field . ',';

                    }
                }
            }


            if (!$id_field && $join_structure) {

                if ($join) {

                    $fields .= $concat_table . $this->tableRows[$alias_table]['id_row']
                        . ' as TABLE' . $alias_table . 'TABLE_' . $this->tableRows[$alias_table]['id_row'] . ',';

                } else {

                    $fields .= $concat_table . $this->tableRows[$alias_table]['id_row'] . ',';

                }

            }

        }

        return $fields;

    }

    protected function createOrder(array $set, string|bool $table = false): string
    {

        if (array_key_exists('order', $set) && $set['order'] === null) return '';

        $table = ($table && (!isset($set['no_concat']) || !$set['no_concat']))
                ? $this->createTableAlias($table)['alias'] . '.' : '';

        $order_by = '';

        if (isset($set['order']) && $set['order']) {

            $set['order'] = (array)$set['order'];

            $set['order_direction'] = (isset($set['order_direction']) && $set['order_direction'])
                ? (array)$set['order_direction'] : ['ASC'];

            $order_by = 'ORDER BY ';

            $direct_count = 0;

            foreach ($set['order'] as $order) {

                if (isset($set['order_direction'][$direct_count])) {

                    $order_direction = strtoupper($set['order_direction'][$direct_count]);

                    $direct_count++;

                } else {

                    $order_direction = strtoupper($set['order_direction'][$direct_count-1]);

                }

                if (in_array($order, $this->sqlFunc)) $order_by .= $order . ',';
                elseif (is_int($order)) $order_by .= $order . ' ' . $order_direction . ',';
                else $order_by .= $table . $order . ' ' . $order_direction . ',';

            }

            $order_by = rtrim($order_by, ',');

        }

        return $order_by;
    }

    protected function createWhere(array $set, string|bool $table = false, $instruction = 'WHERE'): string
    {
        $table = ($table && (!isset($set['no_concat']) || !$set['no_concat']))
            ? $this->createTableAlias($table)['alias'] . '.' : '';

        $where = '';

        if (!empty($set['where']) && is_string($set['where'])) {

            return $instruction . ' ' . trim($set['where']);
        }

        if (!empty($set['where']) && is_array($set['where'])) {

            $set['operand'] = (!empty($set['operand']) && is_array($set['operand'])) ? $set['operand'] : ['='];

            $set['condition'] = (!empty($set['condition']) && is_array($set['condition']))
                ? $set['condition'] : ['AND'];

            $where = $instruction;

            $opera_count = 0;
            $cond_count = 0;

            foreach ($set['where'] as $key => $item) {

                $where .= ' ';

                if (isset($set['operand'][$opera_count])) {

                    $operand = $set['operand'][$opera_count];

                    $opera_count++;

                } else {
                    $operand = $set['operand'][$opera_count - 1];
                }

                if (isset($set['condition'][$cond_count])) {

                    $condition = $set['condition'][$cond_count];

                    $cond_count++;

                } else {

                    $condition = $set['condition'][$cond_count - 1];

                }

                if ($operand === 'IN' || $operand === 'NOT IN') {

                    if (is_string($item) && str_starts_with($item, 'SELECT')) { // Проверка на строку.
                        $in_str = $item;
                    } else {
                        if (is_array($item)) $temp_item = $item;
                        else $temp_item = explode(',', $item ?? '');

                        $in_str = '';

                        foreach ($temp_item as $value) {
                            $in_str .= "'" . addslashes(trim($value)) . "',";
                        }

                    }

                    $where .= $table . $key . ' ' . $operand . ' (' . trim($in_str, ',') . ') ' . $condition;

                } elseif (str_contains($operand, 'LIKE')) {

                    $like_template = explode('%', $operand);

                    foreach ($like_template as $lt_key => $lt) {
                        if (empty($lt)) {
                            if (empty($lt_key)) {
                                $item = '%' . $item;
                            } else {
                                $item .= '%';
                            }
                        }
                    }

                    $where .= $table . $key . ' LIKE ' . "'" . addslashes($item) . "' $condition";

                } else {

                    if (str_starts_with(isset($item), 'SELECT')) {

                        $where .= $table . $key . $operand . ' (' . $item . ") $condition";

                    } elseif ($item === null || $item === 'NULL'){

                        if ($operand === '=') $where .= $table . $key . ' IS NULL ' . $condition;
                            else $where .= $table . $key . ' IS NOT NULL ' . $condition;

                    } else {

                        $where .= $table . $key . $operand . "'" . addslashes($item) . "' $condition";

                    }

                }

            }

            $where = substr($where, 0, strrpos($where, $condition));

        }

        return $where;
    }

    /**
     * @throws DbException
     */
    protected function createJoin(array $set, string|bool $table, bool $new_where = false): array
    {
        $fields = '';
        $join = '';
        $where = '';

        if (isset($set['join'])) {

            $join_table = $table;

            foreach ($set['join'] as $key => $item) {

                if (is_int($key)) { // Проверка пуст ли 'table' в номерованном массиве.

                    if (empty($item['table'])) continue;
                    else $key = $item['table'];

                }

                $concatTable = $this->createTableAlias($key)['alias'];

                if (!empty($join)) $join .= ' ';

                if (isset($item['on']) && $item['on']) {

                    if (isset($item['on']['fields']) && is_array($item['on']['fields'])
                        && count($item['on']['fields']) === 2) {

                        $join_fields = $item['on']['fields'];

                    } elseif (is_array($item['on']) && count($item['on']) === 2) {

                        $join_fields = $item['on'];

                    } else {

                        continue;

                    }

                    if (empty($item['type'])) $join .= 'LEFT JOIN ';
                    else $join .= trim(strtoupper($item['type'])) . ' JOIN ';

                    $join .= $key . ' ON ';

                    $join_temp_table = $item['on']['table'] ?? $join_table;

                    $join .= $this->createTableAlias($join_temp_table)['alias'];

                    $join .= '.' . $join_fields[0] . '=' . $concatTable . '.' . $join_fields[1];

                    $join_table = $key;

                    // Если есть new_where, то отправляется инструкция с именем where в where, иначе с именем group_condition или and.
                    if ($new_where) {

                        if (isset($item['where'])) {
                            $new_where = false;
                        }

                        $group_condition = 'WHERE';

                    } else {

                        $group_condition = isset($item['group_condition']) ? strtoupper($item['group_condition']) : 'AND';

                    }

                    $fields .= $this->createFields($item, $key, $set['join_structure'] ?? false);
                    $where .= $this->createWhere($item, $key, $group_condition);

                }

            }

        }

        return compact('fields', 'join', 'where');

    }

    protected function createInsert(array|bool $fields, array|bool $files, array|bool $except): array
    {
        $insert_arr = [
            'fields' => '(',
            'values' => '',
        ];

        $array_type = array_keys($fields)[0];

        if (is_int($array_type)) {

            $check_fields = false;
            $count_fields = 0;

            foreach ($fields as $item) {

                $insert_arr['values'] .= '(';

                if (!$count_fields) $count_fields = count($item);

                $j = 0;

                // Защита от лишних полей. Невозможно добавить лишнее поле.
                foreach ($item as $row => $value) {

                    if (!empty($except) && in_array($row, $except)) continue;

                    // Проверка заполнены ли поля. $row - название поля
                    if (!$check_fields) $insert_arr['fields'] .= $row . ',';

                    if (!empty($this->sqlFunc) && in_array($value, $this->sqlFunc)) {

                        $insert_arr['values'] .= $value . ',';

                    } elseif ($value == 'NULL' || $value === NULL) { // Если пришёл null или string

                        $insert_arr['values'] .= "NULL" . ',';

                    } else {

                        $insert_arr['values'] .= "'" . addslashes($value) . "',";

                    }

                    $j++;

                    if ($j === $count_fields) break;

                }

                if ($j < $count_fields) {

                    for (; $j < $count_fields; $j++) {

                        $insert_arr['values'] .= "NULL" . ',';

                    }

                }

                // Обрезание лишний ',' в конце.
                $insert_arr['values'] = rtrim($insert_arr['values'], ',') . '),';

                if (!$check_fields) $check_fields = true;

            }

        } else {

            $insert_arr['values'] = '(';

            if ($fields) {

                // Защита от лишних полей. Невозможно добавить лишнее поле.
                foreach ($fields as $row => $value) {

                    if (!empty($except) && in_array($row, $except)) continue;

                    // Проверка заполнены ли поля. $row - название поля
                    $insert_arr['fields'] .= $row . ',';

                    if (!empty($this->sqlFunc) && in_array($value, $this->sqlFunc)) {

                        $insert_arr['values'] .= $value . ',';

                    } elseif ($value == 'NULL' || $value === NULL) { // Если пришёл null или string

                        $insert_arr['values'] .= "NULL" . ',';

                    } else {

                        $insert_arr['values'] .= "'" . addslashes($value) . "',";

                    }

                }

            }

            if ($files) {



                foreach ($files as $row => $file) {

                    // Проверка заполнены ли поля. $row - название поля
                    $insert_arr['fields'] .= $row . ',';

                    if (is_array($file)) $insert_arr['values'] .= "'" . addslashes(json_encode($file)) . "',";
                        else $insert_arr['values'] .= "'" . addslashes($file) . "',";

                }

            }

            $insert_arr['values'] = rtrim($insert_arr['values'], ',') . ')';

        }

        $insert_arr['fields'] = rtrim($insert_arr['fields'], ',') . ')';
        $insert_arr['values'] = rtrim($insert_arr['values'], ',');

        return $insert_arr;

    }

    protected function createUpdate(array|bool $fields, array|bool $files, array|bool $except): string
    {
        $update = '';

        if ($fields) {

            foreach ($fields as $row => $value) {

                if ($except && in_array($row, $except)) continue;

                $update .= $row . '=';

                if (in_array($value, $this->sqlFunc)) {
                    $update .= $value . ',';
                } elseif ($value === null || $value === 'NULL') {
                    $update .= "NULL" . ',';
                } else {
                    $update .= "'" . addslashes($value) . "',";
                }

            }
        }

        if ($files) {

            foreach ($files as $row => $file) {

                $update .= $row . '=';

                if (is_array($file)) $update .= "'" . addslashes(json_encode($file)) . "',";
                    else $update .= "'" . addslashes($file) . "',";

            }
        }

        return rtrim($update, ',');

    }

    protected function joinStructure(array $result, string $table): array
    {

        $joinArr = [];

        $id_row = $this->tableRows[$this->createTableAlias($table)['alias']]['id_row'];

        foreach ($result as $value) {

            if ($value) {

                if (!isset($joinArr[$value[$id_row]])) $joinArr[$value[$id_row]] = [];

                foreach ($value as $key => $item) {

                    if (preg_match('/TABLE(.+)?TABLE/u', $key, $matches)) {

                        $table_normal_name = $matches[1];

                        if (!isset($this->tableRows[$table_normal_name]['multi_id_row'])) {
                            // Сохранение первичного ключа из таблицы.
                            $join_id_row = $value[$matches[0] . '_' . $this->tableRows[$table_normal_name]['id_row']];

                        } else {

                            $join_id_row = '';

                            foreach ($this->tableRows[$table_normal_name]['multi_id_row'] as $multi) {

                                $join_id_row .= $value[$matches[0] . '_' . $multi];

                            }

                        }

                        $row = preg_replace('/TABLE(.+)TABLE_/u', '', $key);

                        if ($join_id_row &&
                            !isset($joinArr[$value[$id_row]]['join'][$table_normal_name][$join_id_row][$row])) {

                            $joinArr[$value[$id_row]]['join'][$table_normal_name][$join_id_row][$row] = $item;

                        }

                        continue;

                    }

                    $joinArr[$value[$id_row]][$key] = $item;

                }
            }
        }

        return $joinArr;

    }

    protected function createTableAlias(string $table): array
    {

        $arr = [];
        // Поиск пробела в названии таблицы
        if (preg_match('/\s+/i', $table)) {
            // Замена нескольких пробелов на один
            $table = preg_replace('/\s{2,}/i', ' ', $table);

            $table_name = explode(' ', $table);

            $arr['table'] = trim($table_name[0]);

            $arr['alias'] = trim($table_name[1]);

        } else {

            $arr['alias'] = $arr['table'] = $table;

        }

        return $arr;

    }

    protected function getTotalCount(string $table, string $where)
    {

        return $this->query("SELECT COUNT(*) as count FROM $table $where")[0]['count'];

    }

    public function getPagination(): bool|array
    {

        if (!$this->numberPages || $this->numberPages === 1 || $this->page > $this->numberPages) {

            return false;

        }

        $result = [];

        if ($this->page !== 1) {

            $result['first'] = 1;

            $result['back'] = $this->page - 1;

        }

        if ($this->page > $this->linksNumber + 1) {

            for ($i = $this->page - $this->linksNumber; $i < $this->page; $i++) {

                $result['previous'][] = $i;

            }

        } else {

            for ($i = 1; $i < $this->page; $i++) {

                $result['previous'][] = $i;

            }

        }

        $result['current'] = $this->page;

        if ($this->page + $this->linksNumber < $this->numberPages) {

            for ($i = $this->page + 1; $i <= $this->page + $this->linksNumber; $i++) {

                $result['next'][] = $i;

            }

        } else {

            for ($i = $this->page + 1; $i <= $this->numberPages; $i++) {

                $result['next'][] = $i;

            }

        }

        if ($this->page !== $this->numberPages) {

            $result['forward'] = $this->page + 1;

            $result['last'] = $this->numberPages;

        }

        return $result;

    }

}
