<?php

namespace core\admin\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\settings\Settings;

class ShowController extends BaseAdmin
{
    /**
     * @throws DbException
     */
    protected function inputData()
    {
        $this->executeBase();

        $this->createTableData();

        $this->createData();

        return $this->extension();

    }

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function outputData()
    {
        $args = func_get_arg(0);
        $vars = $args ?: [];

        if (!isset($this->template)) $this->template = ADMIN_TEMPLATE . 'show';

        $this->content = $this->render($this->template, $vars);

        return parent::outputData();
    }

    /**
     * @throws DbException
     */
    protected function createData(array $arr = [])
    {

        $fields = [];
        $order = [];
        $order_direction = [];

        if (!isset($this->columns['id_row'])) return $this->data = [];

        $fields[] = $this->columns['id_row'] . ' as id';

        if (isset($this->columns['name'])) $fields['name'] = 'name';

        if (isset($this->columns['img'])) $fields['img'] = 'img';

        if (count($fields) < 3) {
            foreach ($this->columns as $key => $item) {

                if (!isset($fields['name']) && str_contains($key, 'name')) {
                    $fields['name'] = $key . ' as name';
                }

                if (!isset($fields['img']) && str_contains($key, 'img')) {
                    $fields['img'] = $key . ' as img';
                }

            }
        }

        if (isset($arr['fields'])) {

            if (is_array($arr['fields'])) {
                $fields = Settings::instance()->arrayMergeRecursive($fields, $arr['fields']);
            } else {
                $fields[] = $arr['fields'];
            }

        }

        if (isset($this->columns['titan_id'])) {
            if (!in_array('titan_id', $fields)) $fields[] = 'titan_id';
            $order[] = 'titan_id';
        }

        if (isset($this->columns['menu_position'])) $order[] = 'menu_position';
        elseif (isset($this->columns['date'])) {
            if (isset($order)) $order_direction = ['ASC', 'DESC'];
            else $order_direction = 'DESC';

            $order[] = 'date';

        }

        if (isset($arr['order'])) {

            if (is_array($arr['order'])) {
                $order = Settings::instance()->arrayMergeRecursive($order, $arr['order']);
            } else {
                $order[] = $arr['order'];
            }

        }

        if (isset($arr['order_direction'])) {

            if (is_array($arr['order_direction'])) {
                $order_direction = Settings::instance()->arrayMergeRecursive($order_direction, $arr['order_direction']);
            } else {
                $order_direction[] = $arr['order_direction'];
            }

        }

        return $this->data = $this->model->read($this->table, [
            'fields' => $fields,
            'order' => $order,
            'order_direction' => $order_direction
        ]);

    }
}