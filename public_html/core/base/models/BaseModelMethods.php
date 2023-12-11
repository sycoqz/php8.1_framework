<?php

namespace core\base\models;

use core\base\exceptions\DbException;

abstract class BaseModelMethods
{

    protected array $sqlFunc = ['NOW()'];

    protected array $tableRows = [];

    /**
     * @throws DbException
     */
    protected function createFields(array $set, string|bool $table = false, bool $join = false): string
    {

        $fields = '';

        $join_structure = false;

        if ((isset($set['join_structure']) || $join) && $table) {

            $join_structure = true;

             $this->showColumns($table);

             if (isset($this->tableRows[$table]['multi_id_row'])) $set['fields'] = [];

        }

        $concat_table = ($table && !isset($set['no_concat'])) ? $table . '.' : '';

        if (empty($set['fields']) || !is_array($set['fields'])) {

            if (!$join) {

                $fields = $concat_table . '*,';

            } else {

                foreach ($this->tableRows[$table] as $key => $item) {

                    if ($key !== 'id_row' && $key !== 'multi_id_row') {

                        $fields .= $concat_table . $key . ' as TABLE' . $table . 'TABLE_' . $key . ',';

                    }
                    
                }
                
            }

        } else {

            $id_field = false;

            foreach ($set['fields'] as $field) {

                if ($join_structure && !$id_field && $this->tableRows[$table] === $field) {

                    $id_field = true;

                }

                if ($field) {

                    if ($join && $join_structure) {

                        if (preg_match('/^(.+)?\s+as\s+(.+)/i', $field, $matches)) {

                            $fields .= $concat_table . $matches[1] . ' as TABLE' . $table . 'TABLE_' . $matches[2] . ',';

                        } else {

                            $fields .= $concat_table . $field . ' as TABLE' . $table . 'TABLE_' . $field . ',';

                        }

                    } else {

                        $fields .= $concat_table . $field . ',';

                    }
                }
            }


            if (!$id_field && $join_structure) {

                if ($join) {

                    $fields .= $concat_table . $this->tableRows[$table]['id_row']
                        . ' as TABLE' . $table . 'TABLE_' . $this->tableRows[$table]['id_row'] . ',';

                } else {

                    $fields .= $concat_table . $this->tableRows[$table]['id_row'] . ',';

                }

            }

        }

        return $fields;

    }

    protected function createOrder(array $set, string|bool $table = false): string
    {

        if (array_key_exists('order', $set) && $set['order'] === null) return '';

        $table = ($table && !isset($set['no_concat'])) ? $table . '.' : '';

        $order_by = '';

        if (!empty($set['order']) && is_array($set['order'])) {

            $set['order_direction'] = (!empty($set['order_direction']) && is_array($set['order_direction']))
                ? $set['order_direction'] : ['ASC'];

            $order_by = 'ORDER BY ';

            $direct_count = 0;

            foreach ($set['order'] as $order) {

                if (isset($set['order_direction'][$direct_count])) {

                    $order_direction = strtoupper($set['order_direction'][$direct_count]);

                    $direct_count++;

                } else {

                    $order_direction = strtoupper($set['order_direction'][$direct_count-1]);

                }

                if (is_int($order)) $order_by .= $order . ' ' . $order_direction . ',';
                else $order_by .= $table . $order . ' ' . $order_direction . ',';

            }

            $order_by = rtrim($order_by, ',');

        }

        return $order_by;
    }

    protected function createWhere(array $set, string|bool $table = false, $instruction = 'WHERE'): string
    {
        $table = ($table && !isset($set['no_concat'])) ? $table . '.' : '';

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
                        else $temp_item = explode(',', $item);

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

                    if (str_starts_with($item, 'SELECT')) {

                        $where .= $table . $key . $operand . ' (' . $item . ") $condition";

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

                if (!empty($join)) $join .= ' ';

                if (isset($item['on'])) {

                    switch (2) {
                        case (isset($item['on']['fields']) && is_countable($item['on']['fields'])
                              && count($item['on']['fields'])):

                            $join_fields = $item['on']['fields'];
                            break;

                        case (is_countable($item['on']) && count($item['on'])):

                            $join_fields = $item['on'];
                            break;

                        default:
                            continue 2;
                    }

                    if (empty($item['type'])) $join .= 'LEFT JOIN ';
                    else $join .= trim(strtoupper($item['type'])) . ' JOIN ';

                    $join .= $key . ' ON ';

                    $join .= $item['on']['table'] ?? $join_table;

                    $join .= '.' . $join_fields[0] . '=' . $key . '.' . $join_fields[1];

                    $join_table = $key;

                    // Если есть new_where, то отправляется инструкция с именем where в where, иначе с именем group_condition или and.
                    if ($new_where) {

                        if ($item['where']) {
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
                } elseif ($value === null) {
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

        $id_row = $this->tableRows[$table]['id_row'];

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

}
