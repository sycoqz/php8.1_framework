<?php

namespace core\user\models;

use core\base\controllers\Singleton;
use core\base\exceptions\DbException;
use core\base\models\BaseModel;

class Model extends BaseModel
{

    use Singleton;

    /**
     * @throws DbException
     */
    public function getGoods(array $set = [], array|bool|null &$catalogFilters = null, array|bool|null &$catalogPrices = null): string|array|bool|int|null
    {

        if (empty($set['join_structure'])) {

            $set['join_structure'] = true;

        }

        if (empty($set['where'])) {

            $set['where'] = [];

        }

        // Сборка дефолтной сортировки товаров
        if (empty($set['order'])) {

            $set['order'] = [];

            if (!empty($this->showColumns('goods')['parent_id'])) {

                $set['order'][] = 'parent_id';

            }

            if (!empty($this->showColumns('goods')['price'])) {

                $set['order'][] = 'price';

            }

        }

        $goods = $this->read('goods', $set);

        // Если пришли товары
        if (isset($goods)) {

            if (!empty($this->showColumns('goods')['discount'])) {

                foreach ($goods as $key => $item) {

                    $this->applyDiscount($goods[$key], $item['discount']);

                }

            }

            unset($set['join'], $set['join_structure'], $set['pagination']);

            if ($catalogPrices !== false && !empty($this->showColumns('goods')['price'])) {

                $set['fields'] = ['MIN(price) as min_price', 'MAX(price) as max_price'];

                $catalogPrices = $this->read('goods', $set);

                if (!empty($catalogPrices[0])) {

                    $catalogPrices = $catalogPrices[0];

                }

            }



            if ($catalogFilters !== false && in_array('filters', $this->showTables())) {

                $parentFiltersFields = [];

                $filtersWhere = [];

                $filtersOrder = [];

                foreach ($this->showColumns('filters') as $name => $item) {

                    if (!empty($item) && is_array($item)) {

                        $parentFiltersFields[] = $name . ' as f_' . $name;

                    }

                }

                if (!empty($this->showColumns('filters')['visibility'])) {

                    $filtersWhere['visibility'] = 1;

                }

                if (!empty($this->showColumns('filters')['menu_position'])) {

                    $filtersOrder[] = 'menu_position';

                }

                $filters = $this->read('filters', [
                    'where' => $filtersWhere,
                    'join' => [
                        'filters f_name' => [
                            'type' => 'INNER',
                            'fields' => $parentFiltersFields,
                            'where' => $filtersWhere,
                            'on' => ['parent_id', 'id']
                        ],
                        'goods_filters' => [
                            'on' => [
                                'table' => 'filters',
                                'fields' => ['id', 'filters_id']
                            ],
                            'where' => [
                                'goods_id' => $this->read('goods', [
                                    'fields' => [$this->showColumns('goods')['id_row']],
                                    'where' => $set['where'] ?? null,
                                    'operand' => $set['operand'] ?? null,
                                    'return_query' => true
                                ])
                            ],
                            'operand' => ['IN'],
                        ]
                    ],
                ]);

                // Подсчёт товаров в каждом фильтре
                if ($filters) {

                    $filtersIds = implode(',', array_unique(array_column($filters, 'id')));

                    $goodsIds = implode(',', array_unique(array_column($filters, 'goods_id')));

                    $query = "SELECT filters_id as id, COUNT(goods_id) as count FROM goods_filters 
                                WHERE filters_id IN ($filtersIds) AND goods_id IN ($goodsIds) GROUP BY filters_id";

                    $goodsCountDb = $this->query($query);

                    $goodsCount = [];

                    if ($goodsCountDb) {

                        foreach ($goodsCountDb as $item) {

                            $goodsCount[$item['id']] = $item;

                        }

                    }

                    $catalogFilters = [];

                    foreach ($filters as $item) {

                        $parent = [];

                        $child = [];

                        foreach ($item as $row => $value) {

                            if (str_contains($row, 'f_')) {

                                $name = preg_replace('/^f_/', '', $row);

                                $parent[$name] = $value;

                            } else {

                                $child[$row] = $value;

                            }

                        }

                        if (isset($goodsCount[$child['id']]['count'])) {

                            $child['count'] = $goodsCount[$child['id']]['count'];

                        }

                        if (empty($catalogFilters[$parent['id']])) {

                            $catalogFilters[$parent['id']] = $parent;

                            $catalogFilters[$parent['id']]['values'] = [];

                        }

                        $catalogFilters[$parent['id']]['values'][$child['id']] = $child;

                        if (isset($goods[$item['goods_id']])) {

                            if (empty($goods[$item['goods_id']]['filters'][$parent['id']])) {

                                $goods[$item['goods_id']]['filters'][$parent['id']] = $parent;

                                $goods[$item['goods_id']]['filters'][$parent['id']]['values'] = [];

                            }

                            $goods[$item['goods_id']]['filters'][$parent['id']]['values'][$item['id']] = $child;

                        }

                    }

                }

            }

        }

        return $goods ?? null;

    }

    /**
     * @throws DbException
     */
    public function applyDiscount(array|null &$data, float|null $discount): void
    {

        if (!empty($this->showColumns('goods')['discount'])) {

            $data['old_price'] = null;

        }

        if ($discount) {

            $data['old_price'] = $data['price'] ?? null;

            $data['discount'] = $discount;

            $data['price'] = $data['old_price'] - ($data['old_price'] / 100 * $discount);

        }

    }

}