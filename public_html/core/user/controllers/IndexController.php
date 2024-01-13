<?php

namespace core\user\controllers;

class IndexController extends BaseUser
{

    protected function inputData()
    {

        parent::inputData();

        $alias = '';

        $res = $this->alias(['catalog' => 'auto', 'vendor' => 'chevrolet'], ['page' => 1, 'order' => 'desc']);

    }

}