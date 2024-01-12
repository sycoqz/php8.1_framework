<?php

namespace core\user\controllers;

use core\admin\models\Model;
use core\base\controllers\BaseController;
use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\models\crypt;
use JetBrains\PhpStorm\NoReturn;

class IndexController extends BaseController
{

    protected string $name;

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function inputData(): bool|string
    {

        $this->init();

        $header = $this->render(TEMPLATE . 'header');

        $content = $this->render();

        $footer = $this->render(TEMPLATE . 'footer');

        return $this->render(TEMPLATE . 'layout', compact('header', 'content', 'footer'));

    }

}