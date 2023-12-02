<?php

namespace core\base\models;

use core\base\exceptions\DbException;
use mysqli;

abstract class BaseModel extends BaseModelMethods
{

    protected mysqli $db;

    /**
     * @param $query
     * @param string $crud = r - SELECT / c - INSERT / u - UPDATE / d - DELETE
     * @param bool $return_id
     * @return array|int|string|true|void
     * @throws DbException
     */
    final public function query($query, string $crud = 'r', bool $return_id = false)
    {
        $result = $this->db->query($query); // Объект с выборкой из базы данных.

        if ($this->db->affected_rows === -1) {
            throw new DbException('Ошибка в SQL запросе: '
            . $query . ' - ' . $this->db->errno . $this->db->error);
        }

        switch ($crud) {

            case 'r':

                if ($result->num_rows) { // Если что-то пришло из базы данных.

                    $res = [];

                    for ($i = 0; $i < $result->num_rows; $i++) {
                        $res[] = $result->fetch_assoc(); // Возврат массива каждого ряда выборки.
                    }

                    return $res;

                }
                break;

            case 'c':
                if (isset($return_id)) return $this->db->insert_id;
                return true;

            default:
                return true;

        }

    }

    /**
     * @param string $table
     * @param array $set
     * 'fields' => ['id', 'name'],
     * 'where' => ['titan' => 'Attack Titan', 'name' => 'Eren', 'surname' => 'Yeager'],
     * 'operand' => ['=', '<>'],
     * 'condition' => ['AND'],
     * 'order' => ['titan', 'name'],
     * 'order_direction' => ['ASC', 'DESC'],
     * 'limit' => '1'
     * 'join' => [
     *     'join_table1' => [
     *        'table' => 'join_table1',
     *        'fields' => ['id as j_id', 'name as j_name'],
     *        'type' => 'left',
     *        'where' => ['username' => 'Eren'],
     *        'operand' => ['='],
     *        'condition' => ['OR'],
     *        'on' => [
     *            'table' => 'titans',
     *            'fields' => ['id', 'parent_id']
     *         ]
     *        'group_condition' => 'AND'
     *     ],
     *     'join_table2' => [
     *        'table' => 'join_table2',
     *        'fields' => ['id as j2_id', 'name as j2_name'],
     *        'type' => 'left',
     *        'where' => ['username' => 'sasha'],
     *        'operand' => ['<>'],
     *        'condition' => ['AND'],
     *        'on' => [
     *           'table' => 'titans',
     *           'fields' => ['id', 'parent_id']
     *        ]
     *      ]
     * ]
     * @return array|int|string|true|null
     * @throws DbException
     */

    final public function read(string $table, array $set = []): bool|array|int|string|null
    {
        $fields = $this->createFields($set, $table);

        $order = $this->createOrder($set, $table);

        $where = $this->createWhere($set, $table);

        if (empty($where)) $new_where = true;
                else $new_where = false;

        $join_arr = $this->createJoin($set, $table, $new_where); // Массив

        $fields .= $join_arr['fields'];
        $join = $join_arr['join'];
        $where .= $join_arr['where'];

        $fields = rtrim($fields, ',');

        $limit = isset($set['limit']) ? 'LIMIT ' . $set['limit'] : '';

        $query = "SELECT $fields FROM $table $join $where $order $limit";

        return $this->query($query);
    }

    /**
     * @param $table - таблица для вставки данных.
     * @param array $set - массив параметров:
     * Fields => [поле => значение]; Если не указан, то обрабатывается $_POST [поле => значение]
     * разрешена передача например NOW() в качестве Mysql функции обычно строкой.
     * Files => [поле => значение]; можно подать массив вида => [поле => [массив значений]]
     * except => ['исключение 1', 'исключение 2'] - исключает данные элементы массива из добавления в запрос
     * return_id => true|false - возвращать или нет идентификатор вставленной записи
     * @return mixed
     * @throws DbException
     */
    final public function create(string $table, array $set = []): mixed
    {

        $set['fields'] = (!empty($set['fields']) && is_array($set['fields'])) ? $set['fields'] : $_POST;

        $set['files'] = (!empty($set['files']) && is_array($set['files'])) ? $set['files'] : false;

        if (empty($set['fields']) && empty($set['files'])) return false;

        $set['return_id'] = !empty(['return_id']);

        $set['except'] = (!empty($set['except']) && is_array($set['except'])) ? $set['except'] : false;

        $insert_arr = $this->createInsert($set['fields'], $set['files'], $set['except']);

        if ($insert_arr) {

            $query = "INSERT INTO $table ({$insert_arr['fields']}) VALUES ({$insert_arr['values']})";

            return $this->query($query, 'c', $set['return_id']);
        }

        return false;

    }

