<?php

namespace core\user\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\user\controllers\BaseUser;

class CatalogController extends BaseUser
{

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function inputData()
    {
        parent::inputData();

        $order = [
            'price' => 'Цене',
            'name' => 'Названию',
        ];

        $data = [];

        if (!empty($this->parameters['alias'])) {

            $data = $this->model->read('catalog', [
                'where' => ['alias' => $this->parameters['alias'], 'visibility' => 1],
                'limit' => 1
            ]);

            if (!$data) {

                throw new RouteException('Не найдены записи в таблице catalog по ссылке - ' .
                    $this->parameters['alias']);

            }

            $data = $data[0];

        }

        // Формирование where
        $where = ['visibility' => 1];

        if ($data) {

            $where = ['parent_id' => $data['id']];

        } else {

            $data['name'] = 'Каталог';

        }

        $catalogFilters = $catalogPrices = null;

        $goods = $this->model->getGoods([
            'where' => $where
        ], $catalogFilters, $catalogPrices);

        return compact('data', 'goods', 'catalogFilters', 'catalogPrices');

    }

}