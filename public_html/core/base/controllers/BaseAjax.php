<?php

namespace core\base\controllers;

use core\base\exceptions\DbException;
use core\base\settings\Settings;

class BaseAjax extends BaseController
{

    /**
     * @throws DbException
     */
    public function route(): void
    {

        $route = Settings::get('routes');

        $controller = $route['user']['path'] . 'AjaxController';

        $data = $this->isPost() ? $_POST : $_GET;

        if (isset($data['ADMIN_MODE'])) {

            unset($data['ADMIN_MODE']);

            $controller = $route['admin']['path'] . 'AjaxController';

        }

        $controller = str_replace('/', '\\', $controller);

        $ajax = new $controller;

        $ajax->createAjaxData($data);

        $ajax->ajax();

    }

    protected function createAjaxData(array $data): void
    {

        $this->data = $data;

    }

}