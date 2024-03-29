<?php

namespace core\base\models;

use core\base\exceptions\DbException;
use mysqli;

abstract class BaseModel extends BaseModelMethods
{

    protected mysqli $db;

    /**
     * @param string $query
     * @param string $crud = r - SELECT / c - INSERT / u - UPDATE / d - DELETE
     * @param bool $return_id
     * @return array|int|string|true|void
     * @throws DbException
     */
    final public function query(string $query, string $crud = 'r', bool $return_id = false)
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
                if ($return_id) return $this->db->insert_id;
                return true;

            default:
                return true;

        }

    }

    /**
     * @param string $table
     * @param array $set
     * 'fields' => ['id', 'name'],
     * 'no_concat' => false/true Если true не присоединять имя таблицы к полям и where
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

        $paginationWhere = $where = $this->createWhere($set, $table);

        if (empty($where)) $new_where = true;
        else $new_where = false;

        $join_arr = $this->createJoin($set, $table, $new_where); // Массив

        $fields .= $join_arr['fields'];

        $join = $join_arr['join'];

        $where .= $join_arr['where'];

        $fields = rtrim($fields, ',');

        $limit = isset($set['limit']) ? 'LIMIT ' . $set['limit'] : '';

        $this->createPagination($set, $paginationWhere, $limit, $table);

        $query = "SELECT $fields FROM $table $join $where $order $limit";

        // Возврат запроса
        if (!empty($set['return_query'])) return $query;

        $result = $this->query($query);

        if (isset($set['join_structure']) && $set['join_structure'] && isset($result)) {

            $result = $this->joinStructure($result, $table);

        }

        return $result;
    }

    protected function createPagination(array $set, string $where, string &$limit, string $table = ''): void
    {

        if (!empty($set['pagination'])) {

            $this->postNumber = isset($set['pagination']['qty']) ? (int)$set['pagination']['qty'] : QTY;

            $this->linksNumber = isset($set['pagination']['qty_links']) ? (int)$set['pagination']['qty_links'] : QTY_LINKS;

            $this->page = !is_array($set['pagination']) ? (int)$set['pagination'] : (int)($set['pagination']['page']) ?? 1;

            if ($this->page > 0 && $this->postNumber > 0) {

                $this->totalCount = $this->getTotalCount($table, $where);

                $this->numberPages = (int)ceil($this->totalCount / $this->postNumber);

                $limit = 'LIMIT ' . ($this->page - 1) * $this->postNumber . ',' . $this->postNumber;

            }

        }

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

        $set['return_id'] = !empty(['return_id']) && isset($set['return_id']);

        $set['except'] = (!empty($set['except']) && is_array($set['except'])) ? $set['except'] : false;

        $insert_arr = $this->createInsert($set['fields'], $set['files'], $set['except']);

        $query = "INSERT INTO $table {$insert_arr['fields']} VALUES {$insert_arr['values']}";

        return $this->query($query, 'c', $set['return_id']);

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
    final public function delete(string $table, array $set = []): int|bool|array|string|null
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
            $join_tables = $join_arr['tables'] ?? '';

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

        if (!isset($this->tableRows[$table]) || empty($this->tableRows[$table])) {

            $checkTable = $this->createTableAlias($table);

            // Проверка был ли уже сделан подобный запрос
            if (isset($this->tableRows[$checkTable['table']])) {

                // Создание ячейки с псевдонимом равной таблице без псевдонима ($checkTable['table'])
                return $this->tableRows[$checkTable['alias']] = $this->tableRows[$checkTable['table']];

            }

            $query = "SHOW COLUMNS FROM {$checkTable['table']}";

            $result = $this->query($query);

            $this->tableRows[$checkTable['table']] = [];

            if ($result) {

                foreach ($result as $row) {

                    $this->tableRows[$checkTable['table']][$row['Field']] = $row;

                    if ($row['Key'] === 'PRI') {

                        if (!isset($this->tableRows[$checkTable['table']]['id_row'])) {

                            $this->tableRows[$checkTable['table']]['id_row'] = $row['Field'];

                        } else {

                            if (!isset($this->tableRows[$checkTable['table']]['multi_id_row'])) {

                                $this->tableRows[$checkTable['table']]['multi_id_row'][] = $this->tableRows[$checkTable['table']]['id_row'];

                            }

                            $this->tableRows[$checkTable['table']]['multi_id_row'][] = $row['Field'];

                        }
                    }
                }
            }

        }

        if (isset($checkTable) && $checkTable['table'] !== $checkTable['alias']) {

            return $this->tableRows[$checkTable['alias']] = $this->tableRows[$checkTable['table']];

        }

        return $this->tableRows[$table];
    }

    /**
     * @throws DbException
     */
    final public function showTables() : array
    {

        $query = 'SHOW TABLES';

        $tables = $this->query($query);

        $tableArr = [];

        if (isset($tables)) {

            foreach ($tables as $table) {

                $tableArr[] = reset($table);

            }

        }

        return $tableArr;

    }

    /**
     * @throws DbException
     */
    public function buildUnion(string $table, array $set): static
    {

        if (array_key_exists('fields', $set) && $set['fields'] === null) return $this;

        if (!array_key_exists('fields', $set)) {

            $set['fields'] = [];

            $columns = $this->showColumns($table);

            unset($columns['id_row'], $columns['multi_id_row']);

            foreach ($columns as $row => $item) {

                $set['fields'][] = $row;

            }

        }

        $this->union[$table] = $set;

        $this->union[$table]['return_query'] = true;

        return $this;

    }

    /**
     * @throws DbException
     */
    public function getUnion(array $set = []): array|bool|int|string|null
    {

        if (!$this->union) return false;

        $unionType = ' UNION ' . (!empty($set['type']) ? strtoupper($set['type']) . ' ' : '');

        $maxCount = 0;

        $maxTableCount = '';

        foreach ($this->union as $key => $item) {

            $count = count($item['fields']);

            $joinFields = '';

            if (!empty($item['join'])) {

                foreach ($item['join'] as $table => $data) {

                    if (array_key_exists('fields', $data) && $data['fields']) {

                        $count += count($data['fields']);

                        $joinFields = $table;

                    } elseif (!array_key_exists('fields', $data) || (!$joinFields['data'])
                        || $data['fields'] === null) {

                        $columns = $this->showColumns($table);

                        unset($columns['id_row'], $columns['multi_id_row']);

                        $count += count($columns);

                        foreach ($columns as $field => $value) {

                            $this->union[$key]['join'][$table]['fields'][] = $field;

                        }

                        $joinFields = $table;

                    }

                }

            } else {

                $this->union[$key]['no_concat'] = true;

            }

            if ($count > $maxCount || ($count === $maxCount && $joinFields)) {

                $maxCount = $count;

                $maxTableCount = $key;

            }

            $this->union[$key]['lastJoinTable'] = $joinFields;
            $this->union[$key]['countFields'] = $count;

        }

        $query = '';

        if (isset($maxCount) && isset($maxTableCount)) {

            $query .= '(' . $this->read($maxTableCount, $this->union[$maxTableCount]) . ')';

            unset($this->union[$maxTableCount]);

        }

        foreach ($this->union as $key => $item) {

            if (isset($item['countFields']) && $item['countFields'] < $maxCount) {


                for ($i = 0; $i < $maxCount - $item['countFields']; $i++) {

                    if ($item['lastJoinTable']) {

                        $item['join'][$item['lastJoinTable']]['fields'][] = null;

                    } else {

                        $item['fields'][] = null;

                    }

                }

            }

            $query && $query .= $unionType;

            $query .= '(' . $this->read($key, $item) . ')';

        }

        $order = $this->createOrder($set);

        $limit = !empty($set['limit']) ? 'LIMIT' . $set['limit'] : '';

        if (method_exists($this, 'createPagination')) {

            $this->createPagination($set, "($query)", $limit);

        }

        $query .= " $order $limit";

        $this->union = [];

        return $this->query((trim($query)));

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
