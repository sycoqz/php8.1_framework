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
    public function getGoods(array $set = [], array|null &$catalogFilters = null, array|null &$catalogPrices = null): void
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
                                    'return_query' => true
                                ])
                            ],
                            'operand' => ['IN'],
                        ]
                    ],
                ]);

            }
        }

    }

}