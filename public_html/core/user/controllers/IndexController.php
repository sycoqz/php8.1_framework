<?php

namespace core\user\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
class IndexController extends BaseUser
{

    protected function inputData()
    {

        parent::inputData();

        $result = $this->img();

    }

}