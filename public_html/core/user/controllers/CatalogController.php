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

        $operand = $this->checkFilters($where);

        $order = $this->createCatalogOrder($orderDb);

        $goods = $this->model->getGoods([
            'where' => $where,
            'operand' => $operand,
            'order' => $orderDb['order'],
            'order_direction' => $orderDb['order_direction']
        ], $catalogFilters, $catalogPrices);

        return compact('data', 'goods', 'catalogFilters', 'catalogPrices', 'order');

    }

    protected function checkFilters(array|null &$where): array
    {

        $dbWhere = [];

        $dbOperand = [];

        // Наличие минимальной цены
        if (isset($_GET['min_price'])) {

            $dbWhere['price'] = $this->clearNum($_GET['min_price']);

            $dbOperand[] = '>=';

        }

        if (isset($_GET['max_price'])) {

            $dbWhere[' price'] = $this->clearNum($_GET['max_price']);

            $dbOperand[] = '<=';

        }

        if (!empty($_GET['filters']) && is_array($_GET['filters'])) {

            // Подзапрос
            $subFiltersQuery = $this->setFilters();

            if ($subFiltersQuery) {

                // Формирование запроса
                $dbWhere['id'] = $subFiltersQuery;

                $dbOperand[] = 'IN';

            }

        }

        $where = array_merge($dbWhere, $where);

        $dbOperand[] = '=';

        return $dbOperand;

    }

    protected function setFilters(): string
    {
        // Обработка данных
        foreach ($_GET['filters'] as $key => $filterId) {

            $_GET['filters'][$key] = $this->clearNum($filterId);

            if (!$_GET['filters'][$key]) {

                unset($_GET['filters'][$key]);

                continue;

            }

            $other = array_search($_GET['filters'][$key], $_GET['filters']);

            if ($other !== false && $other !== $key) unset($_GET['filters'][$key]);

        }

        $result = $this->model->read('filters', [
            'where' => ['id' => 'SELECT DISTINCT parent_id FROM filters WHERE id IN(' . implode(',', $_GET['filters']) . ')'],
            'operand' => ['IN'],
            'join' => [
                'filters f_val' => [
                    'where' => ['id' => implode(',', $_GET['filters'])],
                    'operand' => ['IN'],
                    'fields' => ['id'],
                    'on' => ['id', 'parent_id']
                ]
            ],
            'join_structure' => true,
        ]);

        if ($result) {

            $arr = [];

            $counter = 0;

            foreach ($result as $item) {

                if (isset($item['join']['f_val'])) {

                    $arr[$counter] = array_column($item['join']['f_val'], 'id');

                    $counter++;

                }

            }

            $resultArr = $this->crossDiffArr($arr);

            if ($resultArr) {

                $queryStr = '';

                $filtersCount = 0;

                foreach ($resultArr as $key => $item) {

                    !$filtersCount && $filtersCount = count($item);

                    $queryStr .= ' filters_id IN(' . implode(',', $item) . ')' . (isset($resultArr[$key + 1]) ? ' OR ' : '');

                }

                return 'SELECT goods_id FROM goods_filters WHERE ' . $queryStr .
                    ' GROUP BY goods_id HAVING COUNT(goods_id) >= ' . $filtersCount;

            }

        }

        return '';

    }

    protected function crossDiffArr(array $arr, int $counter = 0): mixed
    {

        if (count($arr) === 1) {

            return array_chunk(array_shift($arr), 1);

        }

        if ($counter === count($arr) - 1) {

            return $arr[$counter];

        }

        $buffer = $this->crossDiffArr($arr, $counter + 1);

        $result = [];

        foreach ($arr[$counter] as $a) {

            foreach ($buffer as $b) {

                $result[] = is_array($b) ? array_merge([$a], $b) : [$a, $b];

            }

        }

        return $result;

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