    /**
     * @param string $table
     * @param array $set
     * @return array|bool|int|string|null
     * @throws DbException
     */
    final public function update(string $table, array $set = []): array|bool|int|string|null
    {

        $where = $this->createWhere($set, $table);

        $set['fields'] = (!empty($set['fields']) && is_array($set['fields'])) ? $set['fields'] : $_POST;

        $set['files'] = (!empty($set['files']) && is_array($set['files'])) ? $set['files'] : false;

        if (empty($set['fields']) && empty($set['files'])) return false;

        $set['except'] = (!empty($set['except']) && is_array($set['except'])) ? $set['except'] : false;

        if (!isset($set['all_rows'])) {

            if (isset($set['where'])) {

                $where = $this->createWhere($set);

            } else {

                $columns = $this->showColumns($table);

                if (!isset($columns)) return false;

                if (isset($columns['id_row']) && isset($set['fields'][$columns['id_row']])) {
                    $where = 'WHERE ' . $columns['id_row'] . '=' . $set['fields'][$columns['id_row']];
                    unset($set['fields'][$columns['id_row']]);
                }
            }
        }

        $update = $this->createUpdate($set['fields'], $set['files'], $set['except']);

        $query = "UPDATE $table SET $update $where";

        return $this->query($query, 'u');
    }

    /**
     * @param string $table
     * @param array $set
     * 'fields' => ['id', 'name'],
     * 'where' => ['titan' => 'Attack Titan', 'name' => 'Eren', 'surname' => 'Yeager'],
     * 'operand' => ['=', '<>'],
     * 'condition' => ['AND'],
     * 'join' => [
     *     'join_table1' => [
     *        'table' => 'join_table1',
     *        'fields' => ['id as j_id', 'name as j_name'],
     *        'type' => 'left',
     *        'where' => ['username' => 'Eren'],
     *        'operand' => ['='],
     *        'condition' => ['OR'],
     *        'on' => [
     *            'table' => 'titans',
     *            'fields' => ['id', 'parent_id']
     *         ]
     *        'group_condition' => 'AND'
     *     ],
     *     'join_table2' => [
     *        'table' => 'join_table2',
     *        'fields' => ['id as j2_id', 'name as j2_name'],
     *        'type' => 'left',
     *        'where' => ['username' => 'sasha'],
     *        'operand' => ['<>'],
     *        'condition' => ['AND'],
     *        'on' => [
     *           'table' => 'titans',
     *           'fields' => ['id', 'parent_id']
     *        ]
     *      ]
     * ]
     * @return array|int|string|true|null
     * @throws DbException
     */
    final public function delete(array $set,string $table): int|bool|array|string|null
    {
        $table = trim($table);

        $where = $this->createWhere($set, $table);

        $columns = $this->showColumns($table);

        if (!isset($columns)) return false;

        // Если пришли поля
        if (!empty($set['fields']) && is_array($set['fields'])) {

            if ($columns['id_row']) {

                $key = array_search($columns['id_row'], $set['fields']);
                if ($key !== false) unset($set['fields'][$key]);
            }

            $fields = [];

            foreach ($set['fields'] as $field) {

                $fields[$field] = $columns[$field]['Default'];
            }

            $update = $this->createUpdate($fields, false, false);

            $query = "UPDATE $table SET $update $where";

        } else {

            $join_arr = $this->createJoin($set, $table);
            $join = $join_arr['join'];
            $join_tables = $join_arr['tables'];

            $query = 'DELETE ' . $table . $join_tables . ' FROM ' . $table . ' ' . $join . ' ' . $where;

        }

        return $this->query($query, 'u');

    }

    /**
     * @param string $table
     * @return array
     * @throws DbException
     */
    final public function showColumns(string $table): array
    {
        $query = "SHOW COLUMNS FROM $table";

        $result = $this->query($query);

        $columns = [

        ];

        if ($result) {

            foreach ($result as $row) {
                $columns[$row['Field']] = $row;
                if ($row['Key'] === 'PRI') $columns['id_row'] = $row['Field'];
            }
            
        }

        return $columns;
    }

    /**
     * @throws DbException
     */
    protected function connect(): void
    {
        $this->db = @new mysqli(HOST,USER,PASS,DB_NAME);

        if ($this->db->connect_error) {
            throw new DbException('Ошибка подключения к базе данных: ' . $this->db->connect_error);

        }

        $this->db->query('SET NAMES UTF8');

    }

}
