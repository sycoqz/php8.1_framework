<?php

namespace core\base\models;

abstract class BaseModelMethods
{

    protected array $sqlFunc = ['NOW()'];

    protected function createFields(array $set, string|bool $table = false): string
    {
        if (array_key_exists('fields', $set) && $set['fields'] === null) return '';

        $set['fields'] = (!empty($set['fields']) && is_array($set['fields'])) ? $set['fields'] : ['*'];

        $table = $table . '.' ?? '';

        $fields = '';

        foreach ($set['fields'] as $field) {
            $fields .= $table . $field . ',';
        }

        return $fields;

    }

    protected function createOrder(array $set, string|bool $table = false): string
    {

        if (array_key_exists('order', $set) && $set['order'] === null) return '';

        $table = $table . '.' ?? '';

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
        $table = $table . '.' ?? '';

        $where = '';

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

    protected function createJoin(array $set, string|bool $table, bool $new_where = false): array
    {
        $fields = '';
        $join = '';
        $where = '';
        $tables = '';

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
                    $tables .= ', ' . trim($join_table);

                    // Если есть new_where, то отправляется инструкция с именем where в where, иначе с именем group_condition или and.
                    if ($new_where) {

                        if ($item['where']) {
                            $new_where = false;
                        }

                        $group_condition = 'WHERE';

                    } else {

                        $group_condition = isset($item['group_condition']) ? strtoupper($item['group_condition']) : 'AND';

                    }

                    $fields .= $this->createFields($item, $key);
                    $where .= $this->createWhere($item, $key, $group_condition);

                }

            }

        }

        return compact('fields', 'join', 'where', 'tables');

    }

    protected function createInsert(array|bool $fields, array|bool $files, array|bool $except): array
    {
        $insert_arr = [
            'fields' => '',
            'values' => '',
        ];

        if ($fields) {

            foreach ($fields as $row => $value) {

                if (!empty($except) && in_array($row, $except)) continue;

                $insert_arr['fields'] .= $row . ',';

                if (in_array($value, $this->sqlFunc)) {
                    $insert_arr['values'] .= $value . ',';
                } else {
                    $insert_arr['values'] .= "'" . addslashes($value) . "',";
                }

            }

        }

        if ($files) {

            foreach ($files as $row => $file) {

                $insert_arr['fields'] .= $row . ',';

                if (is_array($file)) $insert_arr['values'] .= "'" . addslashes(json_encode($file)) . "',";
                    else $insert_arr['values'] .= "'" . addslashes($file) . "',";

            }
        }

        foreach ($insert_arr as $key => $arr) $insert_arr[$key] = rtrim($arr, ',');

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

}
