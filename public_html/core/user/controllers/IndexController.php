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

        return compact('sales');

    }

}