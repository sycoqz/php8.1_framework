<?php

namespace core\admin\controllers;

use core\base\exceptions\DbException;
use core\base\settings\Settings;

class AddController extends BaseAdmin
{

    protected function inputData()
    {
        if (!isset($this->userID)) $this->executeBase();

        $this->createTableData();

        $this->createForeignData();

        $this->createRadio();

        $this->createOutputData();

    }

    protected function createForeignProperty(array $arr, array $rootItems): void
    {

        $where = '';
        $operand = [];

        if (in_array($this->table, $rootItems['tables'])) {
            $this->foreignData[$arr['COLUMN_NAME']][0]['id'] = 0;
            $this->foreignData[$arr['COLUMN_NAME']][0]['name'] = $rootItems['name'];
        }

        $columns = $this->model->showColumns($arr['REFERENCED_TABLE_NAME']);

        $name = '';

        if (isset($columns['name'])) {
            $name = 'name';
        } else {
            foreach ($columns as $key => $value) {

                if (str_contains($key, 'name')) {
                    $name = $key . ' as name';
                }
            }

            if (!isset($name)) $name = $columns['id_row'] . ' as name';

        }

        if (!empty($this->data)) {

            if ($arr['REFERENCED_TABLE_NAME'] === $this->table) {
                // Значение станет строкой в $where
                $where[$this->columns['id_row']] = $this->data[$this->columns['id_row']];
                $operand[] = '<>';

            }

        }

        $foreign = $this->model->read($arr['REFERENCED_TABLE_NAME'], [
            'fields' => [$arr['REFERENCED_COLUMN_NAME'] . ' as id', $name],
            'where' => $where,
            'operand' => $operand
        ]);

        if (isset($foreign)) {

            if (isset($this->foreignData[$arr['COLUMN_NAME']])) {

                foreach ($foreign as $value) {
                    $this->foreignData[$arr['COLUMN_NAME']][] = $value;
                }
            } else {
                $this->foreignData[$arr['COLUMN_NAME']] = $foreign;
            }

        }

    }

    /**
     * @throws DbException
     */
    protected function createForeignData(bool $settings = false): void
    {

        if (!$settings) $settings = Settings::instance();

        $rootItems = $settings::get('rootItems');

        $keys = $this->model->showForeignKeys($this->table);

        if (isset($keys)) {

            foreach ($keys as $item) {

                $this->createForeignProperty($item, $rootItems);

            }

        } elseif (isset($this->columns['parent_id'])) {

            $arr['COLUMN_NAME'] = 'parent_id';
            $arr['REFERENCED_COLUMN_NAME'] = $this->columns['id_row'];
            $arr['REFERENCED_TABLE_NAME'] = $this->table;

            $this->createForeignProperty($arr, $rootItems);

        }

    }
}