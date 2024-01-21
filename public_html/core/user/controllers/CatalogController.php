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

        $catalogFilters = $catalogPrices = $orderDb = null;

        $order = $this->createCatalogOrder($orderDb);

        $goods = $this->model->getGoods([
            'where' => $where,
            'order' => $orderDb['order'],
            'order_direction' => $orderDb['order_direction']
        ], $catalogFilters, $catalogPrices);

        return compact('data', 'goods', 'catalogFilters', 'catalogPrices', 'order');

    }

    protected function createCatalogOrder(array|null &$orderDb): array
    {

        $order = [
            'Цена' => 'price_asc',
            'Название' => 'name_asc'
        ];

        $orderDb = ['order' => null, 'order_direction' => null];

        if (!empty($_GET['order'])) {

            $orderArr = preg_split('/_+/', $_GET['order'], 0, PREG_SPLIT_NO_EMPTY);

            if (!empty($this->model->showColumns('goods')[$orderArr[0]])) {

                $orderDb['order'] = $orderArr[0];

                $orderDb['order_direction'] = $orderArr[1] ?? null;

                // Выбор сортировки
                foreach ($order as $key => $item) {

                    if (str_contains($item, $orderDb['order'])) {

                        $direction = $orderDb['order_direction'] === 'asc' ? 'desc' : 'asc';

                        $order[$key] = $orderDb['order'] . '_' . $direction;

                        break;

                    }

                }

            }

        }

        return $order;

    }

}