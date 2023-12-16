<?php

namespace core\admin\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\settings\Settings;

class AddController extends BaseAdmin
{

    protected string $action = 'add';

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function inputData(): void
    {
        if (!isset($this->userID)) $this->executeBase();

        $this->checkPost();

        $this->createTableData();

        $this->createForeignData();

        $this->createMenuPosition();

        $this->createRadio();

        $this->createOutputData();

        $this->createManyToMany();

    }

    /**
     * @throws RouteException
     */
    protected function createForeignProperty(array $arr, array $rootItems): void
    {

        $where = '';
        $operand = [];

        if (in_array($this->table, $rootItems['tables'])) {
            $this->foreignData[$arr['COLUMN_NAME']][0]['id'] = 0;
            $this->foreignData[$arr['COLUMN_NAME']][0]['name'] = $rootItems['name'];
        }

        $orderData = $this->createOrderData($arr['REFERENCED_TABLE_NAME']);

        if (!empty($this->data)) {

            if ($arr['REFERENCED_TABLE_NAME'] === $this->table) {
                // Значение станет строкой в $where
                $where[$this->columns['id_row']] = $this->data[$this->columns['id_row']];
                $operand[] = '<>';

            }

        }

        $foreign = $this->model->read($arr['REFERENCED_TABLE_NAME'], [
            'fields' => [$arr['REFERENCED_COLUMN_NAME'] . ' as id', $orderData['name'], $orderData['parent_id']],
            'where' => $where,
            'operand' => $operand,
            'order' => $orderData['order']
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
     * @throws RouteException
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

    /**
     * @throws DbException
     */
    protected function createMenuPosition(bool $settings = false) : void
    {

        $where = '';

        if (isset($this->columns['menu_position'])) {

            if (!$settings) $settings = Settings::instance();
            $rootItems = $settings::get('rootItems');

            if (isset($this->columns['parent_id'])) {

                if (in_array($this->table, $rootItems['tables'])) {

                    $where = 'parent_id IS NULL OR parent_id = 0';

                } else {
                    // Запрос внешних ключей
                    $parent = $this->model->showForeignKeys($this->table, 'parent_id')[0];

                    if (isset($parent)) {

                        if ($this->table === $parent['REFERENCED_TABLE_NAME']) {

                            $where = 'parent_id IS NULL OR parent_id = 0';

                        } else {

                            $columns = $this->model->showColumns($parent['REFERENCED_TABLE_NAME']);

                            if (isset($columns['parent_id'])) $order[] = 'parent_id';
                            else $order[] = $parent['REFERENCED_COLUMN_NAME'];

                            $id = $this->model->read($parent['REFERENCED_TABLE_NAME'], [
                                'fields' => [$parent['REFERENCED_COLUMN_NAME']],
                                'order' => $order,
                                'limit' => '1'
                            ])[0][$parent['REFERENCED_COLUMN_NAME']];

                            if (isset($id)) $where = ['parent_id' => $id];

                        }

                    } else {

                        $where = 'parent_id IS NULL OR parent_id = 0';
                    }

                }

            }

            $menu_pos = $this->model->read($this->table, [
                'fields' => ['COUNT(*) as count'],
                'where' => $where,
                'no_concat' => true,
            ])[0]['count'] + 1;

            for ($i = 1; $i <= $menu_pos; $i++) {

                $this->foreignData['menu_position'][$i - 1]['id'] = $i;
                $this->foreignData['menu_position'][$i - 1]['name'] = $i;

            }

        }

        return;

    }

}