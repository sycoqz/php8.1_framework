<?php

namespace core\base\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;

class BaseRoute
{

    use Singleton, BaseMethods;

    /**
     * @throws RouteException
     * @throws DbException
     */
    public static function routeDirection(): void
    {

        if (self::instance()->isAjax()) {

            exit((new BaseAjax())->route());

        }

        RouteController::instance()->route();

    }

}