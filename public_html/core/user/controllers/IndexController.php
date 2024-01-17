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

        $arrHits = ['hit', 'sale', 'new', 'hot'];

        $goods = [];

        foreach ($arrHits as $type) {

            $goods[$type] = $this->model->getGoods([
                'where' => [$type  => 1],
                'limit' => 6,
            ]);

        }

        return compact('sales');

    }

}