<?php

namespace core\user\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
class IndexTestController extends BaseUser
{

    protected string $name;

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function inputData()
    {

        parent::inputData();

        echo $this->getController();

        exit();

    }

}