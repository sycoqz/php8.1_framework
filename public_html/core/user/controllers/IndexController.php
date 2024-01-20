<?php

namespace core\user\controllers;

class IndexController extends BaseUser
{

    protected function inputData()
    {

        parent::inputData();

        $sales = $this->model->read('sales', [
            'where' => ['visibility' => 1],
            'order' => ['menu_position']
        ]);

        $advantages = $this->model->read('advantages', [
            'where' => ['visibility' => 1],
            'order' => ['menu_position'],
            'limit' => 6
        ]);

        $news = $this->model->read('news', [
            'where' => ['visibility' => 1],
            'order' => ['date'],
            'order_direction' => ['DESC'],
            'limit' => 3
        ]);

        $arrHits = [
            'hit' => [
                'name' => 'Хиты продаж',
                'icon' => '<svg><use xlink:href="' . PATH . TEMPLATE . 'assets/img/icons.svg#hit"></use></svg>'
            ],
            'hot' => [
                'name' => 'Горячие предложения',
                'icon' => '<svg><use xlink:href="' . PATH . TEMPLATE . 'assets/img/icons.svg#hot"></use></svg>'
            ],
            'sale' => [
                'name' => 'Распродажа',
                'icon' => '%'
            ],
            'new' => [
                'name' => 'Новинки',
                'icon' => 'new'
            ]
        ];

        $goods = [];

        foreach ($arrHits as $type => $item) {

            $goods[$type] = $this->model->getGoods([
                'where' => [$type  => 1, 'visibility' => 1],
                'limit' => 6,
            ]);

        }

        return compact('sales', 'arrHits', 'goods', 'advantages', 'news');

    }

}